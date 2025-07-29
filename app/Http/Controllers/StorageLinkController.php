<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StorageLinkController extends Controller
{
    public function create()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (file_exists($link)) {
            return response()->json(['message' => 'Link already exists']);
        }

        if (symlink($target, $link)) {
            return response()->json(['message' => 'Symlink created successfully']);
        }

        return response()->json(['message' => 'Failed to create symlink'], 500);
    }
}
