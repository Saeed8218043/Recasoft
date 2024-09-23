<?php

namespace App\Http\Middleware;
use App\Company;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Closure;
use Alert;
use DB;
use App\CompanyMember;
use App\CompanyMembers;
use App\User;
use ErrorException;
use Exception;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->id == 1) {
            return $next($request);
        }else{

        $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $user_id = Auth()->user()->id;
        if ($user_id == 1) {
            return $next($request);
        }

        $query = "SELECT
        c.parent_id,
        c.name,
        c.company_id
    FROM
        companies c
    LEFT JOIN
        company_members cm
    ON
        (
            cm.comp_id = c.id OR c.parent_id>0
        )
    WHERE
         cm.user_id = $user_id
         
    GROUP BY
        c.company_id";

        $company = DB::select($query);
        $parent_comp ='';
        foreach($company as $Comp){
            if($Comp->parent_id ==0){
                $parent_comp = $Comp->company_id;
                break;
            }
        }
        $check = false;
        if(!empty($uriSegments[2]) || $uriSegments[1]=='system-log'){
            
            foreach ($company as $comp) {
                if ($comp->company_id == $uriSegments[2]  && $comp->company_id != null) {
                $check = true;
                break;
            }
        }
        if ($check == true) {
            return redirect()->to('/dashboard/'.$parent_comp)->with('error',"This URL is only for Super Admin you do not have access to this URL");

        } 
    }

    }
}
}
