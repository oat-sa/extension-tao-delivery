<?php
/**
 * Created by PhpStorm.
 * User: ksasim
 * Date: 29.8.15
 * Time: 17.06
 */

$ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
$loginFormSettings = $ext->getConfig('loginForm');
if( empty($loginFormSettings) ){
    $loginFormSettings = array();
}

$loginFormSettings['elements']['guestAccessLink'] = \taoDelivery_helper_Delivery::getGuestAccessLoginFormElement();
$ext->setConfig('loginForm', $loginFormSettings);
