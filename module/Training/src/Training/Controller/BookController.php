<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 27-Feb-18
 * Time: 12:14 PM
 */

namespace Training\Controller;


use Training\Model\Order;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZendCart\Controller\Plugin\ZendCart;

class BookController extends AbstractActionController
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
    /*
    public function indexAction()
    {
        $product = array(
            'id'      => '1',
            'qty'     => 1,
            'price'   => 39.95,
            'name'    => 'PHP cơ bản',
            'options' => array('author' => 'ThaiDuc')
        );

        $cart = $this->ZendCart()->cart();
        $isOld = FALSE;
        if(!empty($cart)) {
            foreach ($cart as $key => $item) {
                if ($item['id'] == $product['id']) {
                    $product = array(
                        'token' => $key,
                        'qty' => $item['qty'] + 1,
                    );
                    $this->ZendCart()->update($product);
                    $isOld = TRUE;
                }
            }
        }
        if($isOld == FALSE){
            $this->ZendCart()->insert($product);
        }

        echo "Done";
        return new ViewModel();
    }

    public function index2Action(){
        $data = $this->ZendCart()->cart();

        echo "<pre>";
        print_r($data);
        echo "</pre>";

        return false;

    }

    // delete
    public function index3Action(){
        $this->ZendCart()->destroy();
        return false;
    }
    */

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $bookTable = $sm->get('BookTable');
        $bookList = $bookTable->fetchAll();

        $flash = $this->flashMessenger()->getMessages();
        $totalProductInCart = ($this->ZendCart()->total_items() > 0) ? $this->ZendCart()->total_items() : 0;
        return new ViewModel(array('bookList' => $bookList, 'flash' => $flash, 'totalProductInCart' => $totalProductInCart));
    }

    public function addItemAction()
    {

        $sm = $this->getServiceLocator();
        $bookTable = $sm->get('BookTable');

        $bookId = $this->params()->fromRoute('id');
        $book = $bookTable->getBookById($bookId);

        $product = array(
            'id' => $book->id,
            'qty' => 1,
            'price' => $book->price,
            'name' => $book->info,
            'options' => array('author' => $book->author)
        );

        $cart = $this->ZendCart()->cart();
        $isOld = FALSE;
        if (!empty($cart)) {
            foreach ($cart as $key => $item) {
                if ($item['id'] == $product['id']) {
                    $product = array(
                        'token' => $key,
                        'qty' => $item['qty'] + 1,
                    );
                    $this->ZendCart()->update($product);
                    $isOld = TRUE;
                }
            }
        }
        if ($isOld == FALSE) {
            $this->ZendCart()->insert($product);
        }

        $this->flashMessenger()->addMessage("Thêm sách $book->ìno vào giỏ hàng thành công!");
        return $this->redirect()->toRoute('training/book', array('action' => 'index'));
    }

    public function cartAction()
    {
        $sm = $this->getServiceLocator();
        $cart = $this->ZendCart()->cart();
        $totalProductInCart = $this->ZendCart()->total_items();
        $totalPriceInCart = $this->ZendCart()->total();

        $flash = $this->flashMessenger()->getMessages();

        $form = new Form();
        $form->setName('sp');
        if (!empty($cart)) {
            foreach ($cart as $token => $item) {
                $element = new \Zend\Form\Element\Number($item['id'] . "[qty]");
                $element->setAttribute('min', 0);
                $element->setValue($item['qty']);

                $element2 = new \Zend\Form\Element\Hidden($item['id'] . "[token]");
                $element2->setValue($token);

                $form->add($element)->add($element2);
            }
        }

        $form->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Cập nhật',
                'class' => 'btn btn-primary'
            )
        ));

        return new ViewModel(array('form' => $form, 'cart' => $cart, 'totalProductInCart' => $totalProductInCart, 'flash' => $flash,
            'totalPriceInCart' => $totalPriceInCart
        ));
    }

    public function updateItemAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            foreach ($data as $item) {
                if (is_array($item)) {
                    if (is_numeric($item['qty']) && $item['qty'] > 0) {
                        $this->ZendCart()->update($item);
                    } else {
                        $this->flashMessenger()->addMessage("Số lượng nhập vào không hợp lệ");
                        return $this->redirect()->toRoute('training/book', array('action' => 'cart'));
                        break;
                    }
                }
            }
        }
        $this->flashMessenger()->addMessage("Cập nhật giỏ hàng thành công");
        return $this->redirect()->toRoute('training/book', array('action' => 'cart'));
    }

    public function removeAllAction()
    {
        $this->ZendCart()->destroy();

        $this->flashMessenger()->addMessage("Giỏ hàng đã xoá !");
        return $this->redirect()->toRoute('training/book', array('action' => 'cart'));
    }

    public function removeItemAction()
    {
        $id = $this->params()->fromRoute('id');

        $cart = $this->ZendCart()->cart();
        foreach ($cart as $token => $item) {
            if ($item['id'] == $id) {
                $this->ZendCart()->remove(array('token' => $token));

                $this->flashMessenger()->addMessage("Đã xoá sản phẩm $item[name] !");
                return $this->redirect()->toRoute('training/book', array('action' => 'cart'));
            }
        }
    }

    public function checkoutAction()
    {
        $total = $this->ZendCart()->total();
        $sm = $this->getServiceLocator();

        $paypalRequest = $this->getPaypalRequest();
        $paymentDetails = new \SpeckPaypal\Element\PaymentDetails(array(
            'amt' => $total['sub-total'],
        ));

        $express = new \SpeckPaypal\Request\SetExpressCheckout(array('paymentDetails' => $paymentDetails));

        $urlConfirm = $this->url()->fromRoute('training/book', array('action' => 'paymentConfirm'));
        $urlCancel = $this->url()->fromRoute('training/book', array('action' => 'paymentCancel'));
        $express->setReturnUrl('http://localhost:8080' . $urlConfirm);
        $express->setCancelUrl('http://localhost:8080' . $urlCancel);

        $response = $paypalRequest->send($express);
        $token = $response->getToken();

        // lưu trữ token vào session dùng ở paymentConfỉm
        $paypalSession = new \Zend\Session\Container('paypal');
        $paypalSession->tokenId = $token;

        return $this->redirect()->toUrl("https://www.sandbox.paypal.com/websrc?cmd=_express-checkout&token=$token&userAction=commit");
    }

        public function paymentConfirmAction(){
        $sm = $this->getServiceLocator();
        $paypalSession = new \Zend\Session\Container('paypal');
        $token = $paypalSession->tokenId;

        //nếu k có session token,nghĩa là chua giao dịch thì chuyển về cart
        if(empty($token)){
            $this->flashMessenger()->addMessage("Bạn không có quyền truy cập trang này");
            return $this->redirect()->toRoute('training/book', array('action' => 'cart'));
        }
        $total = $this->ZendCart()->total();
        $paymentDetails = new \SpeckPaypal\Element\PaymentDetails(array(
            'amt' => $total['sub-total'],
        ));

        $details = new \SpeckPaypal\Request\GetExpressCheckoutDetails(array('token' => $token));
        $paypalRequest =  $this->getPaypalRequest();
        $response = $paypalRequest->send($details);
        $payerId = $response->getPayerId();

        //To capture express payment
        $captureExpress = new \SpeckPaypal\Request\DoExpressCheckoutPayment(array(
            'token'             => $token,
            'payerId'           => $payerId,
            'paymentDetails'    => $paymentDetails
        ));
        $captureResponse = $paypalRequest->send($captureExpress);


        // lưu vào db
        $cart = $this->ZendCart()->cart();
        foreach ($cart as $token => $item){
            $id = $item['id'];
            $data[$id] = array(
                'name' => $item['name'],
                'qty' => $item['qty'],
                'price' => $item['price'],
            );
        }
        $serialize = new \Zend\Serializer\Adapter\PhpSerialize();
        $detail = $serialize->serialize($cart);

        $userLogin = $this->getUserInfo();
        $order = new Order();
        $dataPayment = array(
            'user_id' => $userLogin['id'],
            'total' => $total['sub-total'],
            'detail' => $detail,
            'ship_name' => $response->getFisrtName()." ".$response->getLastName(),
            'ship_address' => $response->getShipToStreet().",".$response->getShipToCity().", ".$response->getShipToCountryName(),
        );
        $order->exchangeArray($dataPayment);
        $orderTable = $sm->get('OrderTable');
        $orderId = $orderTable->saveOrder($order);

        $dataPayment['detail'] = $serialize->unserialize($detail);
        $dataPayment['time'] = $orderTable->getOrderById($orderId)->stamp;
        $dataPayment['orderId'] = $orderId;

        $this->ZendCart()->destroy();
        $paypalSession->tokenId = null;

        return new ViewModel(array('dataPayment' => $dataPayment));
    }

    public function getPaypalRequest(){
        $sm = $this->getServiceLocator();
        $config = $sm->get('config');

        $paypalConfig = new \SpeckPaypal\Element\Config($config['paypal-api']);

        $adapter = new \Zend\Http\Client\Adapter\Curl();
        $adapter->setOptions(array(
            'curloptions' => array(
                CURLOPT_SSL_VERIFYPEER => false,
            ),
        ));
        //set up http client
        $client = new \Zend\Http\Client;
        $client->setMethod('POST');
        $client->setAdapter($adapter);

        $paypalRequest = new \SpeckPaypal\Service\Request;
        $paypalRequest->setClient($client);
        $paypalRequest->setConfig($paypalConfig);

        return $paypalRequest;
    }

    public function historyPaymentAction(){
        $userLogin = $this->getUserInfo();
        $sm = $this->getServiceLocator();
        $orderTable = $sm->get('OrderTable');
        $allOrder = $orderTable->getAllOrderByUserId($userLogin['id']);

        $flash = $this->flashMessenger()->getMessages();

        return new ViewModel(array('allOrder' => $allOrder,'flash' => $flash));
    }

    public function orderDetailAction(){
        $userLogin = $this->getUserInfo();
        $orderId = $this->params()->fromRoute('id');
        $sm = $this->getServiceLocator();
        $orderTable = $sm->get('OrderTable');
        $order = $orderTable->getOrderById($orderId);
        if ($order->user_id == $userLogin['id']) {
            $serialize = new \Zend\Serializer\Adapter\PhpSerialize();
            $books = $serialize->unserialize($order->detail);
            return new ViewModel(array('order' => $order, 'books' => $books));
        }else{
            $this->flashMessenger()->addMessage("Bạn không có quyền truy cập trang này");
            return $this->redirect()->toRoute('training/book', array('action' => 'historyPayment'));
        }
    }
}