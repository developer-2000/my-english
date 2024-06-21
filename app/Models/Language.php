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

    protected $hidden = ['pivot'];

    // Связь с LanguageUser для interface_id
    public function interfaceLanguageUsers()
    {
        return $this->hasMany(LanguageUser::class, 'interface_id');
    }

    // Связь с LanguageUser для learn_id
    public function learnLanguageUsers()
    {
        return $this->hasMany(LanguageUser::class, 'learn_id');
    }
}
