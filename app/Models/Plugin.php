<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use Cviebrock\EloquentSluggable\Sluggable;

class Plugin extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory, Searchable, Auditable, Sluggable;

    protected $fillable = [
        'plugin_group_id',
        'name',
        'slug',
        'description',
        'github_url',
        'status',
        'view_count',
        'download_count',
        'featured',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'view_count' => 'integer',
        'download_count' => 'integer',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function versions()
    {
        return $this->hasMany(\App\Models\PluginVersion::class);
    }
}
