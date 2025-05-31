<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class Plugin extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory, Searchable, Auditable;

    public function versions()
    {
        return $this->hasMany(\App\Models\PluginVersion::class);
    }
}
