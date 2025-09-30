<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'admin';
    protected $fillable = ['name','email','password','status','last_login_at'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['last_login_at' => 'datetime'];

    /** 목록 기본 select */
    public function scopeSummary(Builder $q): Builder
    {
        return $q->select(['id','name','email','status','last_login_at','created_at']);
    }

    /** 검색: search_field + search_term */
    public function scopeSearch(Builder $q, ?string $field, ?string $term): Builder
    {
        $field = trim((string)$field);
        $term  = trim((string)$term);
        if ($field === '' || $term === '') return $q;

        if ($field === 'id') {
            return ctype_digit($term) ? $q->where('id', (int)$term) : $q;
        } elseif ($field === 'name') {
            return $q->where('name', 'like', "%{$term}%");
        } elseif ($field === 'email') {
            return $q->where('email', 'like', "%{$term}%");
        } elseif ($field === 'status') {
            return in_array($term, ['active','inactive'], true) ? $q->where('status', $term) : $q;
        }
        return $q; // 허용 이외 필드는 무시
    }

    /** 정렬: sort_by + direction */
    public function scopeSortBy(Builder $q, ?string $by, ?string $direction = 'desc'): Builder
    {
        $dir = strtolower((string)$direction) === 'asc' ? 'asc' : 'desc';

        if ($by === 'status') {
            return $q->orderBy('status', $dir);
        } elseif ($by === 'last_login_at') {
            return $q->orderBy('last_login_at', $dir);
        } elseif ($by === 'created_at') {
            return $q->orderBy('created_at', $dir);
        } elseif ($by === 'email') {
            return $q->orderBy('email', $dir);
        } elseif ($by === 'name') {
            return $q->orderBy('name', $dir);
        }
        return $q->orderBy('id', $dir); // 기본 id
    }
}
