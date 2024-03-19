<?php
	
	namespace Captcha;

	/**
	 * Class Captcha\GoogleRecaptcha
	*/
	class GoogleRecaptcha extends Captcha {
		/* Google recaptcha API url */
    	const google_url = "https://www.google.com/recaptcha/api/siteverify";

		/**
		 * @param string $secterKey - the google captcha's secret key
		 * @param string $siteKey - the google capcha's site key
		*/
		function __construct(protected string $secretKey,protected string $siteKey){
			echo "<script src='https://www.google.com/recaptcha/api.js?render={$this->siteKey}'></script>
			<script>
				grecaptcha.ready(function () {
					grecaptcha.execute('{$siteKey}', { action: 'contact' }).then(function (token) {
						if (document.getElementById('recaptchaResponse') !=null) {
							var recaptchaResponse = document.getElementById('recaptchaResponse');
							recaptchaResponse.value = token;
						}
					});
				});
			</script>
			";
		}

		/**
		 * verify the google captcha's result after its submission
		 * @param string $repsone - the google captcha's query response
		*/
		public function Verify($response){
			$url = self::google_url."?secret=".$this->secretKey."&response=".$response."&remoteip=".$_SERVER['REMOTE_ADDR'];

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