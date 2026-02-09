<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wordbook extends Model
{
    protected $fillable = ['name'];

    public function words()
    {
        return $this->hasMany(Word::class);
    }
}