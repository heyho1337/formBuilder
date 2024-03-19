<?php
	
	namespace Common;

	/**
	 * Class Common\Dialog
	*/
	class Dialog{
		
		/**
		 * show a html dialog with a message
		 * @param string text - the message 
		*/
		public function showDialog($text) {
			$id = $this->randomPassword(10);
			$dialog = "<dialog open id=\"{$id}\"><p>{$text}</p><button class=\"closeButton\" autofocus>Bezár</button></dialog>";
			
			return "
				<script>
					$('body').append('{$dialog}');
					$('body').on('click', '.closeButton', function() {
						var dialogId = $(this).closest('dialog').attr('id');
						var dialog = $('#' + dialogId);
						dialog.remove();
					});
				</script>
			";
		}

		/**
		 * generating random characters
		 * @param int $length character's length
		 * @return string the random characther chain 
		 */
		function randomPassword($length) {  
			$possibleCharacters =  "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";  
			$characterLength = strlen($possibleCharacters);  
			$seed = (double) microtime() * 1000000;  
			srand($seed);  
			$password = "";  
			for($i=1;$i<=$length;$i++){  
			$character = rand(1,$characterLength);  
			$character = substr($possibleCharacters,$character, 1);  
			$password .= $character;  
			}  
			return $password;  
		}
	}