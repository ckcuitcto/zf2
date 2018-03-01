<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 17-Feb-18
 * Time: 8:45 PM
 */

namespace Training\Controller;

use QHO\Mail\MailManager;
use QHO\Mail\MailMessage;
use Training\Form\UserForm;
use Training\Model\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    public function demoMailAction()
    {
        $sm = $this->getServiceLocator();
        $mail = $sm->get('MailManager');

        // lấy message ở trong mailmessage ra.
        $mess = array(
            'username' => 'thaiduc1',
            'link' => 'hoasaigon.tk'
        );
        $message = new MailMessage();
        $message->forgotPasswordMessage($mess);

        $data = array(
            'mailFrom' => 'hoasaigonn@gmail.com',
            'nameFrom' => 'Thai Duc Test mail library',
            'emailTo' => 'huynhjduc248@gmail.com',
            'emailName' => ' thai duc',
            'subject' => 'kiem tra gui thu vien mail',
            'message' => $message->getMessageInfo(),
        );
        $mail->setDataMailer($data);
        $mail->getSmtpTransport()->send($mail->getDataMailer());
        echo "Done";
        return false;
    }

    public function indexAction()
    {
        $qho = new \QHO\Demo\Test();
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $paging = $userTable->fetchAll(true);
        $page = $this->params()->fromRoute('page', 1);
        $paging->setCurrentPageNumber($page);
        $paging->setItemCountPerPage(3);

        $view = $sm->get('Zend\View\Renderer\PhpRenderer');
        $view->headScript()->appendFile($view->basePath() . "/js/script.js", 'text/javascript');

        $flash = $this->flashMessenger()->getMessages();
        return new ViewModel(array('data' => $paging, 'flash' => $flash));
    }

    //tạo index2 làm phân trang kiểu khác
    public function index2Action()
    {
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $totalRecord = $userTable->countAllUser();
        $page = $this->params()->fromRoute('page', 1);

        $options = array(
            'ItemCountPerPage' => '3',
            'CurrentPageNumber' => $page
        );

        $dataUser = $userTable->listUserWithPaging($options);
        /*
        $adapter = new \Zend\Paginator\Adapter\Null($totalRecord);
        $paging = new \Zend\Paginator\Paginator($adapter);
        $paging->setCurrentPageNumber($page);
        $paging->setItemCountPerPage($options['ItemCountPerPage']);
        */
        $paginator = $sm->get('DataPaging');
        $paging = $paginator->make($totalRecord,$options);

        $view = $sm->get('Zend\View\Renderer\PhpRenderer');
        $view->headScript()->appendFile($view->basePath() . "/js/script.js", 'text/javascript');

        $flash = $this->flashMessenger()->getMessages();

        // dữ liệu dử dụng là dataUser. phân trang để hiển thị dnah sách là $paging
        return new ViewModel(array('data' => $dataUser, 'paging' => $paging));
    }

    public function addAction()
    {
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $form = $sm->get('FormElementManager')->get('UserForm');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $user = new User();
                $user->exChangeArray($data);
                $userTable->saveUser($user);
                $this->flashMessenger()->addMessage('Thêm thành công ');

                return $this->redirect()->toRoute('training/member', array('action' => 'index'));
            }
        }
        return new ViewModel(array('form' => $form));
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $form = $sm->get('FormElementManager')->get('UserForm');
        $dataUser = $userTable->getUserById($id);
        $form->bind($dataUser);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if ($data->password == "" && $data->repassword == "") {
                $form->getInputFilter()->remove('password');
                $form->getInputFilter()->remove('repassword');
                unset($dataUser->password);
            }
            $form->setData($data);
            if ($form->isValid()) {
                $dataOk = $form->getData();
                $userTable->saveUser($dataOk);

                $this->flashMessenger()->addMessage("Cập nhật thành công");
                return $this->redirect()->toRoute('training/member');
            }
        }


        return new ViewModel(array('form' => $form, 'userId' => $id));
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $userTable->deleteUserById($id);

        $this->flashMessenger()->addMessage('Xoá thành viên thành công');
        return $this->redirect()->toRoute('training/member');
    }

    public function accessAction(){
        $userId = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $form = $sm->get('FormElementManager')->get('AccessForm');
        $userTable = $sm->get('UserTable');
        $userInfo = $userTable->getUserById($userId);
        $serialize = new \Zend\Serializer\Adapter\PhpSerialize();
        if($userInfo->access != ""){
            // nghĩa là đã đc phân quyền
            $access = $serialize->unserialize($userInfo->access);
            foreach ($access['training'] as $key => $value){
                $form->get($key."controller")->setValue($key);
                $form->get($key)->setValue($value);
            }
        }
        $request = $this->getRequest();
        if($request->isPost()){
            $data =$request->getPost()->toArray();
            $access = array();
            foreach ($data as $key => $value){
                if(!is_array($value)){
                    unset($data[$key]);
                }
            }
            $access['training'] = $data;
            $strAccess = $serialize->serialize($access);

            $userTable->saveAccess($strAccess,$userId);
            $userInfo = $userTable->getUserById($userId);
            $this->flashMessenger()->addMessage("Phân quyền thành công cho thành viên $userInfo->username !");
            return $this->redirect()->toRoute('training/member');
        }
        return new ViewModel(array('form' => $form,'userId' => $userId));
    }

}