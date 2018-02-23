<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=zf2online;host=localhost',
        'username' => 'root',
        'password' => '',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'adapters' => array(
            'adapter1' => array(
                'driver' => 'Pdo',
                'dsn' => 'mysql:dbname=zf2online;host=localhost',
                'username' => 'root',
                'password' => '',
                'driver_options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                )
            ),
            'adapter2' => array(
                'driver' => 'Pdo',
                'dsn' => 'mysql:dbname=hrm;host=localhost',
                'username' => 'root',
                'password' => '',
                'driver_options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                )
            ),
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function($sm){
                $adapterFactory = new Zend\Db\Adapter\AdapterServiceFactory();
                $adapter = $adapterFactory->createService($sm);
                \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($adapter);
                return $adapter;
            },
        ),
    )
);
