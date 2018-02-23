<?php
/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 22-Feb-18
 * Time: 2:46 PM
 */

namespace Training\Model;
use Zend\Authentication\Storage;

class MyAuth extends Storage\Session
{
    public function setRememberMe($remember = 0, $time = 2592000){
        if($remember == 1){
            $this->session->getManager()->rememberMe($time);
        }
    }

    public function forgetMe(){
        $this->session->getManager()->forgetMe();
    }
}