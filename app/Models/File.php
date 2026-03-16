<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    protected $fillable = ['file_hash', 'size_in_bytes'];

    public function userFiles(): HasMany
    {
        return $this->hasMany(UserFile::class);
    }
}
