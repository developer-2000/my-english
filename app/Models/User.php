<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
}
