<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 23-Feb-18
 * Time: 5:10 PM
 */

namespace Training\Controller;


use Training\Model\File;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;

class FileController extends AbstractActionController
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

        $flash = $this->flashMessenger()->getMessages();
        return new ViewModel(array('files' => $files,'flash' => $flash));
    }



    public function addAction()
    {
        $sm = $this->getServiceLocator();
        $form = $sm->get('FileForm');
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
        $form = $sm->get('FileForm');
        $form->bind($data);
        $request = $this->getRequest();
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
        $fileName = $fileTable->getFileById($id)->filename;
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
    }
}