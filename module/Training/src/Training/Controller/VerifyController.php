<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 19-Feb-18
 * Time: 5:09 PM
 */

namespace Training\Controller;


use QHO\Mail\MailMessage;
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
    protected $reCaptcha;

    public function getMyAuth()
    {
        if (empty($this->myAuth)) {
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

    public function getReCaptcha()
    {
        if (empty($this->reCaptcha)) {
            $config = $this->getServiceLocator()->get('config');
            $this->reCaptcha = new \ZendService\ReCaptcha\ReCaptcha($config['recaptcha']['public'], $config['recaptcha']['private']);
        }
        return $this->reCaptcha;
    }

    protected function getSmtpTransport()
    {
        if (!$this->smtp) {
            $config = $this->getServiceLocator()->get('config');
            $transport = new SmtpTransport();
            $option = new Mail\Transport\SmtpOptions(array(
                    'name' => 'smtp.gmail.com',
                    'host' => 'smtp.gmail.com',
                    'port' => 465,
                    'connection_class' => 'login',
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
        $form = $sm->get('FormElementManager')->get('VerifyForm');
        $request = $this->getRequest();
        $userTable = $sm->get('UserTable');
        $error = "";
        $flash = $this->flashMessenger()->getMessages();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->addInputFilerLogin();
            $form->setData($data);
            if ($form->isValid()) {
                $dataInput = $form->getData();
                $this->getAuthService()->getAdapter()->setIdentity($dataInput['username'])->setCredential($dataInput['password']);
                $result = $this->getAuthService()->authenticate();
                if ($result->isValid()) {
                    if ($dataInput['remember'] == 1) {
                        $this->getMyAuth()->setRememberMe($dataInput['remember']);
                        $this->getAuthService()->setStorage($this->getMyAuth()); // khi set lại thời gian thì phải set lại storage để truyền lại những đối
                        // tượng vừa thiết lập
                    }
                    /*
                    $user = $userTable->getUserByUsername($dataInput['username']);
                    $storage = array(
                        'username' => $dataInput['username'],
                        'level' => $user->level,
                        'id' => $user->id,
                    );
                    */
                    // ép kiểu về dạng mảng
                    //
                    $storage = (array) $this->getAuthService()->getAdapter()->getResultRowObject(array('id','username','level'));
                    $this->getAuthService()->getStorage()->write($storage);
                    return $this->redirect()->toRoute('training/member');
                } else {
                    $error = "Wrong username or password";
                }
            }
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

    public function forgotAction()
    {
        $sm = $this->getServiceLocator();

        // capcha v2
//        $view = $sm->get('Zend\View\Renderer\PhpRenderer');
//        $view->headScript()->appendFile("https://www.google.com/recaptcha/api.js", 'text/javascript');
//        $captcha = new \ZendService\ReCaptcha\ReCaptcha('6LeJdkgUAAAAAFraQGv5Im-t4ialBjkSdUE4sWqm', '6LeJdkgUAAAAAJTeyvU9y1XaCEC5SarkZTa0OQDD');

        $form = $sm->get('FormElementManager')->get('VerifyForm');
        $request = $this->getRequest();
        $error = "";
        $mess = "";

        $captcha = $this->getReCaptcha();

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->addInputFilerForgot();
            $form->setValidationGroup('email'); // khi u=dùng chung form, dùng hàm này để xác định các input cần validate
            $form->setData($data);
            $resultCaptcha = $captcha->verify($data['recaptcha_challenge_field'],$data['recaptcha_response_field']);

            if ($form->isValid() AND $resultCaptcha->isValid()) {
                $dataInput = $form->getData();
                $userTable = $sm->get('UserTable');
                $email = $userTable->getUserByEmail($dataInput['email']);

                if ($email) {
                    $activeCode = md5($email->username . "ThaiDuc" . $email->password);
                    $link = "http://localhost:8080". $this->url()->fromRoute('training/verify', array('action' => 'active')) . "/getinfo/$email->username/$activeCode";

                    $mail = $sm->get('MailManager');
                    // lấy message ở trong mailmessage ra.
                    $mess = array(
                        'username' => $email->username,
                        'link' => $link
                    );
                    $message = new MailMessage();
                    $message->forgotPasswordMessage($mess);

                    // dữ liệu email
                    $dataMailer = array(
                        'mailFrom' => 'hoasaigonn@gmail.com',
                        'nameFrom' => 'Thai Duc Test mail library',
                        'emailTo' => $email->email,
                        'emailName' => $email->name,
                        'subject' => 'Đổi mật khẩu zend 2',
                        'message' => $message->getMessageInfo(),
                    );
                    $mail->setDataMailer($dataMailer);
                    $mail->getSmtpTransport()->send($mail->getDataMailer()) ; // gửi

                    $mess = "Chúng tôi đã gửi email chứa liên kết phục hồi mật khẩu tới địa chỉ $dataInput[email]";
                } else {
                    $error = "Email không tồn tại";
                }
            }else{
                $error = 'Mã captcha không chính xác';
            }
        }
        return new ViewModel(array('form' => $form, 'mess' => $mess, 'error' => $error, 'captcha' => $captcha));
    }

    public function activeAction()
    {
        $name = $this->params()->fromRoute('name');
        $code = $this->params()->fromRoute('code');

        $sm = $this->getServiceLocator();
        $userTable = $sm->get('UserTable');
        $row = $userTable->getUserByUsername($name);
        if($row){
            $activeCode = md5($row->username . "ThaiDuc" . $row->password);
            if($code == $activeCode){
                $newPass = substr(str_shuffle('0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM'),0,8);
                $userTable->resetPassword($name,md5($newPass));
                $link = "http://localhost:8080".$this->url()->fromRoute('training/verify', array('action'=>'login'));


                $mail = $sm->get('MailManager');
                $mess = array(
                    'username' => $row->username,
                    'link' => $link,
                    'newPass' => $newPass
                );
                $message = new MailMessage();
                $message->activeCodeMessage($mess);
                $dataMailer = array(
                    'mailFrom' => 'hoasaigonn@gmail.com',
                    'nameFrom' => 'Thai Duc Test mail library',
                    'emailTo' => $row->email,
                    'emailName' => $row->name,
                    'subject' => 'Mật khẩu mới tài khoản zend2 thái đức',
                    'message' => $message->getMessageInfo(),
                );
                $mail->setDataMailer($dataMailer);
                $mail->getSmtpTransport()->send($mail->getDataMailer()) ; // gửi

                $this->flashMessenger()->addMessage('Mật khẩu mới của bạn đã đc gửi tới Email');
                return $this->redirect()->toRoute('training/verify',array('action' => 'login'));
            }
        }
        $form = $sm->get('FormElementManager')->get('VerifyForm');
        $model = new ViewModel(array(
            'form' => $form,
            'error' => 'Liên kết kích hoạt không chính xác',
            'captcha' => $this->getReCaptcha(),
        ));
        $model->setTemplate('training/verify/forgot');
        return $model;
    }

}