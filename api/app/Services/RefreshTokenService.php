<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class RefreshTokenService
{
    private const NS  = 'rt:admin:';          // rt:admin:{rawToken} -> JSON(meta)
    private const TTL = 60 * 60 * 24 * 7;     // 7일

    public static function mint(int $adminId, ?Request $req = null, ?int $ttl = null, array $extraMeta = []): string
    {
        $token = Str::random(64);
        $meta = array_merge([
            'admin_id'   => $adminId,
            'ip'         => $req?->ip(),
            'user_agent' => $req?->userAgent() ? mb_substr((string)$req->userAgent(), 0, 255) : null,
            'issued_at'  => now()->toISOString(),
            // 선택 메타: session_id, access_token_id ...
        ], $extraMeta);

        Redis::setex(self::NS.$token, $ttl ?? self::TTL, json_encode($meta, JSON_UNESCAPED_UNICODE));
        return $token;
    }

    public static function get(string $token): ?array
    {
        $raw = Redis::get(self::NS.$token);
        return $raw ? json_decode($raw, true) : null;
    }

    public static function rotate(string $oldToken, ?Request $req = null, ?int $ttl = null): ?array
    {
        $oldMeta = self::get($oldToken);
        if (!$oldMeta) return null;

        // 이전 refresh 폐기
        Redis::del(self::NS.$oldToken);

        // 새 refresh 발급 (session_id 유지)
        $newToken = self::mint((int)$oldMeta['admin_id'], $req, $ttl, [
            'session_id' => $oldMeta['session_id'] ?? null,
            // access_token_id는 나중에 컨트롤러에서 새로 채워 넣음
        ]);

        return [
            'admin_id'      => (int)$oldMeta['admin_id'],
            'old_meta'      => $oldMeta,
            'refresh_token' => $newToken,
        ];
    }

    public static function updateMeta(string $token, array $merge): void
    {
        $meta = self::get($token);
        if (!$meta) return;
        $new = array_merge($meta, $merge);
        Redis::setex(self::NS.$token, Redis::ttl(self::NS.$token) ?: self::TTL, json_encode($new, JSON_UNESCAPED_UNICODE));
    }

    public static function revoke(string $token): void
    {
        Redis::del(self::NS.$token);
    }

    public static function revokeAllForAdmin(int $adminId): int
    {
        $deleted = 0;
        $it = NULL;
        // rt:admin:* 스캔 (대량 키에서 KEYS는 금지, SCAN 사용)
        while ($keys = Redis::scan($it, self::NS.'*', 1000)) {
            foreach ($keys as $key) {
                $raw = Redis::get($key);
                if (!$raw) continue;
                $meta = json_decode($raw, true);
                if (($meta['admin_id'] ?? null) === $adminId) {
                    Redis::del($key);
                    $deleted++;
                }
            }
            if ($it === 0) break; // 끝
        }
        return $deleted;
    }

}
