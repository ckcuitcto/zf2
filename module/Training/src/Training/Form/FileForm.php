<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 23-Feb-18
 * Time: 5:15 PM
 */

namespace Training\Form;


use Zend\Form\Element\File;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;

class FileForm extends Form
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->addElements();
        $this->addInputFilter();
    }

    public function addElements(){

        $this->add(
            array(
                'type' => 'text',
                'name' => 'label',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'label' => 'Tên tập tin',
                )
            )
        );
//        $this->add(
//            array(
//                'name' => 'filename',
//                'type' => 'number',
//                'attributes' => array(
//                    'class' => 'form-control',
//                    'multiple' => 'multiple',
//                ),
//                'options' => array(
//                    'label' => 'Tập tin',
//                )
//            )
//        );
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Submit'
            ),
        ));

        $file = new File('file');
        $file->setLabel('Tập tin')
            ->setAttribute('id', 'file');
        $this->add($file);
    }

    public function addInputFilter(){
        $input = new InputFilter();
        $this->setInputFilter($input);
        $input->add(array(
            'name' => 'label',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validator' => array(
                array('name' => 'NotEmpty'),
            ),
        ));

//        $input->add(array(
//           'name' => 'file',
//            'required' => true,
//        ));

        $inputFilter = new InputFilter();
        // File Input
        $fileInput = new FileInput('file');
        $fileInput->setRequired(true);

        // You only need to define validators and filters
        // as if only one file was being uploaded. All files
        // will be run through the same validators and filters
        // automatically.
        $fileInput->getValidatorChain()
            ->attachByName('filesize',      array('max' => 204800))
            ->attachByName('filemimetype',  array('mimeType' => 'image/png,image/x-png'))
            ->attachByName('fileimagesize', array('maxWidth' => 100, 'maxHeight' => 100));

        // All files will be renamed, i.e.:
        //   ./data/tmpuploads/avatar_4b3403665fea6.png,
        //   ./data/tmpuploads/avatar_5c45147660fb7.png
        $fileInput->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'    => './data/upload',
                'randomize' => true,
            )
        );

    }
}