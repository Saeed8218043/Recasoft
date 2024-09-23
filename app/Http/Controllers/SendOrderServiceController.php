<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\RequestLog;
use App\Device;


class SendOrderServiceController extends Controller
{
    public function index(Request $request)
    {
        $company_id = isset($request->company_id)?$request->company_id:'';
        $currentCompany = $company = Company::select('name','parent_id','company_id')->where('company_id',$company_id)->first();
        $user_id = \Auth::user()->id;
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $selectedParent = isset($company->parent_id)?$company->parent_id:0;
        $cID = isset($company->id)?$company->id:0;

        $equipments = Device::select('name','company_id','device_id','event_type')->where('company_id',$company_id)->where('event_type','equipment')->orderBy('name','ASC')->get();

          return view('order-service.index',compact('equipments','currentCompany','company_name','company_id'));

    }
    public function logs(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $logs = RequestLog::where('company_id',$request->company_id)->paginate(30);
        return view('order-service.order_service_logs',compact('logs','company_id','company_name'));
    }
}
