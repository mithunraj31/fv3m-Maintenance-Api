<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        //validating
        $validatedData = $request->validate([
            'image' => 'required|image',
        ]);

        // Start uploading s3
        $image  =  $request-> file('image');
        $path  =  Storage::disk( 's3' )->put('images' ,  $image ,  'public');

        return response(['imageUrl'=>$path,'uri'=>env('AWS_S3_URL').$path,'prefix'=>env('AWS_S3_URL')], 201);
    }
}