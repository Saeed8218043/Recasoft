<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\SystemLog;
class SystemLogController extends Controller
{
    public function index(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';

        $system_logs = SystemLog::select('*')->orderBy('created_at', 'desc')->paginate(50);
        // $system_logs=SystemLog::select('company_id',$company_id)->paginate(30);
        return view('system-log.index',compact('company_id','company_name','system_logs'));
    }
}
