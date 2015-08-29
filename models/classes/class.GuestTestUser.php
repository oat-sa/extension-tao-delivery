<?php

/**
 * Created by PhpStorm.
 * User: ksasim
 * Date: 29.8.15
 * Time: 14.46
 */
use oat\oatbox\user\User;

class taoDelivery_models_classes_GuestTestUser implements User
{
    protected $uri;

    public function __construct()
    {
        $this->uri = common_Utils::getNewUri();
    }

    public function getIdentifier()
    {
        return $this->uri;
    }

    public function getPropertyValues($property)
    {
        return array();
    }

    public function getRoles()
    {
        return array(
            INSTANCE_ROLE_DELIVERY => INSTANCE_ROLE_DELIVERY,
            INSTANCE_ROLE_ANONYMOUS => INSTANCE_ROLE_ANONYMOUS,
            INSTANCE_ROLE_BASEUSER => INSTANCE_ROLE_BASEUSER
        );
    }
}
