<?php

	namespace Form;

	class FormView{

		protected string $formHTML = "";
		protected string $formInput = "";

		function __construct(protected array $input, protected bool $captcha){

		}

		public function loadResources(){
			return '
				<link rel="preload" href="style/form.css" as="style">
				<link rel="stylesheet" href="style/form.css" media="all">
			';
		}

		public function showForm($method,$action,$name, $class){
			$this->setFormElements();
			$this->formHTML = "
				<form method='{$method}' action='{$action}' name='{$name}' class='formMDL {$class}'>
					{$this->formInput}
				</form>
			";
			return $this->formHTML;
		}

		protected function setFormElements(){
			foreach($this->input as $row){
				$this->formInput.= $this->{$row->type}($row);
			}
		}

		protected function text($row){
			return "
				<div class='textInput {$row->class}'>
					<input type='text' id='{$row->name}' title='{$row->title}' name='{$row->name}' placeholder='{$row->label}' {$row->required} value='{$row->value}'/>
					<label for='{$row->name}'>{$row->label}</label>
				</div>
			";
		}

		protected function email($row){
			return "
				<div class='emailInput {$row->class}'>
					<input type='email' id='{$row->name}' title='{$row->title}' name='{$row->name}' placeholder='{$row->label}' {$row->required} value='{$row->value}'/>
					<label for='{$row->name}'>{$row->label}</label>
				</div>
			";
		}

		protected function submit($row){
			$cap = "";
			if($this->captcha === true){
				$cap = "<input type='hidden' name='recaptcha_response' id='recaptchaResponse'>";
			}
			return "{$cap}
				<button type='submit' data-label='{$row->label}' title='{$row->title}' name='{$row->name}' value='{$row->value}'>{$row->label}</button>
			";
		}

	}