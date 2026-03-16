<?php

namespace App\Services;

use App\Models\File;
use App\Models\User;
use App\Models\UserFile;
use Illuminate\Support\Facades\DB;

class FileStorageService
{
    protected $limit = 524288000; // 500MB in bytes

    public function uploadFile(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $fileSize = $data['file_size'];

            // 1. Atomic Check & Increment (Prevents Race Conditions)
            $rowsAffected = DB::table('users')
                ->where('id', $user->id)
                ->whereRaw('total_used_storage + ? <= ?', [$fileSize, $this->limit])
                ->increment('total_used_storage', $fileSize);

            if ($rowsAffected === 0) {
                throw new \Exception("Storage limit of 500MB exceeded.");
            }

            // 2. Deduplication Logic (Bonus)
            $file = File::firstOrCreate(
                ['file_hash' => $data['file_hash']],
                ['size_in_bytes' => $fileSize]
            );

            // 3. Create User File Record
            return UserFile::create([
                'user_id' => $user->id,
                'file_id' => $file->id,
                'file_name' => $data['file_name'],
                'upload_time' => now(),
            ]);
        });
    }

    public function deleteFile(User $user, $fileId)
    {
        DB::transaction(function () use ($user, $fileId) {
            $userFile = UserFile::where('user_id', $user->id)->findOrFail($fileId);
            $fileSize = $userFile->file->size_in_bytes;

            // Mark as deleted and free up storage
            $userFile->delete();
            $user->decrement('total_used_storage', $fileSize);
        });
    }
}
