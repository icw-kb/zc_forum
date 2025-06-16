<?php

namespace App\Models;

use App\Services\Traits\Restrictable;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class Forum extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable, HasFactory, Restrictable, Searchable, Sluggable, SoftDeletes;

    protected $guarded = [];

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    public function forumGroup(): BelongsTo
    {
        return $this->belongsTo(ForumGroup::class);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name', 'id'],
            ],
        ];
    }

    public function sluggableEvent(): string
    {
        return SluggableObserver::SAVED;
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }
}
