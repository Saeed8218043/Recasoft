<?php
use Log;
use App\SystemLog;

if (! function_exists('tokenDecode')) {
	function tokenDecode($jwt='',$key='1eb8bf3lUWURlAIdc4d023bf73bce91d20588f4539a',array $allowed_algs = array('HS256'))
	{
		if (empty($key)) {
			return false;
		}
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			return false;
		}
		list($headb64, $bodyb64, $cryptob64) = $tks;

		if (null === ($header = jsonDecode(urlsafeB64Decode($headb64)))) {
			return false;
		}
		if (null === $payload = jsonDecode(urlsafeB64Decode($bodyb64))) {
			return false;
		}
		if (false === ($sig = urlsafeB64Decode($cryptob64))) {
			return false;
		}
		if (empty($header->alg)) {
			return false;
		}

		if (!in_array($header->alg, $allowed_algs)) {
			return false;
		}
		return $payload;
		
	}

}

if (! function_exists('urlsafeB64Decode')) {
	function urlsafeB64Decode($input='')
	{
		$remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
	}
}

if (! function_exists('jsonDecode')) {
	function jsonDecode($input='')
	{
		return $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
	}
}
function santizeTWkey($string){
	$string = base64_decode($string);
	$string =  substr($string, 0, -1);
	return $string;
}

if (! function_exists('sendSms')) {
	function sendSms($number='', $message='')
	{
		$codeStatus=0;
		Log::info('phone as');
		Log::info($number);
		if(substr($number,0, 2)=='00'){
			$number = '+'.substr($number,2);
		}
		Log::info('updated number');
		Log::info($number);

		try {
			$number = str_replace(['(',')','  ',' '],'',$number);
			
			Log::info('in try function');
				$sid    = env( 'TWILIO_SID' );
				$token  = env( 'TWILIO_TOKEN' );
				$fromNumber = env( 'TWILIO_FROM' );
				$sid = santizeTWkey($sid);
				$token = santizeTWkey($token);


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
			Log::info($e);
			Log::info('error');
			$codeStatus=$e->getCode();
			Log::info($e->getCode());
			if($e->getCode() == 21211)
			{
				return $e->getCode();
			}
		}
		return $codeStatus;
	}
}
