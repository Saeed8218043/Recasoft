<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Mail\RegisterUser;
use Carbon\Carbon;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\MyMail;
use DB;
use DataTables;
use Faker\Factory as Faker;
use Hash;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Image;
use Mail;
use File;
use Auth;
use App\User;
use App\Company;
use App\CompanyMember;
use App\CompanySettingEmail;
use App\CompanyMembers;
use App\RequestLog;
use App\Device;
use App\DeviceTemperature;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
  public function __construct()
    {
         // $this->middleware('CheckAdmin');in
    }
   
    public function index(Request $request)
    {
        $user_id = Auth::id();
        $company_id = isset($request->company_id)?$request->company_id:'';

         $where=$join='';
         $COmp = Company::where('company_id',$company_id)->select('id','parent_id')->first();
         $parent_id = isset($COmp->parent_id)?$COmp->parent_id:0;
        if($user_id==1){
            $where .= " and c.company_id='".$company_id."' ";
            $join=' left join devices d on (d.company_id=c.company_id OR d.coming_from_id=c.id) and d.device_status=1';
        }elseif($user_id>1){
            $sql = DB::table('company_members')->where('user_id',$user_id)->pluck('comp_id')->toArray();
            
            $cID = isset($COmp->id)?$COmp->id:0;
            $where .= " and  (c.company_id='".$company_id."' OR c.id = '".$cID."') ";
           
            $join=' left join devices d on (d.company_id=c.company_id and d.device_status=1 ) ';
        }else{
            $where .= " and c.company_id='".md5(time())."' ";
            $join=' left join devices d on (d.company_id=c.company_id ) ';
        }


        $query = "select c.*,
                  count(IF(d.event_type='ccon',1,null)) as connTotal,
                  count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                  from companies c
                  $join
                  where c.is_active=1 $where  limit 1";
        $company = DB::select($query);
        $company_name = isset($company[0]->name)?$company[0]->name:'';

        $memberss = "select * from company_members where (company_id=? or comp_id=?) and user_id=? order by id desc";
        $memberss = DB::select($memberss,[$company_id,$parent_id,$user_id]);
        $can_manage_users=0;
        $members=[];
        if(isset($memberss[0]->id) || $user_id==1){
            $can_manage_users=1;
            $members = "select * from company_members where company_id=? and role=1 order by id desc";
        $members = DB::select($members,[$company_id]);
        }
        $notification_emails = CompanySettingEmail::where('company_id',$company_id)->get();
        $notification_numbers = CompanySettingEmail::where('company_id',$company_id)->get();
        // dd($company);
        return view('company-settings.index', compact('parent_id','company_id','notification_emails','company','notification_numbers','company_name','members','can_manage_users'));
    }

    public function companyDetails(Request $request){
        $user_id = Auth::id();
        $company_id = isset($request->company_id)?$request->company_id:'';
        //  if($user_id==1){
        //     $query = "select c.*,
        //           count(IF(d.event_type='ccon',1,null)) as connTotal,
        //           count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
        //           from companies c
        //           left join devices d on (d.company_id=c.company_id or d.coming_from_id=c.id)
        //           where c.is_active=1 and c.company_id=? limit 1";
        // }else{
        //     $query = "select c.*,
        //           count(IF(d.event_type='ccon',1,null)) as connTotal,
        //           count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
        //           from companies c
        //           left join devices d on (d.company_id=c.company_id and d.device_status=1)
        //           where c.company_id=? limit 1";
        // }


         if($user_id==1){
            $query = "select c.*,
                  count(IF(d.event_type='ccon',1,null)) as connTotal,
                  count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                  from companies c
                  left join devices d on (d.company_id=c.company_id or d.coming_from_id=c.id)
                  where d.device_status=1 and c.is_active=1 and c.company_id=? limit 1";
        }else{
            // $query = "select c.*,
            //       count(IF(d.event_type='ccon',1,null)) as connTotal,
            //       count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
            //       from companies c
            //       left join devices d on (d.company_id=c.company_id and d.device_status=1 and c.is_active=1)
            //       where c.company_id=? limit 1";

                   $query = "select c.*,
                  count(IF(d.event_type='ccon',1,null)) as connTotal,
                  count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                  from companies c
                  left join devices d on (d.company_id=c.company_id or d.coming_from_id=c.id)
                  where d.device_status=1 and c.is_active=1 and c.company_id=? limit 1";
        }
        
        $company = DB::select($query,[$company_id]);
       
        // $company_name = isset($company[0]->name)?$company[0]->name:'';
        $company_row = Company::where('company_id',$company_id)->first();
        $company_name = $company_row->name;
        $members = "select * from company_members where company_id=? order by id desc";
        $members = DB::select($members,[$company_id]);
        return view('company-details.details', compact('company_id','company','company_name','members'));
    }

    public function companyAdmins(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        $query = "select c.*,
                  count(IF(d.event_type='ccon',1,null)) as connTotal,
                  count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                  from companies c
                  left join devices d on (d.company_id=c.company_id)
                  where c.is_active=1 and c.company_id=? limit 1";
        $company = DB::select($query,[$company_id]);
        $company_name = isset($company[0]->name)?$company[0]->name:'';
        $members = "select * from company_members where company_id=? order by id desc";
        $members = DB::select($members,[$company_id]);
        return view('administrators.index', compact('company_id','company','company_name','members'));
    }

    public function UpdateCompanySettings(Request $request){
        // dd($request->all());
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company_name = isset($request->company_name)?$request->company_name:'';
        $email = isset($request->email)?$request->email:'';
        $phone = isset($request->phone)?$request->phone:'';
        $description = isset($request->description)?$request->description:'';
        $organization_name = isset($request->organization_name)?$request->organization_name:'';
        $organization_no = isset($request->organization_no)?$request->organization_no:'';
        if($company_id != '') {
            $company = Company::where('company_id', $company_id)->first();
            $updatedFields = [];
            $user = auth()->user()->name;
            $action = "Update";
        
            if ($company->name != $company_name) {
                $updatedFields['name'] = $company_name;
                $message = "$user updated project's name ($company->name) to ($company_name) in project's setting";
                SystemLogs($message, $company_id, $action);
            }
        
            if ($company->email != $email) {
                $updatedFields['email'] = $email;
                $message = "$user updated email ($company->email) to ($email) in project's setting in $company->name";
                SystemLogs($message, $company_id, $action);
            }
        
            if ($company->description != $description) {
                $updatedFields['description'] = $description;
                $message = "$user updated description ($company->description) to ($description) in project's setting in $company->name";
                SystemLogs($message, $company_id, $action);
            }
        
            if ($company->organization_name != $organization_name) {
                $updatedFields['organization_name'] = $organization_name;
                $message = "$user updated organization name ($company->organization_name) to ($organization_name) in project's setting in $company->name";
                SystemLogs($message, $company_id, $action);
            }
        
            if ($company->organization_no != $organization_no) {
                $updatedFields['organization_no'] = $organization_no;
                $message = "$user updated organization number ($company->organization_no) to ($organization_no) in project's setting in $company->name";
                SystemLogs($message, $company_id, $action);
            }
        
            if (!empty($updatedFields)) {
                Company::where('company_id', $company_id)->update($updatedFields);
            }
        }
            // Company::where('company_id',$company_id)->update(array('name'=>$company_name,'email'=>$email,'phone'=>$phone,'description'=>$description,'organization_name'=>$organization_name,'organization_no'=>$organization_no));
            // // return redirect(url('company-settings/'.$company_id))->with('message','Updated Sucsessfull');
            

            return back();
    }

    public function UpdateCompanyImage(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        if($request->file('file')){
            $file = $request->file('file');
            $allowed = array('png', 'jpg', 'jpeg');
            $ext = $file->getClientOriginalExtension();
            if (!in_array(strtolower($ext), $allowed)) {
                return response()->json(['status'=>false,'message'=>'Invalid file type']);
                 exit();
            }

            $path = public_path("uploads/company_images");

            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move($path, $filename);

            $url = url('uploads/company_images/'.$filename);

            DB::table('companies')->where('company_id',$company_id)->update(array('image_url'=>$filename));

            return response()->json([
                'status'=>true,
                'message'=>'Image has been uploaded',
                'url'=>$url
            ]);
        }
    }

    public function deleteCompany(Request $request){
        //Log::info('My deleteCompany',['users' => [$request->company_id]]);
        $company_id = isset($request->company_id)?$request->company_id:'';

        $user =auth()->user()->name;
        $action ="Delete";
        $company = Company::where('company_id',$company_id)->first();
        $par_company = Company::where('id',$company->parent_id)->first();
        if(isset($par_company)){

            $message = "$user deleted child project ($company->name) from $par_company->name";
        }else{
            $message = "$user deleted inventory project ($company->name)";
        }
        SystemLogs($message,$company_id,$action);

        $company_parentID = DB::table('companies')->select('parent_id')->where('company_id', $company_id)->first();
        $company_Email = DB::table('companies')->select('email')->where('company_id', $company_id)->first();
        //Log::info('My deleteCompany',['users' => [typeof($company_Email)]]);
        $comp_members= DB::table('company_members')->where('company_id', $company_id)->get();
        if(!empty($comp_members)){
            foreach($comp_members as $row){
                $comp_members= DB::table('company_members')->where('id', $row->id)->delete();
            }
        }
        DB::table('companies')->where('company_id', $company_id)->delete();
        $other_company = DB::table('companies')->where('email', $company_Email->email)->first();

        // if($other_company != ''){
        //     DB::table('company_members')->where('email', $company_Email)->delete();
        //     DB::table('company_members_invite')->where('email', $company_Email)->delete();
        //     DB::table('users')->where('email', $company_Email)->delete();
        // }
        if($company_parentID->parent_id != 0){
            $firstComp = DB::table('companies')->where('id',$company_parentID->parent_id)->first();
        }else{
            $firstComp = DB::table('companies')->first();
        }

        $url = "/company-settings/".$firstComp->company_id;
        return redirect($url);
    }

    public function DeleteCompanyImage(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        DB::table('companies')->where('company_id',$company_id)->update(array('image_url'=>null));
        return back();
    }

    public function inviteMember(Request $request){
        $company_Memeber_ID = isset($request->compMemberId)?$request->compMemberId:0;
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = DB::table('companies')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $company_email = isset($company->email)?$company->email:'';
        // $invited_company_name = (isset($request->invited_company_name))?$request->invited_company_name:'';
        $email = isset($request->email)?$request->email:'';
        $role = isset($request->role)?$request->role:null;
        $url='';

        
        $user = auth()->user()->name;
        $action = "Create";
        $message = "$user invited user ($email) in $company->name";
            SystemLogs($message, $company_id, $action);
        $memberexist = DB::table('company_members_invite')->where('email',$request->email)->where('company_id',$request->company_id)->first();

        if($memberexist==null){
        // DB::table('company_members_invite')->insertGetId(array('email'=>$request->email,'company_id'=>$request->company_id));
        // Entery in company_members table
        // $message = 'Invitation has been sent';

        $comp = Company::where('company_id',$company_id)->select('id')->first();
        $comp_id = isset($comp->id)?$comp->id:0;
        $last_id = DB::table('company_members')->insertGetId(array('comp_id'=>$comp_id,'email'=>$email,'company_name'=>$company_name, 'role'=>$role,'company_id'=>$company_id,'parent_id'=>$company_Memeber_ID,'created_at'=>date('Y-m-d H:i:s', time())));
        $data_ar = ['company_name'=>$company_name,'company_email'=>$company_email];

        $member = DB::table('users')->where(['email'=>$email/*,'company_id'=>$company_id*/])->get();
        $memberInUsers = DB::table('users')->where('email',$email)->first();
        
        if(isset($member) && count($member)>0){
            $url = url('login-user/'.$company_id.'/'.$email);
            if($url!=''){
                $data_ar['url']=$url;
            }
            $subject = 'Access granted: '.$company_name;
            $to_name = $company_name;
            $to_email = $email;
            try {
                Mail::send('emails.invitation-email-accepted', $data_ar, function($message) use ($to_name, $to_email, $subject, $company_email) {
                    $message->to($to_email, $to_name)
                    ->subject($subject);
                    $message->from('notification@recasoft.com','Recasoft Technologies');
                    $message->replyTo('notification@recasoft.com','Recasoft Technologies');
                });
    		
            } catch(\Exception $e){
                // echo 'error';
            }
            if(isset($memberInUsers) && $memberInUsers->email == $email){
                // Entery in company_members_invite table
                DB::table('company_members_invite')->insert(array('email'=>$email,'company_id'=>$company_id,'accepted'=>1));
                $user_id = isset($member[0]->id)?$member[0]->id:0;
                \App\CompanyMembers::whereId($last_id)->update(['user_id'=>$user_id]);
            }else{
                DB::table('company_members_invite')->insert(array('email'=>$email,'company_id'=>$company_id,'accepted'=>0));
                $user_id = isset($member[0]->id)?$member[0]->id:0;
                \App\CompanyMembers::whereId($last_id)->update(['user_id'=>$user_id]);
            }
            
        }else{
            $url = url('account/register/'.\Crypt::encrypt($last_id));
            if($url!=''){
                $data_ar['url']=$url;
            }
            $subject = 'Access granted: '.$company_name;
            $to_name = $company_name;
            $to_email = $email;
            try {
                Mail::send('emails.invitation-email', $data_ar, function($message) use ($to_name, $to_email, $subject, $company_email) {
                    $message->to($to_email, $to_name)
                    ->subject($subject);
                    $message->from('notification@recasoft.com','Recasoft Technologies');
                    $message->replyTo('notification@recasoft.com','Recasoft Technologies');
                });
    		
            } catch(\Exception $e){
                // echo 'error';
            }
            if(isset($memberInUsers) && $memberInUsers->email == $email){
                // Entery in company_members_invite table
                DB::table('company_members_invite')->insert(array('email'=>$email,'company_id'=>$company_id,'accepted'=>1));
                $user_id = isset($member[0]->id)?$member[0]->id:0;
                \App\CompanyMembers::whereId($last_id)->update(['user_id'=>$user_id]);
            }else{
                DB::table('company_members_invite')->insert(array('email'=>$email,'company_id'=>$company_id,'accepted'=>0));
                $user_id = isset($member[0]->id)?$member[0]->id:0;
                \App\CompanyMembers::whereId($last_id)->update(['user_id'=>$user_id]);
            }
            
        }

        return redirect(url('company-settings/'.$company_id))->with('message','Invitation has been sent');
    }
    else{
        return redirect(url('company-settings/'.$company_id))->with('error','This Email is already registered in this company');
        
    }
}

    public function inviteAdmin(Request $request){
        $company_Memeber_ID = isset($request->compMemberId)?$request->compMemberId:0;
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = DB::table('companies')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $company_email = isset($company->email)?$company->email:'';
        //$invited_company_name = (isset($request->invited_company_name))?$request->invited_company_name:'';
        $email = isset($request->email)?$request->email:'';
        $role = 2;
        $url='';
        $comp = Company::where('company_id',$company_id)->select('id','name')
        ->with('childs:id,company_id,parent_id,name')->first();
        $comp_id = isset($comp->id)?$comp->id:0;
        $memberExist =CompanyMembers::where('email',$email)->where('company_id',$company_id)->first();

        if($memberExist==null){
        $last_id = DB::table('company_members')->insertGetId(array('comp_id'=>$comp_id,'email'=>$email,'company_name'=>$company_name, 'role'=>$role,'company_id'=>$company_id,'parent_id'=>$company_Memeber_ID,'created_at'=>date('Y-m-d H:i:s', time())));
        
        $data_ar = ['company_name'=>$company_name,'company_email'=>$company_email];
        // $message = 'Invitation has been sent';
        $member = DB::table('users')->where(['email'=>$email/*,'company_id'=>$company_id*/])->get();
        if(isset($member) && count($member)>0){
            $user_id = isset($member[0]->id)?$member[0]->id:0;

            /*if(isset($comp->childs) && count($comp->childs)>0){
                foreach($comp->childs as $child){
                    $last_id2 = DB::table('company_members')->insertGetId(array('user_id'=>$user_id,'comp_id'=>$child->id,'email'=>$email,'company_name'=>$child->name, 'role'=>$role,'company_id'=>$child->company_id,'parent_id'=>$company_Memeber_ID,'created_at'=>date('Y-m-d H:i:s', time())));
                }
            }*/

            $url = url('login-user/'.$company_id.'/'.$email);
            if($url!=''){
                $data_ar['url']=$url;
            }
            $subject = 'Access granted: '.$company_name;
            $to_name = $company_name;
            $to_email = $email;
            try {
                Mail::send('emails.invitation-email-accepted', $data_ar, function($message) use ($to_name, $to_email, $subject, $company_email) {
                    $message->to($to_email, $to_name)
                    ->subject($subject);
                    $message->from('notification@recasoft.com','Recasoft Technologies');
                    $message->replyTo('notification@recasoft.com','Recasoft Technologies');
                });
    		
            } catch(\Exception $e){
                // echo 'error';
            }
            DB::table('company_members_invite')->insert(array('email'=>$email,'company_id'=>$company_id,'accepted'=>1));
            
            \App\CompanyMembers::whereId($last_id)->update(['user_id'=>$user_id]);
        }else{
            $url = url('account/register/'.\Crypt::encrypt($last_id));
            if($url!=''){
                $data_ar['url']=$url;
            }
            $subject = 'Access granted: '.$company_name;
            $to_name = $company_name;
            $to_email = $email;
            try {
                Mail::send('emails.invitation-email', $data_ar, function($message) use ($to_name, $to_email, $subject, $company_email) {
                    $message->to($to_email, $to_name)
                    ->subject($subject);
                    $message->from('notification@recasoft.com','Recasoft Technologies');
                    $message->replyTo('notification@recasoft.com','Recasoft Technologies');
                });
    		
            } catch(\Exception $e){
                // echo 'error';
            }
        }
        $user =auth()->user()->name;
        $action ="Create";
        $company = Company::where('company_id',$company_id)->first();
        $message = "$email has been invited by $user in $comp->name";
        SystemLogs($message,$request->company_id,$action);
        return back()->with('message','Invitation has been sent');
    }
    else{
        return back()->with('error','This Email is already added in this Project');
    }
    }

    public function createAccount($id){
        $member_id = \Crypt::decrypt($id);
        
        $member = DB::select("select * from company_members where id=? limit 1",[$member_id]);
        $email = isset($member[0]->email)?$member[0]->email:'';
        $company_id = isset($member[0]->company_id)?$member[0]->company_id:'';
        $in_id = $id;
        return view('company-settings.register',compact('company_id','email','in_id'));
    }

    public function createUser(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        $name = isset($request->name)?$request->name:'';
        $email = isset($request->email)?$request->email:'';
        $in_id = isset($request->in_id)?$request->in_id:0;
        $member_id = \Crypt::decrypt($in_id);
        $password = isset($request->password)?$request->password:'';

        $confirm_password = isset($request->confirm_password)?$request->confirm_password:'';
        $user = DB::table('company_members')->where('email',$email)->first();
        if($user){

           $company_member = DB::table('company_members_invite')->where('email',$email)->where('company_id',$company_id)->first();
            if($company_member == null){
                DB::table('company_members_invite')->insert(array('email'=>$email,'company_id'=>$company_id,'accepted'=>1));

            }else{
                DB::table('company_members_invite')->where('id',$company_member->id)->update(['accepted'=>1]);
            }
            $user_id = DB::table('users')->insertGetId(array('name'=>$name,'email'=>$email,'password'=>Hash::make($password),'created_at'=>date('Y-m-d H:i:s', time())));
            \App\CompanyMembers::whereId($member_id)->update(['user_id'=>$user_id]);
            $user = User::where('email',$email)->first();
            Auth::login($user);
            return redirect(url('login'));
        }else{
            return back();
        }
    }

    public function loginUser($company_id,$email){
        $member = DB::table('company_members_invite')->where(['company_id'=>$company_id,'email'=>$email])->get();
        $email = isset($member[0]->email)?$member[0]->email:'';
        return redirect(url('login'));
        // $user = User::where('email',$email)->first();
        // Auth::login($user);
        // return redirect('dashboard/'.$company_id);
    }
    
    public function deleteFromCompany(Request $request){
        $email = isset($request->email)?$request->email:'';
        $company_id = isset($request->company_id)?$request->company_id:'';

        $user =auth()->user()->name;
        $action ="Delete";
        $company = Company::where('company_id',$company_id)->first();
        $message = "$user removed $email from $company->name";
        SystemLogs($message,$request->company_id,$action);

        DB::table('company_members')->where(['email'=>$email,'company_id'=>$company_id])->delete();
        DB::table('company_members_invite')->where(['email'=>$email,'company_id'=>$company_id])->delete();


        return redirect()->back()->with('message','Member successfully removed');//url('company-settings/'.$company_id)
    }
    public function deleteEmail(Request $request){
        $email = isset($request->email)?$request->email:'';
        $company_id = isset($request->company_id)?$request->company_id:'';

        $user =auth()->user()->name;
        $action ="Delete";
        $company = Company::where('company_id',$company_id)->first();
        $message = "$user removed $email from $company->name";
        SystemLogs($message,$request->company_id,$action);

        DB::table('company_setting_emails')->where(['email'=>$email,'company_id'=>$company_id])->delete();

        return redirect()->back()->with('title', 'Recipient removed')->with('success', 'Recipient successfully removed');
        //url('company-settings/'.$company_id)
    }
    public function deleteNumber(Request $request){
        $number = isset($request->email)?$request->email:'';
        $company_id = isset($request->company_id)?$request->company_id:'';

        $user =auth()->user()->name;
        $action ="Delete";
        $company = Company::where('company_id',$company_id)->first();
        $message = "$user removed $number from $company->name";
        SystemLogs($message,$request->company_id,$action);

        DB::table('company_setting_emails')->where(['emailOrNumber'=>$number,'company_id'=>$company_id])->delete();

        return redirect()->back()->with('title', 'Number removed')->with('success', 'Number successfully removed');
        //url('company-settings/'.$company_id)
    }

    public function abc123(){
        $data_ar=['url'=>''];
        $to_name='Ahad';
        $to_email = 'ahad.mashkraft@gmail.com';
        $subject='Recasoft';
        Mail::send('emails.reset-password', $data_ar, function($message) use ($to_name, $to_email, $subject) {
                $message->to($to_email, $to_name)
                ->subject($subject);
                $message->from('notification@app.recasoft.no','Recasoft Technologies');
                $message->replyTo('notification@app.recasoft.no','Recasoft Technologies');
            });
        // return view('emails.invitation-email');
    }
    public function updateSettings($company_id,Request $request)
    {
        $email = isset($request->setting_email)?$request->setting_email:'';

        
        if($email!=''){
            $setting = \App\CompanySetting::where('company_id',$company_id)->where('meta_key','email')->first();
            $previous_email = isset($setting->meta_value)?$setting->meta_value:' ';
            $user =auth()->user()->name;
            $action ="Update";
            $company = Company::where('company_id',$company_id)->first();
            $message = "$user Email for service requests changed ('$previous_email') to ('$email') of $company->name";
            SystemLogs($message,$company_id,$action);
            if($setting ==null){


                $setting = new \App\CompanySetting();    
                $setting->meta_key='email';
                $setting->company_id=$company_id;
                $setting->meta_value = $email;
                $setting->save();
            }else if($setting != null){
                $setting->update([
                    'meta_value'=>$email
                ]);
            }
        }
        else{
            $result =\App\CompanySetting::where('company_id',$company_id)->where('meta_key','email')->delete();
        }

        return redirect()->back();
    }
    public function sendOrderService($company_id,Request $request)
    {
        $attach = $request->file('order_service_file');
        $file = isset($attach)?$attach:'';
        $company_id2 = $company_id;
        $comp = Company::where('company_id',$company_id)->first();
        // dd($request->all());
        if(isset($comp->parent_id) && $comp->parent_id>0){
            $comp = Company::where('id',$comp->parent_id)->first();
            $company_id2 = isset($comp->company_id)?$comp->company_id:'-';
        }
         $setting = \App\CompanySetting::where('company_id',$company_id2)->where('meta_key','email')->first();
         Log::info('orderService');
         // Log::info($setting);
         if(isset($setting->meta_value) && $setting->meta_value!=''){
            $company_email = isset($comp->email)?$comp->email:'';
            $user = auth()->user()->email;
            $company = Company::where('company_id',$company_id)->first();
            $company_name = isset($request->name)?$request->name:'';
            $phone_number = isset($request->phone_number)?$request->phone_number:'';
            $urgent = isset($request->urgent)?$request->urgent:'No';
            $company_name = isset($request->company_name)?$request->company_name:'';
            $description = isset($request->description)?$request->description:'';
            $deviceIDs = isset($request->devices)?$request->devices:[];
            $devices = implode(', ',$deviceIDs);
            $allDevicesArray = explode(', ', $devices);
            $deviceNames =[];
            foreach($allDevicesArray as $device){
                    $device_data = Device::where('sensor_id',$device)->first();
                    if(!empty($device_data->name)){
                        $deviceNames[] = $device_data->name;
                    }
                    else if(!empty($device_data->device_id) && $device_data->name ==''){
                        $deviceNames[] = $device_data->device_id;
                    }else{
                        $non_connected_equipment = Device::where('device_id',$device)->first();
                        $deviceNames[] = $non_connected_equipment->name;
                    }

            }
            $finaldeviceNames = implode("\r\n",$deviceNames);
            $devicesForLog = implode(', ',$deviceNames);

            $to_email = $setting->meta_value;
            if(isset($file) && $file!=null){
                $fileName = $file->getClientOriginalName();
                $file->move(base_path() . '/storage/app/public', $fileName);
                $pathToFile = realpath(Storage::disk('public')->path($fileName));
            }
            $attachFile = isset($pathToFile)?$pathToFile:'';
            $filename = isset($fileName)?$fileName:'';
            $to_name='';
            $subject='Service request from '.$company_name;
            if($urgent =='Yes'){

                $subject2='Urgent Service request confirmation ';
            }else{

                $subject2='Service request confirmation ';
            }
            $data_ar=[
                'company_id'=> $company_id,
                'email'=>$to_email,
                'phone_number'=> $phone_number,
                'urgent'=> $urgent,
                'company_name'=>$company_name,
                'deviceNames'=>$finaldeviceNames,
                'description'=>$description,
            ];

            
            try {
                Mail::send('emails.sendOrderService', $data_ar, function($message) use ($to_name, $to_email , $subject,$company_name,$attachFile,$file) {
   
                    $message
                        ->to($to_email, $to_name)
                        ->subject($subject) 
                        ->from('notification@recasoft.no','Recasoft Technologies')
                        ->replyTo('notification@recasoft.no','Recasoft Technologies');
                        if(isset($file) && $file!=null){
                            $message->attach($attachFile);
                        }
                        
                });
                
            } catch (\Exception $e) {
                Log::info('order service mail issue');
                Log::info($e);
            }
            RequestLog::create([
                'company_name' => $company_name,
                'company_id' => $company_id,
                'request_email' => $user,
                'phone' => $phone_number,
                'urgent'=> $urgent,
                'comments' => $description,
                'attachment' => $filename, 
                'devices' => $devicesForLog,
              ]);

            try {
                Mail::send('emails.emailToCompany', $data_ar, function($message) use ($to_name, $user, $subject2,$company_name,$attachFile,$file) {
   
                    $message
                        ->to($user, $to_name)
                        ->subject($subject2) 
                        ->from('notification@recasoft.no','Recasoft Technologies')
                        ->replyTo('notification@recasoft.no','Recasoft Technologies');
                        if(isset($file) && $file!=null){
                            $message->attach($attachFile);
                        }
                        
                });
            } catch (\Exception $e) {
                Log::info('order service mail issue');
                Log::info($e);
            }
            $user =auth()->user()->name;
            $action ="Order Service";
            $message = "$user ordered service ($devicesForLog) from $company->name.";
            SystemLogs($message,$company_id,$action);
            
            return redirect()->back()->with('messageUpload','Email has been sent successfully.');
        }
        
    }

    // public function sendmailbyphp($from_address = "",$username = 'fazil.mashkraft', $password = "",$message =""){
    //     // php code will be write here to send the mail
    // }
}

