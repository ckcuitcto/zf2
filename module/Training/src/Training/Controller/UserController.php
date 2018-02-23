<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 17-Feb-18
 * Time: 8:45 PM
 */
namespace Training\Controller;

use Training\Form\UserForm;
use Training\Model\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $paging = $userTable->fetchAll(true);
        $page = $this->params()->fromRoute('page',1);
        $paging->setCurrentPageNumber($page);
        $paging->setItemCountPerPage(3);

        $view = $sm->get('Zend\View\Renderer\PhpRenderer');
        $view->headScript()->appendFile($view->basePath()."/js/script.js",'text/javascript');

        $flash = $this->flashMessenger()->getMessages();
        return new ViewModel(array('data'=>$paging,'flash' => $flash));
    }

    public function addAction(){
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $form = $sm->get('UserForm');
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $form->setData($data);
            if($form->isValid()){
                $data = $form->getData();
                $user = new User();
                $user->exChangeArray($data);
                $userTable->saveUser($user);
                $this->flashMessenger()->addMessage('Thêm thành công ');

                return $this->redirect()->toRoute('training/member',array('action'=> 'index'));
            }
        }
        return new ViewModel(array('form' => $form));
    }

    public function editAction(){
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $form = $sm->get('UserForm');
        $dataUser = $userTable->getUserById($id);
        $form->bind($dataUser);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            if($data->password == "" && $data->repassword ==""){
                $form->getInputFilter()->remove('password');
                $form->getInputFilter()->remove('repassword');
                unset($dataUser->password);
            }
            $form->setData($data);
            if($form->isValid()){
                $dataOk = $form->getData();
                $userTable->saveUser($dataOk);

                $this->flashMessenger()->addMessage("Cập nhật thành công");
                return $this->redirect()->toRoute('training/member');
            }
        }


        return new ViewModel(array('form' => $form, 'userId' => $id));
    }

    public function deleteAction(){
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $userTable->deleteUserById($id);

        $this->flashMessenger()->addMessage('Xoá thành viên thành công');
        return $this->redirect()->toRoute('training/member');
    }

}