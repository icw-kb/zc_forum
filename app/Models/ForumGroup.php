<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class ForumGroup extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory, Auditable, Searchable, SoftDeletes;

    protected $guarded = [];

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    public static function getGroupsForUser()
    {
        return ForumGroup::all();
    }
}
