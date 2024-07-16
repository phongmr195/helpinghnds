<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Exception;

trait ImageUpload
{

    /**
     * Handle upload image
     *
     * @param mixed $file
     * @param string $folder
     * @param mixed $fileExist
     *
     * @return string
     */
    public function saveImage($file, string $folder, $fileExist = '')
    {
        if ($file->isValid()) {
            if (!empty($fileExist) && file_exists($fileExist)) {
                try {
                    unlink($fileExist);
                } catch (Exception $e) {}
            }

            $pathThumbFolder = public_path('/uploads/thumb/');
            $pathImageFolder = public_path('/uploads/') . $folder;
            $fileName = $this->saveFile($file, $pathThumbFolder, $pathImageFolder);
            
            return $fileName;
        }
    }

    /**
     * Handle upload multiple images
     *
     * @param array $files
     * @param string $folder
     * @param mixed $fileExist
     *
     * @return array|string
     */
    public function saveImages($files, string $folder, $fileExist = '')
    {
        if(is_array($files)){
            $arrFiles = [];
            foreach($files as $key => $file){
                if ($file->isValid()) {
                    if (!empty($fileExist) && file_exists($fileExist)) {
                        try {
                            unlink($fileExist);
                        } catch (Exception $e) {}
                    }
                    $pathThumbFolder = public_path('/uploads/thumb/');
                    $pathImageFolder = public_path('/uploads/') . $folder;
                    $fileName = $this->saveFile($file, $pathThumbFolder, $pathImageFolder);
        
                    $arrFiles[$key] = [
                        'file_name' => $fileName,
                        'file_path' => asset('/uploads/' . $folder . $fileName)
                    ]; 
                }
            }
            return $arrFiles;
        }

        return 'Data is not array!';
    }

    /**
     * Handle save image and thumb image
     *
     * @param mixed $file
     * @param string $pathThumbFolder
     * @pram string $pathImageFolder
     *
     * @return string
     */
    public function saveFile($file, string $pathThumbFolder, string $pathImageFolder)
    {
        $fileName = str_replace(' ', '_', 'upload-' . time() . '-' . $file->getClientOriginalName());
        $image = Image::make($file->path());
        // Make folder if not exist
        if(!File::exists($pathThumbFolder)){
            File::makeDirectory($pathThumbFolder, 777, true, true);
        }
        if(!File::exists($pathImageFolder)){
            File::makeDirectory($pathImageFolder, 777, true, true);
        }
        $image->save($pathImageFolder.$fileName, 100);

        $image->resize(300, 300, function ($const) {
            $const->aspectRatio();
        })->save($pathThumbFolder.$fileName, 100);

        return $fileName;
    }
}