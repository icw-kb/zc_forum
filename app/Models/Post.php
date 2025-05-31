<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;

class Post extends Model
{
    use HasFactory, Auditable, Searchable, SoftDeletes;

    protected $guarded = [];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
