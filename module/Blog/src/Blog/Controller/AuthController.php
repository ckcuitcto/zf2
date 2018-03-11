<?php

namespace Blog\Controller;

use Blog\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends MainController
{

    public static function verifyCredential(User $user, $inputPassword)
    {
        return password_verify(md5($inputPassword), $user->getPassword());
    }

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $authService = $this->getServiceLocator()->get('ZendAuth');
        if($authService->hasIdentity()){
            $authInfo = $authService->getIdentity();
            echo "<pre>";
            print_r($authInfo);
            echo "</pre>";

        }else{
            $this->flashMessenger()->addMessage("Vui lòng đăng nhập");
            return $this->redirect()->toRoute('blog/auth',array('action'=>'login'));
        }
        return false;
    }

    public function loginAction()
    {
        $sm = $this->getServiceLocator();
        $form = $sm->get('FormElementManager')->get('AuthForm');
        $request = $this->getRequest();
        $error = "";
        $flash = $this->flashMessenger()->getMessages();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $dataInput = $form->getData();
                $authService = $sm->get('ZendAuth');
                $authService->getAdapter()
                    ->setIdentity($data['username'])
                    ->setCredential($data['password']);
                $authResult = $authService->authenticate();
                if ($authResult->isValid()) {
                    $identity = $authResult->getIdentity();
                    $authService->getStorage()->write($identity);
                    return $this->redirect()->toRoute('blog/auth');
                }else{
                    $error = "Wrong username or  password";
                }
            }
        }
        return new ViewModel(array('form' => $form, 'error' => $error, 'flash' => $flash,'errors'=>$error));
    }

    public function logoutAction()
    {
        $sm = $this->getServiceLocator();
        $authService = $sm->get('ZendAuth');
        $authService->clearIdentity();

        $this->flashMessenger()->addMessage('Đăng xuất thành công');
        return $this->redirect()->toRoute('blog/auth', array('action' => 'login'));
    }

}