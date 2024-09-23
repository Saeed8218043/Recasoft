<?php

namespace App\Http\Controllers;

use App\Deviation;
use App\Device;
use App\Company;
use App\Note;
use Illuminate\Http\Request;
use Redirect;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
 

    public function pdfDownload(Request $request, $id){ 
        $deviation =Deviation::where('id',$request->id)->first();
        $company_id = isset($deviation->company_id)?$deviation->company_id:'';
        $date    = isset($deviation->date)?$deviation->date:'';
        $name   = isset($deviation->name)?$deviation->name:'';
        $issue    = isset($deviation->issue)?$deviation->issue:'';
        $actions  = isset($deviation->actions)?$deviation->actions:'';
        $status   = isset($deviation->status)?$deviation->status:'';
        $files   = isset($deviation->files)?$deviation->files:'';
      
         $data =
         [
            'company_id' => $company_id,
            'date' => $date,
            'name' => $name,
            'issue' => $issue,
            'actions' => $actions,
            'status' => $status,
            'files' => $files,
         ];

    
         $pdf = pdf::loadView('pdf_download', $data);
         return $pdf->stream($name . '.pdf');
  //   $headers = [
  //     'Content-Type' => 'application/pdf',
  //     'Content-Disposition' => 'attachment; filename="' . $name . '.pdf"',
  //     'Content-Length' => strlen($pdf->output()),
  // ];
  // $headers['Accept-Ranges'] = 'bytes';

  // return response($pdf->output(), 200, $headers);
    }

    public function Note_pdfDownload(Request $request){ 
        $Sensor_notes =Note::where('id',$request->id)->first();
        $name   = isset($Sensor_notes->name)?$Sensor_notes->name:'';
        $notes    = isset($Sensor_notes->notes)?$Sensor_notes->notes:'';
        $device_id    = isset($Sensor_notes->device_id)?$Sensor_notes->device_id:'';
        $date    = isset($Sensor_notes->created_at)?$Sensor_notes->created_at:'';
        $device = Device::where('id',$device_id)->first();
        $device_name = !empty($device->name)?$device->name:$device->device_id;
         $data =
         [
            'company_name' => $request->company_name,
            'date' => $date,
            'name' => $name,
            'notes' => $notes,
            'device_name'=>$device_name

         ];
         //  $user =auth()->user()->name;
         //  $action ="Download";
         //  $message = "$user download note ($name) of device ($device_name) in $request->company_name";
         //  SystemLogs($message,$device->company_id,$action);
         $pdf = pdf::loadView('sensors.Note_pdf_download', $data);
         return $pdf->stream($name.'.pdf');

    }



}
