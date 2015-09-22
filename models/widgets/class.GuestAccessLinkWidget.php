<?php


class taoDelivery_models_widgets_GuestAccessLinkWidget extends \tao_helpers_form_FormElement
{
    public function render()
    {
        $returnValue = '<a href="'._url('guest', 'DeliveryServer', 'taoDelivery').'">'. __("Guest access").'</a>';
        return $returnValue;
    }
    
}
