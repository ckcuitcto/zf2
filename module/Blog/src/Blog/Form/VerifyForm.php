<?php
namespace Blog\Form;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
class VerifyForm extends Form{
	public function __construct($name){
		parent::__construct($name);
		$this->setAttribute('method','post');
		$this->addElements();
		$this->addInputFilters();
	}
	public function addElements(){
		$this->add(array(
				'type' => 'text',
				'name' => 'username',
				'attributes' => array(
						'class' => 'form-control'
					),
				'options' => array(
						'label' => 'Username'
					),
			));
		$this->add(array(
				'type' => 'password',
				'name' => 'password',
				'attributes' => array(
						'class' => 'form-control'
					),
				'options' => array(
						'label' => 'Password'
					),
			));	
		$this->add(array(
				'type' => 'submit',
				'name' => 'submit',
				'attributes' => array(
						'value' => 'Submit',
						'class' => 'btn btn-primary'
					),
			));	
	}
	public function addInputFilters(){
		$input=new InputFilter;
		$this->setInputFilter($input);
		$input->add(array(
				'name' => 'username',
				'required' => true,
				'filters' => array(
						array('name' => 'StringTrim'),
						array('name' => 'StripTags')
					),
				'validators' => array(
						array('name' => 'NotEmpty')
					),
			));
		$input->add(array(
				'name' => 'password',
				'required' => true,
				'filters' => array(
						array('name' => 'StringTrim'),
						array('name' => 'StripTags')
					),
				'validators' => array(
						array('name' => 'NotEmpty')
					),
			));	

	}

}