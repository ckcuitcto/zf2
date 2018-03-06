<?php
namespace Blog\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
class AuthController extends AbstractActionController{
		public function indexAction(){
			
			return false;
		}	
		public function loginAction(){
			$sm=$this->getServiceLocator();
			$form=$sm->get('FormElementManager')->get('VerifyForm');
			$request=$this->getRequest();
			$error="";
			$flash=$this->flashMessenger()->getMessages();
			if($request->isPost()){
				$data=$request->getPost();
				$form->setData($data);
				if($form->isValid()){
					$dataInput=$form->getData();
                    echo "<pre>";
                    print_r($dataInput);
                    echo "</pre>";

                }
			}
			return new ViewModel(array('form'=>$form,'error'=>$error,'flash'=>$flash));
		}		
}