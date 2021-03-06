<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentence extends Model{
    use HasFactory;

    protected $guarded = [];

    public function sound() {
        return $this->hasOne(SentenceSound::class, 'sentence_id', 'id');
    }

}
