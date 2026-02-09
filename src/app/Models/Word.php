<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Word extends Model
{
    protected $fillable = [
        'term',
        'reading',   // ← 読み方を追加
        'meaning',
        'note',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function wordbook()
    {
    return $this->belongsTo(Wordbook::class);
    }
}