<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\RefreshRequest;
use App\Models\Admin;
use App\Models\MetaLog;
use App\Services\RefreshTokenService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // POST /api/admin/auth/login
    public function login(LoginRequest $r)
    {
        $data = $r->validated();

        $admin = Admin::where('email', $data['email'])->first();
        if (!$admin) {
            return ApiResponse::error('ADMIN_AUTH_EMAIL_NOT_FOUND','등록되지 않은 이메일입니다.',401);
        }
        if ($admin->status !== 'active') {
            return ApiResponse::error('ADMIN_AUTH_INACTIVE','비활성화된 계정입니다.',403);
        }
        if (!Hash::check($data['password'], $admin->password)) {
            MetaLog::recordAdmin('admin.login.failed','security',$admin->id ?? null,'Invalid password',['email'=>$data['email']],$r);
            return ApiResponse::error('ADMIN_AUTH_INVALID_PASSWORD','이메일 또는 비밀번호가 잘못되었습니다.',401);
        }

        // Access & Refresh
        $new = $admin->createToken('admin-access', ['admin:*']);
        $accessToken = $new->plainTextToken;
        $accessId    = $new->accessToken->id;

        $sessionId    = (string) Str::uuid();
        $refreshToken = RefreshTokenService::mint($admin->id, $r, null, [
            'session_id'      => $sessionId,
            'access_token_id' => $accessId,
        ]);

        $admin->forceFill(['last_login_at' => now()])->save();
        MetaLog::recordAdmin('admin.login.success','info',$admin->id,'Login success',[],$r);

        return ApiResponse::ok([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => [
                'id'     => $admin->id,
                'name'   => $admin->name,
                'email'  => $admin->email,
                'status' => $admin->status,
            ]
        ]);
    }

    // POST /api/admin/auth/refresh
    public function refresh(RefreshRequest $r)
    {
        $res = RefreshTokenService::rotate($r->validated()['refresh_token'], $r);
        if (!$res) {
            return ApiResponse::error('ADMIN_REFRESH_INVALID','리프레시 토큰이 유효하지 않습니다.',401);
        }

        $adminId = $res['admin_id'];
        $oldMeta = $res['old_meta'];
        $admin   = Admin::find($adminId);
        if (!$admin || $admin->status !== 'active') {
            return ApiResponse::error('ADMIN_REFRESH_FORBIDDEN','토큰은 유효하나 계정이 비활성/없습니다.',403);
        }

        // 1) 이전 access token 무효화
        if (!empty($oldMeta['access_token_id'])) {
            PersonalAccessToken::find($oldMeta['access_token_id'])?->delete();
        }

        // 2) 새 access token 발급
        $new = $admin->createToken('admin-access', ['admin:*']);
        $newAccess    = $new->plainTextToken;
        $newAccessId  = $new->accessToken->id;

        // 3) 새 refresh 메타에 access_token_id 업데이트
        $newRefresh = $res['refresh_token'];
        RefreshTokenService::updateMeta($newRefresh, [
            'access_token_id' => $newAccessId,
        ]);

        MetaLog::recordAdmin('admin.token.refreshed','info',$admin->id,'Access token refreshed',[],$r);

        return ApiResponse::ok([
            'access_token'  => $newAccess,
            'refresh_token' => $newRefresh,
        ]);
    }


    // GET /api/admin/me
    public function me(Request $r)
    {
        /** @var Admin $admin */
        $admin = $r->user();
        return response()->json([
            'id'            => $admin->id,
            'name'          => $admin->name,
            'email'         => $admin->email,
            'status'        => $admin->status,
            'last_login_at' => $admin->last_login_at,
        ]);
    }

    // POST /api/admin/auth/logout
    public function logout(Request $r)
    {
        $r->user()?->currentAccessToken()?->delete();

        $rt = (string)$r->input('refresh_token', '');
        if ($rt !== '') {
            RefreshTokenService::revoke($rt);
        }

        MetaLog::recordAdmin('admin.logout','info',$r->user()->id ?? null,'Logout',[],$r);

        return response()->noContent();
    }
}
