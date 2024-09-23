<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Deviation;
use App\DeviceDocument;
use DB;

class DownloadFile extends Controller
{
    public function download($device){
        $image = DeviceDocument::findOrFail($device);
            $pathToFile = storage_path('app/public/' .$image->url);
            return response()->download($pathToFile);
    }
    public function deviationFileDownload(Request $request){
        $id = isset($request->id)?$request->id:'';
        $file_name = isset($request->file_name)?$request->file_name:'';
        $image = Deviation::findOrFail($id);
            $pathToFile = storage_path('app/public/' .$file_name);
            return response()->download($pathToFile);
    }
}