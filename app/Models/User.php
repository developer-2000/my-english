<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use \Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Чтобы автоматически загружать отношения
    protected $with = ['languageUser'];

    // вывести все роли
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // присвоение роли или ролей
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail()->id;
        }
        return $this->roles()->syncWithoutDetaching($role);
    }

    // проверка существования по названию
    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    // Определение отношения "один к одному" с моделью Language
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'language_users');
    }


    /**
     * Делает выборку языков пользователя через LanguageUser
     * @return HasOne
     */
    public function languageUser(): HasOne
    {
        return $this->hasOne(LanguageUser::class, 'user_id')
            ->with(['interfaceLanguage', 'learnLanguage']);
    }

    // Аксессор для получения данных о языке
    public function getLanguageUserDataAttribute()
    {
        $languageUser = $this->languageUser;

        if ($languageUser) {
            $languageUser->load('interfaceLanguage', 'learnLanguage');
        }

        return $languageUser;
    }

    /**
     * Автоматические функции таблицы
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->assignDefaultLanguages();
        });
    }

    /**
     * Назначает юзеру языки по умолчанию
     * @return void
     */
    public function assignDefaultLanguages()
    {
        // Получаем все языки
        $collect = Language::get();
        $interfaceLanguage = $collect->firstWhere('language', 'ru');
        $learnLanguage = $collect->firstWhere('language', 'en');

        if ($interfaceLanguage && $learnLanguage) {
            LanguageUser::create([
                "user_id" => $this->id,
                "learn_id" => $learnLanguage->id,
                "interface_id" => $interfaceLanguage->id,
            ]);
        }
    }
}
