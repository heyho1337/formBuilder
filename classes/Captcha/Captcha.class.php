<?php
	namespace Captcha;

	abstract class Captcha{

		private static $instance;
		
		/**
		 * verify the captcha
		 * @param $response 
		*/
		abstract public function Verify($response);

		public static function getInstance(...$args) {
			if (!self::$instance) {
				self::$instance = new self(...$args);
			}
			return self::$instance;
		}

		private function __clone() {}
    	public function __wakeup() {}
	}