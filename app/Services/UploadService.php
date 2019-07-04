<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Check file exits
     * @param string    $url
     * @return bool
     */
    public static function checkFileExist($url)
    {
        if (Storage::exists($url)) {
            return true;
        }

        return false;
    }
}
