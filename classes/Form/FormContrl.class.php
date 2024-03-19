<?php

	namespace Form;

	/**
	 * Class Form\FormContrl
	*/
	class FormContrl{

		public array $resultList;
		protected object $dialog;
		protected bool $verified = true;
		protected int $errorCount = 0;

		/***
		 * database table for the forms's submit data
		*/
		protected const formTable = [
			'form_id' => 'INT AUTO_INCREMENT PRIMARY KEY',
			'form_fields' => 'TEXT',
			'form_name' => 'VARCHAR(50)',
			'form_subject' => 'VARCHAR(50)',
			'form_to' => 'VARCHAR(50)'
		];
		
		/**
		 * @param array $input - an array that should contain the form's input elements
		 * @param bool $captcha - true | false determines if there should be a captcha
		 * inside the form or not.
		 * @param string $name - the form's html name attribute
		 * @param string $subject - the subject that will appear inside the email the 
		 * form will send out after submission
		 * @param string $to - the email address where the form will send the email
		 * after it's submission
		*/
		function __construct(protected array $input, public bool $captcha, protected string $name, protected string $subject, protected string $to){
			$this->dialog = new \Common\Dialog();
		}

		/**
		 * the form's submission
		 * @param bool $mail - true | false determines if the form should send an email or not
		 * @param bool $db - true | false determines if the form should save it's data into a 
		 * database or not
		 * @param string $to - the email address where the form will send the email
		 * after it's submission
		 * @param string $subject - the subject that will appear inside the email the 
		 * form will send out after submission
		 * @param string $send - you can add your own function to the form's submission
		 * @param string $success - you can specify what the form should do after the 
		 * submission is done
		*/
		public function sendForm(bool $mail,bool $db,string $to,string $subject, $send = null, $success = null){
			$submitSearch = array_search('submit', array_column($this->input, 'type'));
			if ($submitSearch !== false) {
				$submitObj = $this->input[$submitSearch];
				if(isset($_POST[$submitObj->name])){
					$this->validateForm();
					if($this->verified && $this->errorCount === 0){
						if(is_callable($send)){
							$send();
						}
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
					echo $resultText;
					echo $this->dialog->showDialog($resultText);
				}
			}

		}

		/**
		 * validating the form and it's input elements
		*/
		protected function validateForm(){
			$this->resultList = [];
			$this->errorCount = 0;
			$this->verified = true;
			if($this->captcha === true){
				$cap = \Captcha\GoogleRecaptcha::getInstance();
				$response = $_POST['g-recaptcha-response-'.$this->name];
				if(!empty($response)){
					$this->verified = $cap->Verify($response);  
					if(!$this->verified){
						array_push($this->resultList,"captcha failed");
					}
				}
				else{
					array_push($this->resultList,"captcha failed");
				}
			}
			foreach($this->input as $row){
				$valid = $this->validateInput($row);
				if(!$valid){
					$this->errorCount++;
				}
			}
		}

		/**
		 * validating an input element baed on it's type
		 * @param object $inputRow - the input element's object from the 
		 * form's input array
		*/
		protected function validateInput(object $inputRow){
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

		/**
		 * the form's email sending function
		 * @param string $success - you can specify what the form
		 * should do after it's successfully send out the email
		*/
		protected function sendMail($success = null){
			$mail = new \PHPMailer\PHPMailer();
			$mail->Host = "localhost";
			$mail->From = "noreply@noreply.hu";
			$mail->FromName = "noreply";
			$mail->CharSet = "utf-8";
			$mail->AddAddress($this->to);
			$mail->Subject = $this->subject;
			$mailtext = "<html><head><title>{$this->subject}</title></head><body>
			<h1>{$this->subject}</h1>";
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
			$mail->IsHTML(true);
			if (!$mail->Send()) {
				array_push($this->resultList,$mail->ErrorInfo);
			} else {
				if(is_callable($success)){
					$success();
				}
			}    
		}

		/**
		 * saving the form's data into the database
		*/
		protected function saveToDb(){
			$db = \Db\SqlDb::getInstance();
			$db->create('forms',self::formTable);
			$fields = [];
			foreach($this->input as $row){
				$fields[$row->label] = $_POST[$row->name];
			}
			$_POST['form_fields'] = json_encode($fields);
			$_POST['form_name'] = $this->name;
			$_POST['form_subject'] = $this->subject;
			$_POST['form_to'] = $this->to;
			$db->insert('forms');
		}
	}