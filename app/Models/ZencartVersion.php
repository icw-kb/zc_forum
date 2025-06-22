<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZencartVersion extends Model
{
    use HasFactory;
    
    protected $fillable = ['version'];

    public function pluginVersions()
    {
        return $this->belongsToMany(PluginVersion::class);
    }
}
