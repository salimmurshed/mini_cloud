<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // ... existing code

    protected $fillable = [
        'name',
        'email',
        'password',
        'total_used_storage', // Add this
    ];

    /**
     * Relationship to active files
     */
    public function activeFiles()
    {
        return $this->hasMany(UserFile::class)->whereNull('deleted_at');
    }
}
