<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PluginDependency extends Model
{
    protected $table = 'plugin_dependencies';

    protected $fillable = [
        'plugin_slug',
        'dep_slug',
        'constraint',
    ];

    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_slug', 'slug');
    }
}
