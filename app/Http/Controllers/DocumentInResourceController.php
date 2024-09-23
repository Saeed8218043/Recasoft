<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Document;
use Illuminate\Http\Request;
use App\Company;
use DB;
use Log;

class DocumentInResourceController extends Controller
{
    public function index(Request $request)
    {
        $user_id = \Auth::user()->id;
        $company_id = isset($request->company_id) ? $request->company_id : '';
        $currentCompany = Company::select('name', 'company_id')->where('company_id', $company_id)->first();
        $documents = Document::where('company_id', $request->company_id)
        ->where('parent_id', 0)
        ->withCount('children')
        ->orderBy('name', 'asc')
        ->get();
    
        $company = Company::where('company_id', $request->company_id)->first();
        $cID = isset($company->id) ? $company->id : '';
        $selectedParent = isset($company->parent_id) ? $company->parent_id : 0;

        $selectedParent = isset($company->parent_id) ? $company->parent_id : 0;
        $members = "select * from company_members where (company_id=? or comp_id=?) and user_id=? and role=2 order by id desc";
        $members = DB::select($members, [$company_id, $selectedParent, $user_id]);
        $can_manage_users = 0;
        if (isset($members[0]->id)  || $user_id == 1) {
            $can_manage_users = 1;
        }
        $company_name = isset($company->name) ? $company->name : '';
        $id = \Auth::user()->id;
        // dd($counts);
        Session::forget('newArray');

        $html = view('documents.folderTablePart2',compact('company_id', 'documents', 'company_name', 'can_manage_users', 'currentCompany', 'user_id', 'cID', 'selectedParent'));
        $html = $html->render();

        return response()->json(['html'=>$html]);
        
        // return view('documents.index', compact('company_id', 'documents', 'company_name', 'can_manage_users', 'currentCompany', 'user_id', 'cID', 'selectedParent'));
    }


    public function subFolders(Request $request)
    {
        
     
            $id = isset($request->id) ? $request->id : '';
            $slug = isset($request->slug) ? $request->slug : '';
            $company_id = isset($request->company_id) ? $request->company_id : '';
            $company = Company::where('company_id', $request->company_id)->first();
            $company_name = isset($company->name) ? $company->name : '';
            $documents = Document::where('parent_id', $id)->withCount('children')->orderBy('name', 'asc')->get();
            
            $test = [];

            $set = false;
            $index = 0;

            if (Session::has('newArray')) {
                $test =  Session::get('newArray');
                foreach ($test['data'] as $key => $item) {
                    if ($item['id'] == $id) {
                        $set = true;
                    }
                }
                if (!$set) {
                    array_push($test['data'], ['id' => $id]);
                }
            } else {

                $test = [
                    'data' => [
                        ['id' => $id]
                    ]
                ];
            }
            Session::put('newArray', $test);

            $html = view('documents.folderTablePart2',compact('company_id', 'documents','slug','id','company_name'));
        $html = $html->render();

        return response()->json(['html'=>$html]);
        
        }

}
