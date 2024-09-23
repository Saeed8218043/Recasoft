<?php

namespace App\Http\Middleware;

use App\Company;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Config;
use Closure;
use Alert;
use DB;
use App\CompanyMember;
use App\CompanyMembers;
use App\User;
use ErrorException;
use Exception;
use Illuminate\Http\Request;

class RolesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $role2 = '';
        $child_companies = [];
        $email = \Auth::user()->email;
        $id = \Auth::user()->id;
        $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $user_id = Auth()->user()->id;
        $company = '';
        if ($user_id == 1) {
            $query = "SELECT c.parent_id, c.name, c.company_id
                    FROM
                        companies c
                    LEFT JOIN
                        company_members cm
                    ON
                        (c.parent_id=0)
                        WHERE
                        c.parent_id=0
                    GROUP BY
                        c.company_id";
            $company = DB::select($query);

        }


        if ($user_id > 1) {

            $companies = CompanyMembers::where('user_id', $id)->get();
            foreach ($companies as $comp) {
                $child_companies[] = Company::where('parent_id', $comp->comp_id)->get();
            }


            $company = collect($companies)->concat($child_companies)->flatten();
        }
        $company_id = '';
        foreach ($company as $Comp) {
            if ($Comp->parent_id != 0 || $company_id == '') {
                $company_id = $Comp->company_id;
                break;
            }
        }
        $check = false;


        if (isset($uriSegments[2])) {

            foreach ($company as $comp) {

                if (($comp->company_id == $uriSegments[2] && $comp->company_id != null)) {
                    $check = true;
                    break;
                }
            }

            if (($check == true || $user_id == 1)) {
            } else {
                if (isset($role2) && $role2 == 'valid') {
                    return redirect()->to('/dashboard/' . $company_id)->with('title', 'Project not found')->with('error', "You do not have access to the project");
                } else {

                    return redirect()->to('/equipments/' . $company_id)->with('title', 'Project not found')->with('error', "You do not have access to the project");
                }
            }
        } else {

            foreach ($company as $comp) {
                if ($comp->company_id != null) {
                    $check = true;
                    break;
                }
            }
            if ($check == true || $user_id == 1) {

                if (isset($role2) && $role2 == 'valid') {
                    return redirect()->to('/dashboard/' . $company_id);
                } else {

                    return redirect()->to('/equipments/' . $company_id);
                }

            }
        }
        return $next($request);

    }
}
