<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 01-Mar-18
 * Time: 3:28 PM
 */

namespace Training\Form;


use Zend\Form\Form;

class AccessForm extends Form
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('method','post');
        $this->addElements();
//        $this->addInputFilter();

    }

    public function addElements(){
        $this->add(array(
            'type' => 'CheckBox',
            'name' => 'usercontroller',
            'attributes' => array(
                'id' => 'user'
            ),
            'options' => array(
                'checked_value' => 'user',
                'unchecked_value' => 'OFF'
            )
        ));

        $this->add(array(
            'name' => 'user',
            'type' => 'MultiCheckBox',
            'attributes' => array(
                'class' => 'useraction',
            ),
            'options' => array(
                'label' => 'User Controller',
                'options' => array(
                    'index' => 'Index Action',
                    'add' => 'Add Action',
                    'edit' => 'Edit Action',
                    'delete' => 'Delete Action',
                )
            )
        ));

        $this->add(array(
            'type' => 'CheckBox',
            'name' => 'filecontroller',
            'attributes' => array(
                'id' => 'file'
            ),
            'options' => array(
                'checked_value' => 'file',
                'unchecked_value' => 'OFF'
            )
        ));

        $this->add(array(
            'name' => 'file',
            'type' => 'MultiCheckBox',
            'attributes' => array(
                'class' => 'fileaction',
            ),
            'options' => array(
                'label' => 'File Controller',
                'options' => array(
                    'index' => 'Index Action',
                    'add' => 'Add Action',
                    'edit' => 'Edit Action',
                    'delete' => 'Delete Action',
                    'download' => 'Download Action',
                    'share' => 'Share Action',
                    'removeShare' => 'Remove Share Action'
                )
            )
        ));

            // chat controller
        $this->add(array(
            'type' => 'CheckBox',
            'name' => 'chatcontroller',
            'attributes' => array(
                'id' => 'chat'
            ),
            'options' => array(
                'checked_value' => 'chat',
                'unchecked_value' => 'OFF'
            )
        ));

        $this->add(array(
            'name' => 'chat',
            'type' => 'MultiCheckBox',
            'attributes' => array(
                'class' => 'chataction',
            ),
            'options' => array(
                'label' => 'Chat Controller',
                'options' => array(
                    'index' => 'Index Action',
                    'listMessage' => 'List Message Action',
                )
            )
        ));


        //book controller
        $this->add(array(
            'type' => 'CheckBox',
            'name' => 'bookcontroller',
            'attributes' => array(
                'id' => 'book'
            ),
            'options' => array(
                'checked_value' => 'book',
                'unchecked_value' => 'OFF'
            )
        ));

        $this->add(array(
            'name' => 'book',
            'type' => 'MultiCheckBox',
            'attributes' => array(
                'class' => 'bookaction',
            ),
            'options' => array(
                'label' => 'Book Controller',
                'options' => array(
                    'index' => 'Index Action',
                    'addItem' => 'Add Item Action',
                    'cart' => 'Cart Action',
                    'updateItem' => 'Update Item Action',
                    'removeAll' => 'Remove All Action',
                    'removeItem' => 'Remove Item Action',
                    'checkout' => 'Checkout Action',
                    'paymentConfirm' => 'Payment Confirm Action',
                    'historyPayment' => 'History Payment  Action',
                    'orderDetail' => 'Order Detail Action',

                )
            )
        ));


        $this->add(
            array(
                'type' => 'submit',
                'name' => 'submit',
                'attributes' => array(
                    'value' => 'Update',
                    'class' => 'btn btn-primary'
                )
            )
        );
    }
}