<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 23-Feb-18
 * Time: 5:10 PM
 */

namespace Training\Controller;


use Blog\Controller\MainController;
use QHO\Mail\MailMessage;
use Training\Model\File;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;

class FileController extends MainController
{
    protected $authService;
    protected $smtp;

    public function getAuthService()
    {
        if (empty($this->authService)) {
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authService;
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

    public function getUserInfo()
    {
        return $this->getAuthService()->getStorage()->read();
    }

    public function getFileLocation()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['upload_location'];
    }
    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $view = $sm->get('Zend\View\Renderer\PhpRenderer');
        $view->headScript()->appendFile($view->basePath()."/js/script.js",'text/javascript');

        $fileTable = $sm->get('FileTable');
        $userInfo = $this->getUserInfo();
        $files = $fileTable->getFileByUserId($userInfo['id']);

        $allShared= $fileTable->getAllSharedByUserId($userInfo['id']);

        $lastFile = $fileTable->getLastFileUpload($userInfo['id']);
        $lastFileTmp = array();
        foreach ($lastFile as $file){
            $fileTmp = array($file,'shared' => $fileTable->checkFileShared($file->id,$userInfo['id']));
            $lastFileTmp[] = $fileTmp;
        }
        $lastFile = $lastFileTmp;
        $flash = $this->flashMessenger()->getMessages();
        return new ViewModel(array('files' => $files,'flash' => $flash,'allShared' => $allShared,'lastFile' => $lastFile));
    }



    public function addAction()
    {
        $sm = $this->getServiceLocator();
        $form = $sm->get('FormElementManager')->get('FileForm');
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {

                $size = new Size(array('min' => '10KB', 'max' => '2MB'));
                $mime = new MimeType(array('image/jpg', 'image/gif', 'application/pdf', 'image/jpeg'));
                $adapter = new \Zend\File\Transfer\Adapter\Http();
//                $fileName = Rand::getString(32, 'abcdefghijklmnopqrstuvwxyz', true) . "_" . $post['file']['name'];
                $adapter->setValidators(array($size, $mime), $post['file']['name']);
                if ($adapter->isValid()) {
                    $adapter->setDestination($this->getFileLocation());
                    if ($adapter->receive($post['file']['name'])) {
                        $dataInput = $form->getData();
                        $userInfo = $this->getUserInfo();
                        $info = array(
                            'label' => $dataInput['label'],
                            'filename' => $dataInput['file']['name'],
                            'user_id' => $userInfo['id'],
                        );
                        $fileObj = new File();
                        $fileObj->exchangeArray($info);
                        $fileTable = $sm->get('FileTable')->saveFile($fileObj);
                        $this->flashMessenger()->addMessage('Thêm file thành công');
                        return $this->redirect()->toRoute('training/file');
                    }
                } else {
                    $dataError = $adapter->getMessages();
                    foreach ($dataError as $value) {
                        $err[] = $value;
                    }
                    $form->setMessages(array('file' => $err));
                }
            }
        }
        return new ViewModel(array('form' => $form));
    }

    public function editAction(){
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $fileTable = $sm->get('FileTable');

        $data = $fileTable->getFileById($id);
        $form = $sm->get('FormElementManager')->get('FileForm');
        $form->bind($data);
        $request = $this->getRequest();

        $userInfo = $this->getUserInfo();
        $this->isOwner($userInfo['id'],$data->user_id);

        if($request->isPost()){
            $dataInput = $request->getPost();
            $form->setValidationGroup('label');
            $form->setData($dataInput);
            if($form->isValid()){
                $dataUpdate = $form->getData();
                $fileTable->saveFile($dataUpdate);
                $this->flashMessenger()->addMessage('Chỉnh sửa thành công');
                return $this->redirect()->toRoute('training/file',array('action' => 'index'));
            }
        }
        return new ViewModel(array('form' => $form, 'fileId' => $id));
    }

    public function deleteAction(){
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $fileTable = $sm->get('FileTable');
        $fileData = $fileTable->getFileById($id);
        $fileName = $fileData->filename;

        $userInfo = $this->getUserInfo();
        $this->isOwner($userInfo['id'],$fileData->user_id);

        $path = $this->getFileLocation()."/$fileName";
        $fileTable->deleteFileById($id);
        if(file_exists($path)){
            unlink($path);
        }
        $this->flashMessenger()->addMessage('Xoá thành công!');
        return $this->redirect()->toRoute('training/file');
    }

    public function downloadAction(){
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $fileTable = $sm->get('FileTable');
        $fileData = $fileTable->getFileById($id);

        $userInfo = $this->getUserInfo();
        if($fileTable->checkFileShared($id,$userInfo['id'])){
            $path = $this->getFileLocation()."/$fileData->filename";
            $data = file_get_contents($path);
            $response = $this->getEvent()->getResponse();
            $response->getHeaders()->addHeaders(
                array(
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="'.$fileData->filename.'"',
//                'Content-Length' => $stats['size'],
                    'Expires' => '@0', // @0, because zf2 parses date as string to \DateTime() object
                    'Cache-Control' => 'must-revalidate',
                    'Pragma' => 'public'
                )
            );
            $response->setContent($data);
            return $response;
        }else{
            $this->flashMessenger()->addMessage('Bạn không thể tải tập tin vì tập tin chưa được chia sẻ cho bạn!');
            return $this->redirect()->toRoute('training/file',array('action' => 'index'));
        }
    }

    public function shareAction(){
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();

        $view = $sm->get('Zend\View\Renderer\PhpRenderer');
        $view->headScript()->appendFile($view->basePath()."/js/script.js",'text/javascript');

        $form = $sm->get('FormElementManager')->get('ShareForm');
        $fileTable = $sm->get('FileTable');
        $userTable = $sm->get('UserTable');
        $allUser = $userTable->fetchAll($id);
        $fileData = $fileTable->getFileById($id,'withUser');
        $opUser = array();

        $userInfo = $this->getUserInfo();
        $this->isOwner($userInfo['id'],$fileData->user_id);


        // lấy các usser đã đc chia sẻ

        $sharedUser = $fileTable->getUserSharedByFileId($id);

        $flash = $this->flashMessenger()->getMessages();
        foreach ($allUser as $user) {   
            if($user->id != $fileData->user_id && !$fileTable->checkFileShared($id, $user->id)) {
                $opUser[$user->id] = $user->username;
            }
        }

        // nếu file đã đc chia sẻ cho tất cả user thì k đc chia sẻ nữa
        if(empty($opUser)){
            $opUser[] = 'File đã được chia sẻ cho tất cả thành viên';
            $form->get('submit')->setAttribute('disabled','disabled');
        }
        $form->get('user_id')->setValueOptions($opUser); // set value cho select
        $token = $this->params()->fromRoute('token');
        if($token){
            $userData = explode('-',$token);
            if(md5('ThaiDuc'.$userData[0]) == $userData[1]){
                $form->get('user_id')->setAttributes(array('value' => $userData[0],'selected' => true));
            }
        }

        $request = $this->getRequest();
        if($request->isPost()){
            $dataInput = $request->getPost();
            $form->setData($dataInput);
            if($form->isValid()){
                $dataInsert = $form->getData();
                $fileTable->saveShare($id,$dataInsert['user_id']);

                //gửi email khi file đc chia sẻ
                $mail = $sm->get('MailManager');
                $mess = array(
                    'label' => $fileData->label,
                );
                $message = new MailMessage();
                $message->acceptShareFileMessage($mess);
                $dataMailer = array(
                    'mailFrom' => 'hoasaigonn@gmail.com',
                    'nameFrom' => 'Thai Duc Test mail library',
                    'emailTo' => $fileData->email,
                    'emailName' => $fileData->name,
                    'subject' => "Yêu cầu chia sẻ file $fileData->label của bạn đã đc chấp nhận",
                    'message' => $message->getMessageInfo(),
                );
                $mail->setDataMailer($dataMailer);
                $mail->getSmtpTransport()->send($mail->getDataMailer()) ; // gửi

                $this->flashMessenger()->addMessage('Chia sẻ file thành công');
                return $this->redirect()->toRoute('training/file',array('action' => 'share','id' => $id));
            }
        }
        return new ViewModel(array('form' => $form, 'fileId' => $id, 'fileData' => $fileData, 'shared' => $sharedUser, 'flash' => $flash));
    }

    public function removeShareAction(){
        $id = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();

        $fileTable = $sm->get('FileTable');
        $shareInfo  =$fileTable->getSharingById($id);
        $fileInfo = $fileTable->getFileById($shareInfo->file_id);

        $userInfo = $this->getUserInfo();
        $this->isOwner($userInfo['id'],$fileInfo->user_id);

        $fileTable->removeShareById($id);
        $this->flashMessenger()->addMessage('Đã hủy chia sẻ tập tin!');
        return $this->redirect()->toRoute('training/file',array('action' => 'share','id'=>$shareInfo->file_id));

    }

    public function isOwner($userId,$ownerId){
        if($userId != $ownerId){
            $this->flashMessenger()->addMessage('Bạn không phải là tác giả của tập tin nên không thực hiện được chức năng này');
            return $this->redirect()->toRoute('training/file',array('action' => 'index'));
        }else{
            return true;
        }
    }

    public function requestShareAction()
    {
        $fileId = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $userInfo = $this->getUserInfo();

        $fileTable = $sm->get('FileTable');
        $userTable = $sm->get('UserTable');
        $fileInfo = $fileTable->getFileById($fileId, 'withUser');
        if ($fileInfo) {
            $token = md5('ThaiDuc' . $userInfo['id']);
            $link = 'http://localhost:8080' . $this->url()->fromRoute('training/file', array('action' => 'share', 'id' => $fileId, 'token' => $userInfo['id'] . '-' . $token));

            $mail = $sm->get('MailManager');
            $mess = array(
                'userRequest' => $userInfo['username'],
                'owner' => $fileInfo->username,
                'label' => $fileInfo->label,
                'link' => $link
            );
            $message = new MailMessage();
            $message->requestShareFileMessage($mess);
            $dataMailer = array(
                'mailFrom' => 'hoasaigonn@gmail.com',
                'nameFrom' => 'Thai Duc Test mail library',
                'emailTo' => $fileInfo->email,
                'emailName' => $fileInfo->name,
                'subject' => "Yêu cầu chia sẻ file $fileInfo->label từ $userInfo[username]",
                'message' => $message->getMessageInfo(),
            );
            $mail->setDataMailer($dataMailer);
            $mail->getSmtpTransport()->send($mail->getDataMailer()); // gửi

            $this->flashMessenger()->addMessage("Gửi yêu cầu thành công đến $fileInfo->username");
        } else {
            $this->flashMessenger()->addMessage("Liên kết không hợp lệ");
        }
        return $this->redirect()->toRoute('training/file');
    }

}