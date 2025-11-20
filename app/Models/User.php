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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'is_active',
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

    // Relasi ke student
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id'); // Pastikan 'user_id' sesuai dengan kolom foreign key
    }

    // Relasi ke student
    public function mentor()
    {
        return $this->hasOne(Mentor::class, 'user_id'); // Pastikan 'user_id' sesuai dengan kolom foreign key
    }

    // Relasi ke student
    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id'); // Pastikan 'user_id' sesuai dengan kolom foreign key
    }

    // Definisikan relasi dengan model Session
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
