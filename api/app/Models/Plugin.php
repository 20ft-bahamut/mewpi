<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $table = 'plugins';

    protected $fillable = [
        'slug',
        'name',
        'version',
        'enabled',
        'installed',
        'path',
        'repo_url',
        'author_name',
        'author_url',
        'license',
        'description',
        'manifest',
        'checksum',
        'requires_php',
        'requires_laravel',
        'requires_mewpi',
        'installed_at',
        'enabled_at',
        'disabled_at',
    ];

    protected $casts = [
        'enabled'   => 'boolean',
        'installed' => 'boolean',
        'manifest'  => 'array',
        'installed_at' => 'datetime',
        'enabled_at'   => 'datetime',
        'disabled_at'  => 'datetime',
    ];

    // 관계 정의
    public function versions()
    {
        return $this->hasMany(PluginVersion::class);
    }

    public function dependencies()
    {
        return $this->hasMany(PluginDependency::class, 'plugin_slug', 'slug');
    }

    // 검색/정렬 스코프
    public function scopeSearch($query, ?string $field, ?string $term)
    {
        if ($field && $term) {
            return $query->where($field, 'like', "%{$term}%");
        }
        return $query;
    }

    public function scopeSort($query, ?string $sortBy, ?string $direction = 'asc')
    {
        if ($sortBy) {
            return $query->orderBy($sortBy, $direction ?? 'asc');
        }
        return $query->orderBy('id', 'desc');
    }
}
