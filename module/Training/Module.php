<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Training;

use Training\Form\FileForm;
use Training\Form\ShareForm;
use Training\Form\VerifyForm;
use Training\Model\FileTable;
use Training\Model\MyAuth;
use Training\Model\UserTable;
use Zend\Authentication\AuthenticationService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $share = $eventManager->getSharedManager();
        $share->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function ($e) {
            $controller = $e->getTarget(); // lam viec voi controller
            // lay ra controller hien tai, kiem tra xem no co lien quan j voi verifycontroller, neu có nghĩa là
            // ng sử dụng đang đứng trong các action thược verìy controller
            if ($controller instanceof Controller\VerifyController) {
                $controller->layout('layout/auth');
            } else {
                $auth = $e->getApplication()->getServiceManager()->get('AuthService');
                $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
                $userLogin = $auth->getStorage()->read();
                $viewModel->username_layout = $userLogin['username'];
                //ktra ng dung dang nhap
                if (!$auth->hasIdentity()) {
                    $controller->plugin('redirect')->toRoute('training/verify', array('action' => 'login'));
                }
            }
        });
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'UserTableGateWay' => function ($sm) {
                    $db = $sm->get('Zend\Db\Adapter\Adapter');
                    $result = new ResultSet();
                    $result->setArrayObjectPrototype(new \Training\Model\User());
                    return new TableGateway('Users', $db, null, $result);
                },
                'UserTable' => function ($sm) {
                    $tableGateWay = $sm->get('UserTableGateWay');
                    $userTable = new UserTable($tableGateWay);
                    return $userTable;
                },
                'UserForm' => function ($sm) {
                    $form = new \Training\Form\UserForm('User_Form');
                    $user = new \Training\Model\User();
                    $form->setInputFilter($user->getInputFilter());
                    return $form;
                },
                'VerifyForm' => function ($sm) {
                    $form = new VerifyForm('Login_Form');
                    return $form;
                },
                'AuthService' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $authAdapter = new DbTableAuthAdapter\CredentialTreatmentAdapter($adapter, 'users', 'username', 'password', 'MD5(?)');
                    $auth = new AuthenticationService();
                    $auth->setAdapter($authAdapter);
                    return $auth;
                },
                'MyAuth' => function ($sm) {
                    $auth = new MyAuth();
                    return $auth;
                },
                'ChatsTableGateWay' => function($sm){
                    $db = $sm->get('Zend\Db\Adapter\Adapter');
                    return new TableGateway('chats',$db);
                },
                'FileForm' => function ($sm) {
                    $form = new FileForm('File_Form');
                    return $form;
                },
                'ShareForm' => function ($sm) {
                    $form = new ShareForm('Share_Form');
                    return $form;
                },
                'FileTableGateWay' => function($sm){
                    $db = $sm->get('Zend\Db\Adapter\Adapter');
                    $result = new ResultSet();
                    $result->setArrayObjectPrototype(new \Training\Model\File());
                    return new TableGateway('files', $db, null, $result);
                },
                'ShareTableGateWay' => function($sm){
                    $db = $sm->get('Zend\Db\Adapter\Adapter');
                    return new TableGateway('sharings', $db);
                },
                'FileTable' => function($sm){
                    $tableGateWay = $sm->get('FileTableGateWay');
                    $shareTableGateWay = $sm->get('ShareTableGateWay');
                    return new FileTable($tableGateWay,$shareTableGateWay);
                }
            )
        );
    }
}
