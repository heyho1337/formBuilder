<?php

	namespace Form;

	/**
	 * Class Form\FormView
	 * showing the fomr's html
	*/
	class FormView{

		protected string $formHTML = "";
		protected string $formInput = "";

		/**
		 * @param array $input - an array that should contain the form's input elements
		 * @param bool $captcha - true | false determines if there should be a captcha
		 * inside the form or not.
		*/
		function __construct(protected array $input, protected bool $captcha){

		}

		/**
		 * loading the neccessary resources for the form to works.
		*/
		public function loadResources(){
			return '
				<link rel="preload" href="style/form.css" as="style">
				<link rel="stylesheet" href="style/form.css" media="all">
			';
		}

		/**
		 * creating the form's html 
		 * @param string $method - post | get - the form's html method attribute
		 * @param string $action - the url the form should use for its submission
		 * you can leave it empty and the form's built in function will run
		 * @param string $name - the form's html name attribute
		 * @param string $class - you can add a unique class to the form
		 * if you want to write your own stlye for the form on top of the existing design
		 * @return string of a html form
		*/
		public function showForm(string $method, string $action,string $name, string $class){
			$this->setFormElements();
			$this->formHTML = "
				<form method='{$method}' action='{$action}' name='{$name}' class='formMDL {$class}'>
					{$this->formInput}
				</form>
			";
			return $this->formHTML;
		}

		/**
		 * creating the form's input elements based on the input element array
		 * @return string of html elements
		*/
		protected function setFormElements(){
			foreach($this->input as $row){
				$this->formInput.= $this->{$row->type}($row);
			}
		}

		/**
		 * creating htmlfor simple text input
		 * @param object $row - the input element's object from the input array
		 * @return string of html elements 
		*/
		protected function text(object $row){
			return "
				<div class='textInput {$row->class}'>
					<input type='text' id='{$row->name}' title='{$row->title}' name='{$row->name}' placeholder='{$row->label}' {$row->required} value='{$row->value}'/>
					<label for='{$row->name}'>{$row->label}</label>
				</div>
			";
		}

		/**
		 * creating htmlfor simple email input 
		 * @param object $row - the input element's object from the input array 
		 * @return string of html elements 
		*/
		protected function email(object $row){
			return "
				<div class='emailInput {$row->class}'>
					<input type='email' id='{$row->name}' title='{$row->title}' name='{$row->name}' placeholder='{$row->label}' {$row->required} value='{$row->value}'/>
					<label for='{$row->name}'>{$row->label}</label>
				</div>
			";
		}

		/**
		 * creating htmlfor the form's submit button 
		 * @param object $row - the input element's object from the input array
		 * @return string of html elements 
		*/
		protected function submit(object $row){
			$cap = "";
			if($this->captcha === true){
				$cap = "<input type='hidden' name='recaptcha_response' id='recaptchaResponse'>";
			}
			return "{$cap}
				<button type='submit' data-label='{$row->label}' title='{$row->title}' name='{$row->name}' value='{$row->value}'>{$row->label}</button>
			";
		}

	}