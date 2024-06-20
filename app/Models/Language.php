<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'language',
    ];

    // Определение отношения "один к одному" с моделью User
    public function users()
    {
        return $this->belongsToMany(User::class, 'language_users');
    }
}
