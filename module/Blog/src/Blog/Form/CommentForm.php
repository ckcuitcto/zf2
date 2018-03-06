<?php

/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 03-Mar-18
 * Time: 2:50 PM
 */
namespace Blog\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class CommentForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('method','post');

        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements(){
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Email cuả bạn',
            )
        ));

        $this->add(array(
            'name' => 'content',
            'type' => 'textarea',
            'attributes' => array(
                'class' => 'form-control',
                'rows' => 5,
            ),
            'options' => array(
                'label' => 'Nội dung',
            )
        ));

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
        $input->add(array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validator' => array(
                array(
                    'name' => 'NotEmpty',
                ),
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'domain' => true,
                    )
                )
            )
        ));
        $input->add(array(
            'name' => 'content',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
            'validator' => array(
                array(
                    'name' => 'NotEmpty',
                ),
            )
        ));
    }
}