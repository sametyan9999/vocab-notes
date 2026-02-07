<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Word extends Model
{
    protected $fillable = ['term', 'meaning', 'note'];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}