<?php 

namespace Training\Form;


use Zend\Form\Element\File;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;

class ShareForm extends Form
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements(){
    	$this->add(
    		array(
    			'type' => 'select',
    			'name' => 'user_id',
    			'attributes' => array(
    				'class' => 'form-control'
    			),
    			'options' => array(
    				'label' => 'Username'
    			)
    		)
    	);

    	$this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Submit'
            ),
        ));
    }

    public function addInputFilter(){
    	$input = new InputFilter();
    	$this->setInputFilter($input);
    	$input->add( array(
    		'name' => 'user_id',
    		'required' => true
    	));
    }
}
?>