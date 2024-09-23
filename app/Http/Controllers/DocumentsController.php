<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Document;
use Illuminate\Http\Request;
use App\Company;
use App\DeviceDocument;
use App\SystemLog;
use DB;
use Log;

class DocumentsController extends Controller
{
    public function index(Request $request)
    {
        $user_id = \Auth::user()->id;
        $search = isset($request->search)?$request->search:'';
        $company_id = isset($request->company_id) ? $request->company_id : '';
        $currentCompany = Company::select('name', 'company_id')->where('company_id', $company_id)->first();
        $documents = Document::where('company_id', $request->company_id)->where('parent_id', 0)->withCount('children')->orderBy('name', 'asc')->get();
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
        if($search!=''){
            $documents =Document::where('company_id', $request->company_id)->where('parent_id', 0)->where('name','LIKE',"%{$search}%")->withCount('children')->orderBy('name', 'asc')->get();
        }
        // dd($documents);
        $company_name = isset($company->name) ? $company->name : '';
        $id = \Auth::user()->id;
        // dd($counts);
        Session::forget('newArray');
        return view('documents.index', compact('company_id','search', 'documents', 'company_name', 'can_manage_users', 'currentCompany', 'user_id', 'cID', 'selectedParent'));
    }

    public function checkchildren(Request $request)
    {
        $transfer_company = isset($request->transfer_company) ? $request->transfer_company : '';
        $folder_id = isset($request->folder_id) ? $request->folder_id : '';
        $folder = Document::where('id', $folder_id)->first();
        if (Document::findOrfail($folder->id)) {
            $this->createCopyRecursive($folder, $transfer_company);
            return back();
        }
        /*  $children = Document::where('parent_id',$id)->where('company_id',$company_id)->get();

        if(isset($children)){
            foreach($children as $subfolder){
                Log::info('checkchildren');
                Log::info($subfolder);
                dd($subfolder);
                $this->checkchildren($subfolder->id,$company_id);
                // $child = Document::where('parent_id',$subfolder->id)->first();
            }
        }else{
            return back();
        }*/
    }
    public function createCopyRecursive($row, $transfer_company)
    {
        $find_parent = Document::where('belongsTo', $row->id)->where('parent_id', 0)->where('company_id', $transfer_company)->first();
        $find = Document::where('belongsTo', $row->id)->where('parent_id', 0)->where('company_id', $row->company_id)->first();
        if ($find_parent == null && $find==null) {
            $file = isset($row->file) ? $row->file : '';
            $slug = str_replace(' ', '-', $row->name);


            $documents = Document::where('slug', $row->slug)->where('parent_id', 0)->first();

            if (isset($documents)) {
                $parent = Document::create([
                    'name' => $row->name,
                    'company_id' => $transfer_company,
                    'type' => $row->type,
                    'user_id' => \Auth::user()->id,
                    'slug' => $slug,
                    'belongsTo' => $row->id,
                    'parent_id' => 0
                ]);
                $parent->update([
                    'slug' => $slug . $parent->id,
                ]);
            } else {
                $last_parent = Document::where('id', $row->parent_id)->first();
                $parent = Document::where('name', $last_parent->name)->where('company_id', $transfer_company)->where('belongsTo', $last_parent->id)->latest()->first();
                $sub = Document::create([
                    'name' => $row->name,
                    'company_id' => $transfer_company,
                    'type' => $row->type,
                    'user_id' => \Auth::user()->id,
                    'slug' => $slug,
                    'belongsTo' => $row->id,
                    'file' => $file
                ]);

                $sub->update([
                    'slug' => $sub->name . $sub->id,
                    'parent_id' => $parent->id
                ]);
            }

            if (isset($row->children) && count($row->children) > 0) {
                foreach ($row->children as $row3) {

                    $this->createCopyRecursive($row3, $transfer_company);
                }
                return back()->with('success', 'Folder Copy successful');
            }
        } else {
            return back()->with("message", "This folder is already exist in Company");
        }
    }




    public function syncFolder(Request $request)
    {
        $folder_id = isset($request->folder_id) ? $request->folder_id : '';
        $main_folder = Document::where('id',$folder_id)->first();
        $folders = Document::where('belongsTo', $folder_id)->get();
        if($folders->isNotEmpty()){
            foreach($folders as $folder){
    
                if (Document::findOrfail($folder->id)) {
                    $this->syncFolderRecursive($main_folder,$folder);
                    
                }
            }
            return back();
        }else{
            return back()->with("message", "This folder's clone is not in any project, cannot be synced");
        }
    }




    public function syncFolderRecursive($row,$folders)
    {
        $company_id = $folders->company_id;
        

        $file = isset($row->file) ? $row->file : '';
        $slug = str_replace(' ', '-', $row->name);


        $documents = Document::where('slug', $row->slug)->where('parent_id', 0)->first();

        if (isset($documents)) {
            $parent = Document::create([
                'name' => $row->name,
                'company_id' => $company_id,
                'type' => $row->type,
                'user_id' => \Auth::user()->id,
                'slug' => $slug,
                'belongsTo' => $row->id,
                'parent_id' => 0
            ]);
            $parent->update([
                'slug' => $slug . $parent->id,
            ]);
        } else {
            $last_parent = Document::where('id', $row->parent_id)->first();
            $parent = Document::where('name', $last_parent->name)->where('company_id', $company_id)->where('belongsTo', $last_parent->id)->latest()->first();
            $sub = Document::create([
                'name' => $row->name,
                'company_id' => $company_id,
                'type' => $row->type,
                'user_id' => \Auth::user()->id,
                'slug' => $slug,
                'belongsTo' => $row->id,
                'file' => $file
            ]);

            $sub->update([
                'slug' => $slug.$sub->id,
                'parent_id' => $parent->id
            ]);
        }

        if (isset($row->children) && count($row->children) > 0) {
            foreach ($row->children as $row3) {

                $this->syncFolderRecursive($row3,$folders);
            }
            
            return back()->with('success', 'Folder is synced');
        }
        if (isset($folders->children) && count($folders->children) > 0) {
            foreach ($folders->children as $row3) {
                $this->destroy($row3->id);
                $row3->delete();
            }
        }
        Document::where('id', $folders->id)->delete();
                

       
            return back()->with('success', 'Folder is synced successfully');
        } 
        
    


    
    public function store(Request $request)
    {

        
        $slug = str_replace(' ', '-', $request->name);
        Document::create([
            'name' => $request->name,
            'company_id' => $request->company_id,
            'user_id' => \Auth::user()->id,
            'slug' => $slug
        ]);
        $document = Document::where('slug', $slug)->first();
        $document->update([
            'slug' => $slug . $document->id
        ]);
        $user =auth()->user()->name;
        $action ="Create";
        $company = Company::where('company_id',$request->company_id)->first();
        $message = "$user created document ($request->name) in $company->name";
        SystemLogs($message,$request->company_id,$action);
        return back();
    }

    public function destroy($id)
    {
        $device_documents = DeviceDocument::where('belongsTo',$id)->get();
        if(isset($device_documents) && $device_documents!=null){

            foreach($device_documents as $file){
                $file->delete();
            }
        }
        $clone_folders = Document::where('belongsTo',$id)->first();
        if (isset($clone_folders->children) && count($clone_folders->children) > 0) {
            foreach ($clone_folders->children as $row3) {
                $this->destroy($row3->id);
                $row3->delete();
            }
            Document::where('id', $clone_folders->id)->delete();
        }

        $row = Document::where('id',$id)->first();
        if (isset($row->children) && count($row->children) > 0) {
            foreach ($row->children as $row3) {
                $this->destroy($row3->id);
                $row3->delete();
            }
        }
        if (isset($row)) {
    
        $user =auth()->user()->name;
        $action ="Delete";
        $company = Company::where('company_id',$row->company_id)->first();
        if($row->type == 0){
            $message = "$user Deleted document ($row->name) and its childs in $company->name";
        }else{
            $message = "$user Deleted document ($row->name) in $company->name";
        }
        SystemLogs($message,$row->company_id,$action);
    }
        Document::where('id', $id)->delete();
        return back()->with('message', 'Folder and its directory Deleted successfully');

        
        // $sub_files = Document::where('parent_id', $id)->get();
        // if (isset($sub_files)) {
        //     foreach ($sub_files as $sub) {
        //         $sub->delete();
        //     }
        // }
        
    }
    public function viewFolderValue(Request $request)
    {
        $data = Document::where('id', $request->id)->first();
        return view('documents.UpdateFolderModal', compact('data'))->render();
    }
    public function update(Request $request)
    {
        $id = isset($request->edit_id) ? $request->edit_id : '';
        
        $name = isset($request->name) ? $request->name : '';
        $data = Document::where('id', $id)->first();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $file->move(base_path() . '/storage/app/public', $fileName);
            $data->update([
                'file' => $fileName,
            ]);
        }
        $data->update([
            'name' => $name
        ]);
        $user =auth()->user()->name;
        $action ="Update";
        $company = Company::where('company_id',$data->company_id)->first();
        $message = "$user updated document ($name) in $company->name";
        SystemLogs($message,$data->company_id,$action);

        $device_documents = DeviceDocument::where('belongsTo',$id)->get();
        if(isset($device_documents) && $device_documents!=null){

            foreach($device_documents as $file){
                $file->update([
                    'name' => $name
                ]);
            }
        }
        return back();
    }



    public function subFolders(Request $request)
    {
        request()->segment(3);
        $validate_request = Document::where('company_id', $request->company_id)->where('slug', request()->segment(3))->first();
        if (isset($validate_request)) {


            $slug = isset($request->slug) ? $request->slug : '';
            $company_id = isset($request->company_id) ? $request->company_id : '';
            $company = Company::where('company_id', $request->company_id)->first();
            $company_name = isset($company->name) ? $company->name : '';
            $document = Document::where('slug', $slug)->first();
            $id = isset($document->id) ? $document->id : '';
            $documents = Document::orderby('type')->orderby('name')->where('parent_id', $id)->withCount('children')->get();
            $test = [];
            $search = isset($request->search)?$request->search:'';
            
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
            if($search!=''){
                $documents =Document::where('company_id', $request->company_id)->where('parent_id', $id)->where('name','LIKE',"%{$search}%")->withCount('children')->orderBy('name', 'asc')->get();
            }
            $html = view('documents.folderTablePart', compact('company_id', 'search','slug', 'company_name', 'documents'));
            $html = $html->render();
            return view('documents.subfolders', compact('html','search', 'slug', 'company_id', 'company_name'));
        } else {
            abort(404);
        }
    }

    public function createSubFolder(Request $request)
    {
        $company_id = isset($request->company_id) ? $request->company_id : '';
        $slug = str_replace(' ', '-', $request->name);
        $document = Document::where('slug', $request->slug)->first();

        Document::create([
            'name' => $request->name,
            'company_id' => $request->company_id,
            'user_id' => \Auth::user()->id,
            'parent_id' => $document->id,
            'slug' => $slug
        ]);
        $subdocument = Document::where('slug', $slug)->first();
        $subdocument->update([
            'slug' => $slug . $subdocument->id
        ]);
        $user =auth()->user()->name;
        $action ="Create";
        $company = Company::where('company_id',$request->company_id)->first();
        $message = "$user created subfolder ($request->name) of ($document->name) in $company->name";
        SystemLogs($message,$request->company_id,$action);
        return back();
    }
    public function createFile(Request $request)
    {
        $slug = str_replace(' ', '-', $request->name);
        $document = Document::where('slug', $request->slug)->first();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $file->move(base_path() . '/storage/app/public', $fileName);
            $document->create([
                'name' => $request->name,
                'file' => $fileName,
                'company_id' => $request->company_id,
                'user_id' => \Auth::user()->id,
                'type' => 1,
                'slug' => $slug,
                'parent_id' => $document->id
            ]);
            $fileData = Document::where('slug', $slug)->first();
            $fileData->update([
                'slug' => $slug . $fileData->id
            ]);

        $user =auth()->user()->name;
        $action ="Create";
        $company = Company::where('company_id',$request->company_id)->first();
        $message = "$user uploaded file ($request->name) in ($document->name) in $company->name";
        SystemLogs($message,$request->company_id,$action);
        }
        return back();
    }

    public function downloadFile($id)
    {
        $image = \App\Document::findOrFail($id);
        $pathToFile = storage_path('app/public/' . $image->file);
        return response()->download($pathToFile);
    }
}
