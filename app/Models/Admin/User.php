<?php

namespace App\Models\Admin;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use SoftDeletes;
    use Notifiable;

    protected $fillable = [
        'name', 'login', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles(){
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function isAdministrator(){
        return $this->roles()->where('name', 'admin')->exists();
    }

    public function isUser(){
        $user = $this->roles()->where('name', 'user')->exists();
        if ($user) return "user";
    }

    public function isDisabled(){
        $disabled = $this->roles()->where('name', 'disabled')->exists();
        if ($disabled) return "disabled";
    }
}
