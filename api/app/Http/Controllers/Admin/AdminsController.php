<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminsIndexRequest;
use App\Http\Requests\Admin\AdminsStoreRequest;
use App\Http\Requests\Admin\AdminsUpdateStatusRequest;
use App\Http\Requests\Admin\AdminsResetPasswordRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use App\Models\MetaLog;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminsController extends Controller
{
    // GET /api/admin/admins
    public function index(AdminsIndexRequest $r)
    {
        $v = $r->validated();
        $perPage = (int)($v['per_page'] ?? 20);

        $paginator = Admin::query()
            ->summary()
            ->search($v['search_field'] ?? null, $v['search_term'] ?? null)
            ->sortBy($v['sort_by'] ?? 'id', $v['direction'] ?? 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::ok([
            'data' => \App\Http\Resources\Admin\AdminResource::collection($paginator)->resolve(),
            'meta' => [
                'page'      => $paginator->currentPage(),
                'per_page'  => $paginator->perPage(),
                'total'     => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }


    // GET /api/admin/admins/{id}
    public function show(int $id)
    {
        $admin = Admin::query()->summary()->find($id);
        if (!$admin) {
            return ApiResponse::error('ADMIN_NOT_FOUND', '관리자를 찾을 수 없습니다.', 404);
        }
        return ApiResponse::ok(AdminResource::make($admin)->resolve());
    }

    // POST /api/admin/admins
    public function store(AdminsStoreRequest $r)
    {
        $data = $r->validated();

        $admin = new Admin();
        $admin->name     = $data['name'];
        $admin->email    = $data['email'];
        $admin->password = Hash::make($data['password']);
        $admin->status   = $data['status'] ?? 'active';
        $admin->save();

        MetaLog::recordAdmin(
            action: 'admin.created',
            level: 'info',
            adminId: $r->user()->id ?? null,
            message: 'Admin created',
            context: ['target_admin_id' => $admin->id, 'email' => $admin->email],
            request: $r
        );

        return ApiResponse::ok(AdminResource::make($admin)->resolve(), 'created', 201);
    }

    // PATCH /api/admin/admins/{id}/status
    public function updateStatus(AdminsUpdateStatusRequest $r, int $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return ApiResponse::error('ADMIN_NOT_FOUND', '관리자를 찾을 수 없습니다.', 404);
        }

        $admin->status = $r->validated()['status'];
        $admin->save();

        MetaLog::recordAdmin(
            action: 'admin.status.updated',
            level: 'info',
            adminId: $r->user()->id ?? null,
            message: 'Admin status changed',
            context: ['target_admin_id' => $admin->id, 'status' => $admin->status],
            request: $r
        );

        return ApiResponse::ok([
            'id'     => $admin->id,
            'status' => $admin->status,
        ]);
    }

    // POST /api/admin/admins/{id}/toggle
    public function toggle(Request $r, int $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return ApiResponse::error('ADMIN_NOT_FOUND', '관리자를 찾을 수 없습니다.', 404);
        }

        $admin->status = $admin->status === 'active' ? 'inactive' : 'active';
        $admin->save();

        MetaLog::recordAdmin(
            action: 'admin.status.toggled',
            level: 'info',
            adminId: $r->user()->id ?? null,
            message: 'Admin status toggled',
            context: ['target_admin_id' => $admin->id, 'status' => $admin->status],
            request: $r
        );

        return ApiResponse::ok([
            'id'     => $admin->id,
            'status' => $admin->status,
        ]);
    }

    // PATCH /api/admin/admins/{id}/password
    public function resetPassword(AdminsResetPasswordRequest $r, int $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return ApiResponse::error('ADMIN_NOT_FOUND', '관리자를 찾을 수 없습니다.', 404);
        }

        $admin->password = Hash::make($r->validated()['password']);
        $admin->save();

        MetaLog::recordAdmin(
            action: 'admin.password.reset',
            level: 'warning',
            adminId: $r->user()->id ?? null,
            message: 'Admin password reset',
            context: ['target_admin_id'=>$admin->id, 'email'=>$admin->email],
            request: $r
        );

        return ApiResponse::ok(['id'=>$admin->id, 'message'=>'비밀번호가 초기화되었습니다.']);
    }

    // DELETE /api/admin/admins/{id}
    public function destroy(Request $r, int $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return ApiResponse::error('ADMIN_NOT_FOUND', '관리자를 찾을 수 없습니다.', 404);
        }

        $email = $admin->email;
        DB::transaction(function () use ($admin, $r, $email, $id) {
            $admin->delete();

            MetaLog::recordAdmin(
                action: 'admin.deleted',
                level: 'warning',
                adminId: $r->user()->id ?? null,
                message: 'Admin deleted',
                context: ['target_admin_id' => $id, 'email' => $email],
                request: $r
            );
        });

        return ApiResponse::ok(['id'=>$id, 'message'=>'관리자가 삭제되었습니다.']);
    }
}
