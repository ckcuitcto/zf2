<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Training\Controller\User' => 'Training\Controller\UserController',
            'Training\Controller\Verify' => 'Training\Controller\VerifyController',
            'Training\Controller\Chat' => 'Training\Controller\ChatController',
            'Training\Controller\File' => 'Training\Controller\FileController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'training' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/training',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Training\Controller',
                        'controller' => 'User',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                    'member' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/member[/:action[/:id][/page/:page]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                                'page' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Training\Controller\User',
                                'action' => 'index'
                            ),
                        ),
                    ),
                    'verify' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/verify[/:action[/getinfo/:name/:code]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'name' => '[a-zA-Z0-9_-]+',
                                'code' => '[a-zA-Z0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Training\Controller\Verify',
                                'action' => 'index'
                            ),
                        ),
                    ),
                    'chat' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/chat[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Training\Controller\Chat',
                                'action' => 'index'
                            ),
                        ),
                    ),
                    'file' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/file[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Training\Controller\File',
                                'action' => 'index'
                            ),
                        ),
                    )
                ),
            ),
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

);
