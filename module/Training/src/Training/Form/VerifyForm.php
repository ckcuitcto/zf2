<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 19-Feb-18
 * Time: 5:15 PM
 */

namespace Training\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class VerifyForm extends Form
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method','post');
        $this->addElements();
    }

    public function addElements(){
        $this->add(
            array(
                'type' => 'text',
                'name' => 'username',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'label' => 'Username',
                )
            )
        );

        $this->add(
            array(
                'type' => 'password',
                'name' => 'password',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'label' => 'Password',
                )
            )
        );

        $this->add(
            array(
                'type' => 'checkbox',
                'name' => 'remember',
//                'attributes' => array(
//                    'class' => 'form-control',
//                ),
                'options' => array(
                    'label' => 'Remember me',
                    'use_hidden_element' => true, // nếu ng dùng k lựa chọn thì mặc định là gì,
                    'checked_value' => 1,
                    'unchecked_value' => 0,
                )
            )
        );

        $this->add(
            array(
                'type' => 'text',
                'name' => 'email',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'label' => 'Email'
                ),
            )
        );

        $this->add(
            array(
                'type' => 'submit',
                'name' => 'submit',
                'attributes' => array(
                    'value' => 'Login',
                    'class' => 'btn btn-primary'
                )
            )
        );
    }

    public function addInputFilerLogin(){
        $input = new InputFilter();
        $this->setInputFilter($input);

        $input->add(
            array(
                'name' => 'username',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                )
            )
        );

        $input->add(
            array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                )
            )
        );
    }

    public function addInputFilerForgot(){
        $input = new InputFilter();
        $this->setInputFilter($input);

        $input->add(
            array(
                'name' => 'username',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                )
            )
        );

    }
}