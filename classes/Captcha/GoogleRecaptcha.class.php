<?php
	
	namespace Captcha;

	class GoogleRecaptcha {
		/* Google recaptcha API url */
    	const google_url = "https://www.google.com/recaptcha/api/siteverify";

		function __construct(protected string $secretKey){
			
		}

		public function Verify($response){
			$url = self::google_url."?secret=".$this->secretKey."&response=".$response;

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 15);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
			$curlData = curl_exec($curl);

			curl_close($curl);
			
			$res = json_decode($curlData, true);
			if($res['success'] == 'true'){ 
				return true;
			}
			else{
				return false;
			}
		}
	}