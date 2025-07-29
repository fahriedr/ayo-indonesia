<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

trait Helpers {

    public function uploadFile($file, $path = 'team') {

        $file_decode = $this->revalidateBase64File($file);

        $full_path = $path . '/' . $file_decode['name'];

        $res = Storage::disk("public")->put($full_path, $file_decode['file']);

        return $full_path;
    }

    public function revalidateBase64File($file)
    {
        $image_64 = $file;
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

        // find substring fro replace here eg: data:image/png;base64,

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);

        $imageName = md5(microtime()).'.'.$extension;

        return [
            'file' => base64_decode($image),
            'name' => $imageName,
        ];
    }
}