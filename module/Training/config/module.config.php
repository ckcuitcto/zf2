<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Training\Controller\User' => 'Training\Controller\UserController',
            'Training\Controller\Verify' => 'Training\Controller\VerifyController',
            'Training\Controller\Chat' => 'Training\Controller\ChatController',
            'Training\Controller\File' => 'Training\Controller\FileController',
            'Training\Controller\Book' => 'Training\Controller\BookController',
            'Training\Controller\Acl' => 'Training\Controller\AclController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Training' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/Training/layout/layout.phtml',
            'layout/auth' => __DIR__ . '/../view/Training/layout/auth.phtml',
        )
    ),
    'smtp_config' => array(
        'username' => "hoasaigonn@gmail.com",
        'password' => "giahanthaiduc",
        'ssl' => 'ssl'
    ),
    'recaptcha' => array(
        'public' => '6Lf65EcUAAAAAHfsKWZj72FJFVflgebWAhZVkJNk',
        'private' => '6Lf65EcUAAAAAExw1XDcvmeeTrXrRTCIgIQdof2U',
    ),
    'upload_location' => dirname(__DIR__)."/../../data/upload",
    'paypal-api' => array(
        'username'      => 'thducit_api1.gmail.com',
        'password'      => '67UNTT6A6DN7AF2K',
        'signature'     => 'AvRtU8qMo4nSlG1gmXBDr-ZpipoZA06zS8IqINXAwY01uaPRK3mUnbTS',
        'endpoint'      => 'https://api-3t.sandbox.paypal.com/nvp' //this is sandbox endpoint
    )

);
