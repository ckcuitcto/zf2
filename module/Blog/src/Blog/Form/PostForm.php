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

class PostForm extends Form
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
            'name' => 'title',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Tiêu đề',
            )
        ));

        $this->add(array(
            'name' => 'info',
            'type' => 'textarea',
            'attributes' => array(
                'class' => 'form-control',
                'rows' => 5,
                'id' => 'infoEditor',
            ),
            'options' => array(
                'label' => 'Mô tả',
            )
        ));

        $this->add(array(
            'name' => 'content',
            'type' => 'textarea',
            'attributes' => array(
                'class' => 'form-control',
                'rows' => 15,
                'id' => 'contentEditor',
            ),
            'options' => array(
                'label' => 'Chi tiết',
            )
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'radio',
            'attributes' => array(
                'value' => 1,
            ),
            'options' => array(
                'label' => 'Tình trạng',
                'options' => array(
                    '2' => 'Duyệt',
                    '1' => 'Chưa duyệt'
                )
            )
        ));

        $this->add(array(
            'name' => 'cate_id',
            'type' => 'select',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Thể loại',
            )
        ));

        $this->add(array(
            'name' => 'tags',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Từ khoá',
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
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validator' => array(
                array(
                    'name' => 'NotEmpty',
                ),
            )
        ));
        $input->add(array(
            'name' => 'info',
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
        $input->add(array(
            'name' => 'content',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validator' => array(
                array(
                    'name' => 'NotEmpty',
                ),
            )
        ));
    }
}