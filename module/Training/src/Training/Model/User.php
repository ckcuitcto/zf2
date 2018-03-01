<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 18-Feb-18
 * Time: 12:01 AM
 */

namespace Training\Model;

use Zend\Form\Factory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $password;
    public $email;
    public $level;
    public $username;
    public $access;

    protected $inputFilter;

    public function exChangeArray($data)
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['name'])) {
            $this->name = $data['name'];
        }
        if (isset($data['password'])) {
            $this->password = md5($data['password']);
        }
        if (isset($data['email'])) {
            $this->email = $data['email'];
        }
        if (isset($data['level'])) {
            $this->level = $data['level'];
        }
        if (isset($data['username'])) {
            $this->username = $data['username'];
        }
        if (isset($data['access'])) {
            $this->access = $data['access'];
        }
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception('Not used');
        // TODO: Implement setInputFilter() method.
    }

    public function getArrayCopy(){
        return get_object_vars($this);
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $input = new InputFilter();
            $factory = new \Zend\InputFilter\Factory();

            $input->add(
                $factory->createInput(
                    array(
                        'name' => 'username',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                            array('name' => 'StripTags')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Db\NoRecordExists',
                                'options' => array(
                                    'table' => 'users',
                                    'field' => 'username',
                                    'adapter' => \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter(),
                                    'exclude' => array(
                                        'field' => 'id',
                                        'value' => !is_null($this->id) && !empty($this->id) ? $this->id : 0,
                                    ),
                                    'messages' => array(
                                        'recordFound' => 'Tên truy cập đã tồn tại',
                                    )
                                )),
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'min' => '3',
                                    'max' => '20',
                                    'messages' => array(
                                        'stringLengthTooShort' => 'Tên truy cập không được ít hơn %min% kí tự',
                                        'stringLengthTooLong' => 'Tên truy cập không được vượt quá %max% kí tự',
                                    )
                                )),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        'isEmpty' => 'Tên truy cập không được rỗng',
                                    )
                                )
                            )
                        )
                    )
                )
            );
            $input->add(
                $factory->createInput(
                    array(
                        'name' => 'email',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                            array('name' => 'StripTags')
                        ),
                        'validators' => array(
                            array('name' => 'EmailAddress'),
                            array('name' => 'NotEmpty')
                        )
                    )
                )
            );

            $input->add(
              $factory->createInput(
                  array(
                      'name' => 'password',
                      'required' => true,
                      'filters' => array(
                          array('name' => 'StringTrim'),
                          array('name' => 'StripTags')
                      ),
                      'validators' => array(
                          array('name' => 'NotEmpty'),
                          array(
                              'name' => 'StringLength',
                              'options' => array(
                                  'min' => 6,
                                  'max' => 32,
                                  'messages' => array(
                                      'stringLengthTooShort' => 'Mật khẩu không được ít hơn %min% kí tự',
                                      'stringLengthTooLong' => 'Mật khẩu  không được vượt quá %max% kí tự',
                                  )
                              )
                          ),
                      )
                  )
              )
            );

            $input->add(
                $factory->createInput(
                    array(
                        'name' => 'repassword',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                            array('name' => 'StripTags')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Identical',
                                'options' => array(
                                    'token' => 'password'
                                )),
                        )
                    )
                )
            );

            $input->add(
                $factory->createInput(
                    array(
                        'name' => 'name',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                            array('name' => 'StripTags')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        'isEmpty' => 'Tên truy cập không được rỗng',
                                    )
                                )
                            )
                        )
                    )
                )
            );

            $this->inputFilter = $input;
        }
        return $this->inputFilter;
        // TODO: Implement getInputFilter() method.
    }
}