<?php

/**
 * Created by PhpStorm.
 * User: Thai Duc
 * Date: 02-Mar-18
 * Time: 12:03 AM
 */
namespace Training\Navigation;
use Zend\Navigation\Service\DefaultNavigationFactory;

class TrainingNavigationFactory extends DefaultNavigationFactory
{
    public function getName()
    {
        return 'training';
    }
}