<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Deviation;
use App\Company;
use Illuminate\Http\Request;
use DB;
use Log;
use Illuminate\Support\Facades\Validator;

class DeviationController extends Controller
{
    public function index(Request $request)
    {

        $company_id = isset($request->company_id)?$request->company_id:'';
        $deviations_data = Deviation::where('company_id',$company_id)->get();
        $company = Company::where('company_id',$company_id)->first();
        $company_name= $company->name;
        return view('deviations.index',compact('company_id','company_name','deviations_data'));
    }
    public function store(Request $request){
        // dd($request->all());

        $company_id = isset($request->company_id)?$request->company_id:'';
        $date = isset($request->date)?$request->date:'';
        $name = isset($request->name)?$request->name:'';
        $issue = isset($request->issue)?$request->issue:'';
        $actions = isset($request->actions)?$request->actions:'';
        $status = isset($request->status)?$request->status:'';
        $files = $request->file('files');

        $validator = Validator::make($request->all(), [
            'files.*' => 'required|mimes:jpg,jpeg,png,gif,pdf,zip,rar',
        ]);
         
         if ($validator->fails()) {
            return back()->with('message','Your file format is not allowed to upload or file is too large must be of less than 20mb.');  
        }else{

            $row = Deviation::create([
                'name'=>$name,
                'issue'=>$issue,
                'company_id'=>$company_id,
                'date'=>$date,
                'status'=>$status,
                'actions'=>$actions,
            ]);
    
            $fname=[];
            if(isset($files) && $files!=null){
            foreach($files as $file){
                $fileName = $file->getClientOriginalName();
                $file->move(base_path() . '/storage/app/public', $fileName);
                $fname[]=$fileName;
            }
            $allFiles = implode(",",$fname);

            $add_filename =Deviation::where('id',$row->id)->first();
            $add_filename->update([
                'files'=>$allFiles
            ]);
        }
        $user =auth()->user()->name;
        $action ="Create";
        $company = Company::where('company_id',$company_id)->first();
        $message = "$user created deviation ($name) in $company->name";
        SystemLogs($message,$company_id,$action);
    
            return back();
        }

    }

    public function destroy($id){
       $deviation = Deviation::where('id',$id)->first();
       $user =auth()->user()->name;
       $action ="Delete";
       $company = Company::where('company_id',$deviation->company_id)->first();
       $message = "$user deleted deviation ($deviation->name) in $company->name";
       SystemLogs($message,$deviation->company_id,$action);

       $deviation = Deviation::where('id',$id)->delete();
       return back();
    }

    public function update(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        $id = isset($request->id)?$request->id:'';
        $date = isset($request->date)?$request->date:'';
        $d_name = isset($request->name)?$request->name:'';
        $issue = isset($request->issue)?$request->issue:'';
        $actions = isset($request->actions)?$request->actions:'';
        $status = isset($request->status)?$request->status:'';
        $files = $request->file('files');

            Deviation::where('id',$id)->update([
                "date" => $date,
                "name" => $d_name,
                "issue" => $issue,
                "actions" => $actions,
                "status" => $status,
            ]);
            if(isset($files) && $files!=null){
            $validator = Validator::make($request->all(), [
                'files.*' => 'required|mimes:jpg,jpeg,png,gif,pdf,zip,rar',
            ]);
    
            if ($validator->fails()) {
                return back()->with('message','Your file format is not allowed to upload or file is too large must be of less than 20mb.');  
            }else{
    
                $name=[];
            
                foreach($files as $file){
                    $fileName = $file->getClientOriginalName();
                    $file->move(base_path() . '/storage/app/public', $fileName);
                    $name[]=$fileName;
                }
                $allFiles = implode(",",$name);
                Deviation::where('id',$id)->update([
                    'files'=>$allFiles
                ]);
            }
        }
        $user =auth()->user()->name;
        $action ="Update";
        $company = Company::where('company_id',$company_id)->first();
        $message = "$user updated deviation ($d_name) in $company->name";
        SystemLogs($message,$company_id,$action);

            return back()->with('success','Deviation has been successfully Updated.');
        }
    }
