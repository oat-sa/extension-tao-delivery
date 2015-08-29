<?php

/**
 * Created by PhpStorm.
 * User: ksasim
 * Date: 29.8.15
 * Time: 13.53
 */
class taoDelivery_models_classes_GuestTestTakerSession extends common_session_DefaultSession
{

    public function __construct()
    {
        parent::__construct( new taoDelivery_models_classes_GuestTestUser() );
    }

    public function getUserLabel()
    {
        return __('TAO Guest');
    }

}