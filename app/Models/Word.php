<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model {
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'time_forms' => 'json',
    ];

    public function type() {
        return $this->belongsTo(WordType::class, 'type_id', 'id')->withDefault(function ($type, $word) {
            $type->type = '';
            $type->color = 'black';
            $type->id = 0;
        });
    }
}
