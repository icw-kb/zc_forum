<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use Cviebrock\EloquentSluggable\SluggableObserver;

class ForumGroup extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory, Auditable, Searchable, SoftDeletes, Sluggable;

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

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name', 'id']
            ]
        ];
    }
    public function sluggableEvent(): string
    {
        return SluggableObserver::SAVED;
    }

}
