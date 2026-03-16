<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'file_id',
        'file_name',
        'upload_time'
    ];

    // Dates that should be treated as Carbon instances
    protected $casts = [
        'upload_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
