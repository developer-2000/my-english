<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoSentence extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sound()
    {
        return $this->hasOne(RoSentenceSound::class, 'sentence_id', 'id');
    }
}
