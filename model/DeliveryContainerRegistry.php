<?php
/**
 * Created by PhpStorm.
 * User: AlehHutnikau
 * Date: 12-Dec-16
 * Time: 10:44
 */

namespace oat\taoDelivery\model;

use oat\oatbox\AbstractRegistry;

/**
 * Class DeliveryContainerRegistry
 *
 * Registry is used to store available delivery container implementations with it's options.
 * @see \oat\taoDelivery\model\DeliveryContainer
 *
 * @package oat\taoDelivery\model
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>\
 */
class DeliveryContainerRegistry extends AbstractRegistry
{

    const CONFIG_ID = 'deliveryContainerRegistry';

    /**
     * @return string
     */
    public function getConfigId()
    {
        return self::CONFIG_ID;
    }

    /**
     * @return \common_ext_Extension
     */
    public function getExtension()
    {
        return \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
    }

    /**
     * Register a delivery container
     *
     * @param string $class fully qualified class name
     * @param array $options options to be used during instantiation of delivery container
     * @return boolean true if registered
     */
    public function register($class, array $options = [])
    {
        if(class_exists($class)) {
            self::getRegistry()->set($class, [
                'class' => $class,
                'options' => $options,
            ]);

            return true;
        }
        return false;
    }

}