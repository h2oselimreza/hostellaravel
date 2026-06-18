<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'username',
        'password',
        'user_group',
        'panel_type',
        'email',
        'user_type_code',
        'full_name',
        'contact_no',
        'is_reset',
        'created_type',
        'created_by',
        'updated_type',
        'updated_by',
        'created_dt_tm',
        'updated_dt_tm',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'user_group', 'id');
    }

    public function customerEmployee()
    {
        return $this->hasOne(CustomerEmployee::class, 'employee_id', 'user_id');
    }
}
