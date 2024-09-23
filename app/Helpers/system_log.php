<?php

use App\SystemLog;

if (! function_exists('SystemLogs')) {

    function SystemLogs($message,$company_id,$action){
        $log = new SystemLog;
        $log->company_id = $company_id;
        $log->actions = $action;
        $log->user = auth()->user()->name;
        $log->log_message = $message; 
        $log->save();
        
    }
}
