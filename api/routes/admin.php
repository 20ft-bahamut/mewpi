<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\PluginsController;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->middleware('throttle:refresh');

// 보호 구간
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // 관리자 관리
    Route::prefix('admins')->group(function () {
        Route::get('/', [AdminsController::class, 'index']);                 // 목록 + 검색 + 페이지네이션
        Route::post('/', [AdminsController::class, 'store']);                // 생성
        Route::get('{id}', [AdminsController::class, 'show']);               // 단일 조회
        Route::patch('{id}/status', [AdminsController::class, 'updateStatus']); // 상태 변경
        Route::post('{id}/toggle', [AdminsController::class, 'toggle']);     // 상태 토글
        Route::patch('{id}/password', [AdminsController::class, 'resetPassword']); // 비밀번호 초기화
        Route::delete('{id}', [AdminsController::class, 'destroy']);         // 삭제
    });

    // 플러그인 관리
    Route::prefix('plugins')->group(function () {
        Route::get('/', [PluginsController::class, 'index']);          // 목록
        Route::post('/', [PluginsController::class, 'store']);         // 설치
        Route::get('{slug}', [PluginsController::class, 'show']);      // 상세
        Route::patch('{slug}', [PluginsController::class, 'update']);  // 수정
        Route::delete('{slug}', [PluginsController::class, 'destroy']); // 삭제
        Route::post('{slug}/enable', [PluginsController::class, 'enable']);   // 활성화
        Route::post('{slug}/disable', [PluginsController::class, 'disable']); // 비활성화
    });
});

// 헬스체크
Route::get('/ping', fn() => response()->json(['ok'=>true,'scope'=>'admin']));
