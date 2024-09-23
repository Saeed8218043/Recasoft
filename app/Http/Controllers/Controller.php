<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Twilio\Rest\Client;
use Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $data=[];

    function santizeTWkey($string){
    	$string = base64_decode($string);
    	$string =  substr($string, 0, -1);
    	return $string;
    }
    protected function sendSms($number='', $message='')
	{
		$codeStatus=0;
		\Log::info('phone as');
		\Log::info($number);
		if(substr($number,0, 2)=='00'){
			$number = '+'.substr($number,2);
		}
		\Log::info('updated number');
		\Log::info($number);

		try {
			$number = str_replace(['(',')','  ',' '],'',$number);
			
				
				$sid    = env( 'TWILIO_SID' );
				$token  = env( 'TWILIO_TOKEN' );
				$fromNumber = env( 'TWILIO_FROM' );
				$sid = $this->santizeTWkey($sid);
				$token = $this->santizeTWkey($token);


			$client = new Client( $sid, $token );
			$client->messages->create(
				$number,
				[
					'from' => $fromNumber,
					'body' => $message,
				]
			);
			$codeStatus=1;
		} catch (\Exception $e){
			$codeStatus=$e->getCode();
			\Log::info($e->getCode());
			if($e->getCode() == 21211)
			{
				return $e->getCode();
			}
		}
		return $codeStatus;
	}
}
