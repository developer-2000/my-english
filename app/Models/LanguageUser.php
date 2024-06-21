<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageUser extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'learn_id',
        'interface_id',
    ];

    // Связь с моделью User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Связь с моделью Language для interface_id
    public function interfaceLanguage()
    {
        return $this->belongsTo(Language::class, 'interface_id')
            ->withDefault([ 'language' => 'en' ]);
    }

    // Связь с моделью Language для learn_id
    public function learnLanguage()
    {
        return $this->belongsTo(Language::class, 'learn_id')
            ->withDefault([ 'language' => 'en' ]);
    }

    // Автообновление auth()->user() после изменений данных связанных с ним
    protected static function booted()
    {
        static::updated(function ($languageUser) {
            // После обновления LanguageUser обновляем данные пользователя
            $languageUser->user->refresh();
        });
    }

}
