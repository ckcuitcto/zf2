<?php

/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 05-Mar-18
 * Time: 12:56 AM
 */
namespace Blog\View\Helper;
use Zend\View\Helper\AbstractHelper;

class Menu extends AbstractHelper
{
    protected $sm;
    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    public function callMenu(){
        $entityManager = $this->sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $menus = $entityManager->getRepository('\Blog\Entity\Category')->findAll();


        $str = '<div class="panel panel-default">';
        $str .= '    <div class="panel-heading">';
        $str .= '        <h3 class="panel-title"> Chuyên mục</h3>';
        $str .= '    </div>';
        $str .= '    <div class="panel-body">';
        $str .= "<ul class='nav nav-pills nav-stacked'>";
        foreach ($menus as $menu){
            $url = $this->view->url('blog/post',array('action'=>'cate','id'=>$menu->getId()));
            $str .= "<li><a href='$url'>".$menu->getName()."</a>  </li>";
        }
        $str .= "</ul>";
        $str .= '    </div>';
        $str .= '</div>';

        return $str;
    }
}