<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MetaLog extends Model
{
    protected $table = 'meta_logs';

    protected $fillable = [
        'channel', 'level', 'actor_admin_id',
        'action', 'message',
        'entity_type', 'entity_id',
        'ip', 'user_agent',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    /**
     * 기본 기록 메서드 (요청 메타 자동 주입)
     */
    public static function record(array $attrs, ?Request $request = null): self
    {
        if ($request) {
            $attrs['ip'] = $attrs['ip'] ?? $request->ip();
            $ua = (string) $request->userAgent();
            $attrs['user_agent'] = $attrs['user_agent'] ?? mb_substr($ua, 0, 255);
        }
        // context는 배열/스칼라 모두 허용
        if (isset($attrs['context']) && !is_array($attrs['context'])) {
            $attrs['context'] = ['value' => $attrs['context']];
        }
        return static::create($attrs);
    }

    /**
     * 관리자 액션 전용 헬퍼
     */
    public static function recordAdmin(
        string $action,
        string $level,
        ?int $adminId,
        string $message,
        array $context = [],
        ?Request $request = null,
        ?string $entityType = null,
        ?int $entityId = null
    ): self {
        return static::record([
            'channel'        => 'admin',
            'level'          => $level,                 // 'info' | 'warning' | 'error' | 'security' …
            'actor_id'       => $adminId,
            'action'         => $action,                // e.g. 'admin.login.success'
            'message'        => $message,
            'entity_type'    => $entityType,
            'entity_id'      => $entityId,
            'context'        => $context,
        ], $request);
    }
}
