<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 23-Feb-18
 * Time: 1:22 PM
 */

namespace Training\Controller;


use Blog\Controller\MainController;
use Training\Form\ChatForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ChatController extends MainController
{
    protected $authService;

    public function getAuthService()
    {
        if (empty($this->authService)) {
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authService;
    }

    public function getUserInfo()
    {
        return $this->getAuthService()->getStorage()->read();
    }

    public function indexAction()
    {
        $form = new ChatForm('Chat_Form');
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $userData = $this->getUserInfo();
                $dataInput = $form->getData();
                $dataInsert = array(
                    'message' => $dataInput['mess'],
                    'user_id' => $userData['id'],
                );
                $sm->get('ChatsTableGateWay')->insert($dataInsert);
                return $this->redirect()->toRoute('training/chat', array('action' => 'index'));
            }
        }
        return new ViewModel(array('form' => $form));
    }

    public function listMessageAction()
    {
        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $chatTable = $sm->get('ChatsTableGateWay');
//        $chats = $chatTable->select();
//        foreach ($chats as $chat) {
//            $mess['name'] = $userTable->getUserById($chat->user_id)->username;
//            $mess['mess'] = $chat->message;
//            $mess['time'] = $chat->stamp;
//            $data[] = $mess;
//        }
//        $view = new ViewModel(array('mess' => $data));
        $query = $chatTable->getSql()->select();
        $query->join('users','users.id = chats.user_id',array('username'));
        $data = $chatTable->selectWith($query);
        $view = new ViewModel(array('mess' => $data));
        $view->setTerminal(true);
        return $view;
    }

}
