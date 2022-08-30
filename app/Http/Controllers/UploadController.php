<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function __construct()
    {

    }

    public function uploadFile(Request $request)
    {
        ini_set('upload_max_filesize', '20M');
        $file = $request->file('file');
        if (!isset($file)) {
            return null;
        }
        $size = \File::size($file);
        $destinationPath = public_path() . '/upload/files/';
        @mkdir(public_path() . '/upload/files', 0777);

        $tmp_filename = $request->file('file')->getClientOriginalName();
        $final_name = explode(".",$tmp_filename);

         $extension = $file->getClientOriginalExtension();
        if(isset($final_name) && isset($final_name[0])) {
            $filename = $final_name[0] . '_' . str_random(25) . '.' . $extension;
        }else{
            $filename = str_random(25) . '.' . $extension;
        }
        $upload_success = $request->file('file')->move($destinationPath, $filename);

        if ($upload_success) {
            return response()->json(['filename' => $filename, 'size' => $size, 'name' => $file->getClientOriginalName()]);
        } else {
            return 'YEP: Problem in file upload';
        }
    }

    public function multipleFileUpload(Request $request)
    {
        ini_set('upload_max_filesize', '20M');
        $files = $request->file('files');
        @mkdir(public_path() . '/upload/files', 0777);
        foreach ($files as $file) {
            $destinationPath = public_path() . '/upload/files/';
            $filename = $file->getClientOriginalName();
            $file->move($destinationPath, $filename);
        }
        return response()->json('success');
    }

    public function downloadimages($file)
    {
        $fileToDownload = public_path() . '/upload/' . $file;
        $ext = pathinfo($fileToDownload, PATHINFO_EXTENSION);
        return response()->download($fileToDownload, 'file.' . $ext);
    }

    public function downloadfile($file)
    {
        $fileToDownload = public_path() . '/upload/files/' . $file;
        //        $ext = pathinfo($fileToDownload, PATHINFO_EXTENSION);
        return response()->download($fileToDownload, $file);
    }
}
