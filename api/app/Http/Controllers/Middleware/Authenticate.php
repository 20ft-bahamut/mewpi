<?php

namespace App\Http\Controllers\Middleware;

use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;

class Authenticate extends BaseAuthenticate
{
    /**
     * 인증 실패 시 리다이렉트 목적지.
     * API 요청은 절대 리다이렉트하지 않도록 null 반환.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson() || $request->is('api*')) {
            return null;
        }

        // 웹 라우트에서만 필요하다면 활성화
        // return route('login');
        return null;
    }
}
