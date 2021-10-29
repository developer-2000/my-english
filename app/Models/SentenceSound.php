<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentenceSound extends Model {
    use HasFactory;

    protected $table = 'sentence_sounds';
    protected $guarded = [];

}
