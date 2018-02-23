<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 19-Feb-18
 * Time: 5:09 PM
 */

namespace Training\Controller;


use Zend\Captcha\ReCaptcha;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;

class VerifyController extends AbstractActionController
{
    protected $authService;
    protected $myAuth;
    protected $smtp;

       public function getMyAuth(){
            if (empty($this->myAuth)){
                $this->myAuth = $this->getServiceLocator()->get('MyAuth');
            }
            return $this->myAuth;
        }

    public function getAuthService()
    {
        if (empty($this->authService)) {
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authService;
    }

    protected function getSmtpTransport(){
        if(!$this->smtp){
            $config = $this->getServiceLocator()->get('config');
            $transport = new SmtpTransport();
            $option = new Mail\Transport\SmtpOptions(array(
                'name' => 'smtp.gmail.com',
                'host' => 'smtp.gmail.com',
                'port' => 465,
                'connection_class' =>  'login',
                'connection_config' => $config['smtp_config']
                )
            );
            $transport->setOptions($option);
            $this->smtp = $transport;

        }
        return $this->smtp;
    }

    public function indexAction()
    {
        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('training/member');
        } else {
            $this->flashMessenger()->addMessage('Vui lòng đăng nhập để truy cập vào hệ thống');
        }
        return $this->redirect()->toRoute('training/verify', array('action' => 'login'));
    }

    public function loginAction()
    {
        $sm = $this->getServiceLocator();
        $form = $sm->get('VerifyForm');
        $request = $this->getRequest();
        $userTable = $sm->get('UserTable');
        $error = "";
        $flash = $this->flashMessenger()->getMessages();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->addInputFilerLogin();
            $form->setData($data);
            if ($form->isValid()) {
                var_dump($data);
                $dataInput = $form->getData();
                $this->getAuthService()->getAdapter()->setIdentity($dataInput['username'])->setCredential($dataInput['password']);
                $result = $this->getAuthService()->authenticate();
                if ($result->isValid()) {
                    if($dataInput['remember'] == 1){
                        $this->getMyAuth()->setRememberMe($dataInput['remember']);
                        $this->getAuthService()->setStorage($this->getMyAuth()); // khi set lại thời gian thì phải set lại storage để truyền lại những đối
                        // tượng vừa thiết lập
                    }
                    $user = $userTable->getUserByUsername($dataInput['username']);
                    $storage = array(
                        'username' => $dataInput['username'],
                        'level' => $user->level,
                        'id' => $user->id,
                    );
                    $this->getAuthService()->getStorage()->write($storage);
                    return $this->redirect()->toRoute('training/member');
                } else {
                    $error = "Wrong username or password";
                }
            }else{
                var_dump($data);
            }
        }else{
            echo 1;
        }
    return new ViewModel(array('form' => $form, 'error' => $error, 'flash' => $flash));
    }

    public function logoutAction()
    {
        $this->getAuthService()->clearIdentity();
        $this->getMyAuth()->forgetMe();
        $this->flashMessenger()->addMessage('Đăng xuất thành công');

        return $this->redirect()->toRoute('training/verify', array('action', 'login'));
    }

    public function forgotAction(){
        $sm = $this->getServiceLocator();
        $form = $sm->get('VerifyForm');
        $request = $this->getRequest();
        $error = "";
        $mess = "";
//        $capcha = new ReCaptcha();
//        $capcha->setPrivkey('6Lf65EcUAAAAAExw1XDcvmeeTrXrRTCIgIQdof2U');
//        $capcha->setPubkey('6Lf65EcUAAAAAHfsKWZj72FJFVflgebWAhZVkJNk');

//        $view = $sm->get('Zend\View\Renderer\PhpRenderer');
//        $view->headScript()->appendFile("https://www.google.com/recaptcha/api.js",'text/javascript');

        if($request->isPost()){
            $data = $request->getPost();
            $form->addInputFilerForgot();
            $form->setValidationGroup('email'); // khi u=dùng chung form, dùng hàm này để xác định các input cần validate
            $form->setData($data);

            if($form->isValid()){
                $dataInput = $form->getData();
                $userTable = $sm->get('UserTable');
                $row = $userTable->getUserByEmail($dataInput['email']);
                var_dump($dataInput);
                if($row){
                    $activeCode = md5($row->username."ThaiDuc".$row->password);
                    $link = $this->url()->fromRoute('training/verify', array('action' => 'active'))."/getinfo/$row->username/$activeCode";

                    $mail = new Mail\Message();
                    $mail->setFrom('hoasaigonn@gmail.com','Thai Duc Zend2 ');
                    $mail->addTo($row->email,$row->name);
                    $mail->setSubject("Đổi mật khẩu zend 2");
                    $mail->setBody("Nhâp vào lin kđể phục hồi tài khoản http://zend2.local$link");

                    $this->getSmtpTransport()->send($mail);;
//                    $transport->send($mail);

                    $mess = "Chúng tôi đã gửi email chứa liên kết phục hồi mật khẩu tới địa chỉ $dataInput[email]";
                }else{
                    $error = "Email không tồn tại";
                }
            }
        }
        return new ViewModel(array('form' => $form, 'mess' => $mess , 'error' => $error));
    }

    public function activeAction(){
        echo 'reset';
        return false;
    }

}