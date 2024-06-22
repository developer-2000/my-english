<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnSentence extends Model{
    use HasFactory;

    protected $guarded = [];

    public function sound() {
        return $this->hasOne(EnSentenceSound::class, 'sentence_id', 'id');
    }

}
