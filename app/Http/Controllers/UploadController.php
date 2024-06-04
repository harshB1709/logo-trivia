<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

class UploadController extends Controller
{
    public function upload(Request $request) {
        $location = null;
        if($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $location = Storage::disk('public')->putFileAs('wysiwyg', $file, uniqid() . '.' . $extension);
            $location = Storage::disk('public')->url($location);
        }

        return response()->json([
            'location' => $location
        ]);
    }
}
