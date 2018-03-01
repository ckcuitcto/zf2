<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 26-Feb-18
 * Time: 4:26 PM
 */
return array(
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
                            'route' => '/file[/:action[/:id[/:token]]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                                'token' => '[a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                'controller' => 'Training\Controller\File',
                                'action' => 'index'
                            ),
                        ),
                    ),
                    'book' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/book[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Training\Controller\Book',
                                'action' => 'index'
                            ),
                        ),
                    ),
                    'acl' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/acl[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Training\Controller\Acl',
                                'action' => 'index'
                            ),
                        ),
                    )
                ),
            ),
        ),
    ),
);