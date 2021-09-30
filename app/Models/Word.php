<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model {
    use HasFactory;

    protected $guarded = [];

    public function type() {
        return $this->belongsTo(WordType::class, 'type', 'id')->withDefault(function ($type, $word) {
            $type->type = '';
            $type->color = 'black';
            $type->id = 0;
        });
    }
}
