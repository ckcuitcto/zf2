<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 01-Mar-18
 * Time: 11:27 PM
 */
return array(
    'navigation' => array(
        // mặc định. chưa đăng nhập
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'application',
            ),
            array(
                'label' => 'Login',
                'route' => 'training/verify',
            )
        ),
        //đăng nhập thànhcông
        'member' => array(
            array(
                'label' => 'Home',
                'route' => 'application',
            ),
            array(
                'label' => 'QL Thành viên',
                'route' => 'training/member',
                'resource' => 'training:user', //
                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => 'List Member',
                        'route' => 'training/member',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Add Member',
                        'route' => 'training/member',
                        'action' => 'add'
                    ),
                    array(
                        'label' => 'Edit Member',
                        'route' => 'training/member',
                        'action' => 'edit'
                    ),
                    array(
                        'label' => 'Access Member',
                        'route' => 'training/member',
                        'action' => 'access'
                    ),
                )
            ),
            array(
                'label' => 'Book store',
                'route' => 'training/book',
                'resource' => 'training:book', //
                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => 'List Book',
                        'route' => 'training/book',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'View Cart',
                        'route' => 'training/book',
                        'action' => 'cart'
                    ),
                    array(
                        'label' => 'Payment Confirm',
                        'route' => 'training/book',
                        'action' => 'paymentConfirm'
                    ),
                    array(
                        'label' => 'History Payment',
                        'route' => 'training/book',
                        'action' => 'historyPayment'
                    ),
                    array(
                        'label' => 'Order Detail',
                        'route' => 'training/book',
                        'action' => 'orderDetail'
                    ),
                )
            ),
            array(
                'label' => 'QL File',
                'route' => 'training/file',
                'resource' => 'training:file', //
                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => 'File',
                        'route' => 'training/file',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Share File',
                        'route' => 'training/file',
                        'action' => 'share'
                    ),
                    array(
                        'label' => 'Add File',
                        'route' => 'training/file',
                        'action' => 'add'
                    ),
                    array(
                        'label' => 'Edit File',
                        'route' => 'training/file',
                        'action' => 'edit'
                    ),
                ),
            ),
            array(
                'label' => 'Chat',
                'route' => 'training/chat',
                'resource' => 'training:chat', //
                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Chat room',
                        'route' => 'training/chat',
                        'action' => 'index'
                    ),
                )
            ),
//            array(
//                'label' => 'Logout',
//                'route' => 'training/verify',
//                'action' => 'logout'
//            ),
        ),
    )
);