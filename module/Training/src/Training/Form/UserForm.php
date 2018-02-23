<?php
    namespace Training\Form;

    use Zend\Form\Form;
    use Zend\InputFilter\InputFilter;

    class UserForm extends Form{
        public function __construct($name)
        {
            parent::__construct($name);
            $this->setAttribute('method','post');

            $this->addElements();
//            $this->addInputFiler();
        }

        public function addElements(){
            $this->add(
                array(
                    'name' => 'username',
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                    ),
                    'options' => array(
                        'label' => 'User Name',
                    )
                )
            );

            $this->add(
                array(
                    'name' => 'email',
                    'type' => 'email',
                    'attributes' => array(
                        'class' => 'form-control',
                    ),
                    'options' => array(
                        'label' => 'Email',
                    )
                )
            );

            $this->add(
                array(
                    'name' => 'name',
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control',
                    ),
                    'options' => array(
                        'label' => 'Full Name',
                    )
                )
            );

            $this->add(
                array(
                    'name' => 'password',
                    'type' => 'password',
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
                    'name' => 'repassword',
                    'type' => 'password',
                    'attributes' => array(
                        'class' => 'form-control',
                    ),
                    'options' => array(
                        'label' => 'Re-Password',
                    )
                )
            );
            $this->add(
                array(
                    'name' => 'level',
                    'type' => 'select',
                    'attributes' => array(
                        'class' => 'form-control',
                    ),
                    'options' => array(
                        'label' => 'Level',
                        'options' => array(
                            '1' => 'Member',
                            '2' => 'Administrator'
                        ),
                    )
                )
            );

            $this->add(
                array(
                    'name' => 'submit',
                    'type' => 'submit',
                    'attributes' => array(
                        'class' => 'btn btn-primary',
                        'value' =>'Submit'
                    ),
                )
            );
        }

//        public function addInputFiler(){
//            $input = new InputFilter();
//            $this->setInputFilter($input);
//
//            $input->add(array(
//                'name' => 'username',
//                'required' => true,
//                'filters' => array(
//                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags')
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'Db\NoRecordExists',
//                        'options' => array(
//                            'table' => 'users',
//                            'field' => 'username',
//                            'adapter' => \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter(),
//                            'messages' => array(
//                                'recordFound' => 'Tên truy cập đã tồn tại',
//                            )
//                        )),
//                    array(
//                        'name' => 'StringLength',
//                        'options' => array(
//                            'min' => '3',
//                            'max' => '20',
//                            'messages' => array(
//                                'stringLengthTooShort' => 'Tên truy cập không được ít hơn %min% kí tự',
//                                'stringLengthTooLong' => 'Tên truy cập không được vượt quá %max% kí tự',
//                            )
//                    )),
//                    array(
//                        'name' => 'NotEmpty',
//                        'options' => array(
//                            'messages' => array(
//                                'isEmpty' => 'Tên truy cập không được rỗng',
//                            )
//                        )
//                    )
//                )
//            ));
//
//            $input->add(array(
//                'name' => 'email',
//                'required' => true,
//                'filters' => array(
//                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags')
//                ),
//                'validators' => array(
//                    array('name' => 'EmailAddress'),
//                    array('name' => 'NotEmpty')
//                )
//            ));
//
//            $input->add(array(
//                'name' => 'password',
//                'required' => true,
//                'filters' => array(
//                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags')
//                ),
//                'validators' => array(
//                    array('name' => 'NotEmpty'),
//                    array(
//                        'name' => 'StringLength',
//                        'options' => array(
//                            'min' => 6,
//                            'max' => 32,
//                            'messages' => array(
//                                'stringLengthTooShort' => 'Mật khẩu không được ít hơn %min% kí tự',
//                                'stringLengthTooLong' => 'Mật khẩu  không được vượt quá %max% kí tự',
//                            )
//                        )
//                    ),
//                )
//            ));
//
//            $input->add(array(
//                'name' => 'repassword',
//                'required' => true,
//                'filters' => array(
//                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags')
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'Identical',
//                        'options' => array(
//                            'token' => 'password'
//                        )),
//                )
//            ));
//
//            $input->add(array(
//                'name' => 'name',
//                'required' => true,
//                'filters' => array(
//                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags')
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'NotEmpty',
//                        'options' => array(
//                            'messages' => array(
//                                'isEmpty' => 'Tên truy cập không được rỗng',
//                            )
//                        )
//                    )
//                )
//            ));
//        }
    }