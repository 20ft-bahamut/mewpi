<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PluginVersion extends Model
{
    protected $table = 'plugin_versions';

    protected $fillable = [
        'plugin_id',
        'version',
        'manifest',
        'notes',
    ];

    protected $casts = [
        'manifest' => 'array',
    ];

    public function plugin()
    {
        return $this->belongsTo(Plugin::class);
    }
}
