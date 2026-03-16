<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFile;
use App\Services\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    protected $storageService;

    public function __construct(FileStorageService $service)
    {
        $this->storageService = $service;
    }

    // POST /users/{user_id}/files
    public function upload(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Validate that an actual file is present
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:512000', // max 500MB (in KB)
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $uploadedFile = $request->file('file');

            // Auto-calculate metadata from the file object
            $data = [
                'file_name' => $uploadedFile->getClientOriginalName(),
                'file_size' => $uploadedFile->getSize(), // Automatically gets bytes
                'file_hash' => hash_file('sha256', $uploadedFile->getRealPath()), // Auto hash
            ];

            $fileRecord = $this->storageService->uploadFile($user, $data);

            return response()->json([
                'message' => 'File uploaded successfully',
                'data' => $fileRecord->load('file')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // DELETE /users/{user_id}/files/{file_id}
    public function delete($userId, $fileId)
    {
        $user = User::findOrFail($userId);

        try {
            $this->storageService->deleteFile($user, $fileId);
            return response()->json(['message' => 'File deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'File not found or already deleted'], 404);
        }
    }

    // GET /users/{user_id}/storage-summary
    public function summary($userId)
    {
        $user = User::findOrFail($userId);
        $limit = 524288000; // 500MB

        return response()->json([
            'total_storage_used' => $user->total_used_storage,
            'remaining_storage' => max(0, $limit - $user->total_used_storage),
            'total_active_files' => $user->activeFiles()->count(),
            'storage_limit' => $limit
        ]);
    }

    // GET /users/{user_id}/files
    public function list($userId)
    {
        $user = User::findOrFail($userId);

        $files = $user->activeFiles()
            ->with('file:id,size_in_bytes') // Get size from the deduped table
            ->get(['id', 'file_id', 'file_name', 'upload_time']);

        return response()->json($files);
    }
}
