<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZencartVersion extends Model
{
    public function pluginVersions()
    {
        return $this->belongsToMany(PluginVersion::class);
    }
}
