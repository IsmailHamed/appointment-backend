<?php
/**
 * Created by PhpStorm.
 * User: IHamed
 * Date: 6/19/2020
 * Time: 5:44 PM
 */

namespace App\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait FileManagement
{
    protected function generate_name_to_file($data)
    {
        $imageName = "";
        if (!is_null($data) && !empty($data)) {
            $tempPath = $data->getRealPath();
            $extension = $data->getClientOriginalExtension();
            $hashFile = md5_file($tempPath);
            $imageName = $hashFile . "_" . date('YmdHisv') . "." . $extension;
        }
        return $imageName;
    }

    protected function upload_image($data, $baseDir = '')
    {
        $imageName = $this->generate_name_to_file($data);
        if (!empty($imageName)) {
            $disk = config('filesystems.default');
            $filesystem = Storage::disk($disk);
            $isExitsImage = $filesystem->exists($imageName);
            if (!$isExitsImage) {
                $filesystem->putFileAs($baseDir, $data, $imageName);
            }
        }
        return $imageName;
    }

    protected function check_request_and_upload_image($key, $baseDir = '')
    {
        $imageName = "";
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $imageName = $this->upload_image($image, $baseDir);
        }
        return $imageName;
    }

    protected function uploadImageAndAppendImageNameToRequest($baseDir, $key = 'image_name')
    {
        $request = request();
        $fileName = $this->check_request_and_upload_image('image', $baseDir);
        $isEmptyOrNull = $this->IsNullOrEmptyString($fileName);
        if (!$isEmptyOrNull) {
            $data = array_merge($request->except('image'), [$key => $fileName]);
        } else {
            $data = $request->all();
        }
        return $data;
    }
}
