<?php

	include("includes/autoloader.inc.php");
	$inputList = [
		(object)[
			'name' => 'fname',
			'title' => 'First name',
			'label' => 'First name',
			'required' => 'required',
			'value' => '',
			'options' => [],
			'type' => 'text',
			'class' => 'fname'
		],
		(object)[
			'name' => 'lastname',
			'title' => 'Last name',
			'label' => 'Last name',
			'required' => 'required',
			'value' => '',
			'options' => [],
			'type' => 'text',
			'class' => 'lname'
		],
		(object)[
			'name' => 'email',
			'title' => 'E-mail address',
			'label' => 'E-mail',
			'required' => 'required',
			'value' => '',
			'options' => [],
			'type' => 'email',
			'class' => 'email'
		],
		(object)[
			'name' => 'send',
			'title' => 'Submit form',
			'label' => 'Send',
			'required' => 'required',
			'value' => '',
			'options' => [],
			'type' => 'submit',
			'class' => 'submit'
		],
	];
	$formView = new \Form\FormView($inputList,true);
	$formContrl = new \Form\FormContrl($input,true,"test","test subject","recipient@test.com");
	if($formContrl->captcha === true){
		$cap = new \Captcha\GoogleRecaptcha("secretKey","sitekey");
	}
	echo $formView->loadResources();
	echo $formView->showForm('post','','test','test');