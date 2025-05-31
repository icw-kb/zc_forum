<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class PluginVersion extends Model
{
    use HasFactory, Searchable, Auditable;

    public function plugin()
    {
        return $this->belongsTo(\App\Models\Plugin::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function compatibleZenCartVersions()
    {
        return $this->belongsToMany(ZencartVersion::class);
    }
}
