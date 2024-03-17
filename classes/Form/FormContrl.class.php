<?php

	namespace Form;

	class FormContrl{

		public array $resultList;
		protected object $dialog;
		
		function __construct(protected array $input, protected string $captchaExits, protected string $name){
			$this->dialog = new \Common\Dialog();
		}

		public function sendForm($send,$mail,$db,$to,$subject,$success){
			$this->resultList = [];
			$submitSearch = array_search('submit', array_column($this->input, 'type'));
			if ($submitSearch !== false) {
				$submitObj = $this->input[$submitSearch];
				if(isset($_POST[$submitObj->name])){
					$verified = true;
					if($this->captchaExits === true){
						$cap = new \Captcha\GoogleRecaptcha("secretKey");
						$response = $_POST['g-recaptcha-response-'.$this->name];
						if(!empty($response)){
							$verified = $cap->Verify($response);  
							if(!$verified){
								array_push($this->resultList,"captcha failed");
							}
						}
						else{
							array_push($this->resultList,"captcha failed");
						}
					}
					$errorCount = 0;
					foreach($this->input as $row){
						$valid = $this->validate($row);
						if(!$valid){
							$errorCount++;
						}
					}
					if($verified && $errorCount > 0){
						$send();
						if($mail === true){
							$this->sendMail($to,$subject,$success);
						}
						if($db === true){
							$this->saveToDb();
						}
					}
					$resultText = "";
					foreach($this->resultList as $row){
						$resultText.= $row."<br/>";
					}
					$this->dialog->showDialog($resultText);
				}
			}

		}

		protected function validate($inputRow){
			if($inputRow->required === 'required' && empty($_POST[$inputRow->name])){
				array_push($this->resultList, $inputRow->title." input cannot be empty");
				return false;
			}
			else{
				$methodName = 'validate'.ucfirst($inputRow->type);
				if(method_exists($this,$methodName)){
					return $this->{$methodName}($_POST[$inputRow->name]);
				}
				else{
					return true;
				}
			}
		}

		protected function sendMail($to,$subject,$success){
			$mail = new \PHPMailer\PHPMailer\PHPMailer();
			$mail->Host = "localhost";
			$mail->From = "noreply@noreply.hu";
			$mail->FromName = "noreply";
			$mail->CharSet = "utf-8";
			$mail->AddAddress($to);
			$mail->Subject = $subject;
			$mailtext = "<html><head><title></title></head><body>";
			foreach($this->input as $row){
				switch($row->type){
					case 'checkbox':
						break;
					case 'radio':
						break;
					case 'select':
						break;
					case 'submit':
						break;
					default:
						$mailtext.= "<p><strong>{$row->label}:</strong> {$_POST[$row->name]}</p>";
						break;	
				}
			}
			$mailtext .= "</body></html>";
			$mail->MsgHTML($mailtext);
			$mail->IsHTML(true); // send as HTML
			if (!$mail->Send()) {
				array_push($this->resultList,$mail->ErrorInfo);
			} else {
				$success();
			}    
		}

		protected function saveToDb(){
			$db = new \Db\SqlDb();
		}
	}