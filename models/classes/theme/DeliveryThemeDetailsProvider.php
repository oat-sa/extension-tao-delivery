<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDelivery\models\classes\theme;


use oat\oatbox\PhpSerializable;
use oat\oatbox\PhpSerializeStateless;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\theme\ThemeDetailsProviderInterface;

/**
 * Class DeliveryThemeDetailsProvider
 *
 * @package oat\taoDelivery\models\classes\theme
 * @deprecated It was moved to oat\taoDeliveryRdf\model\theme\DeliveryThemeDetailsProvider
 */
class DeliveryThemeDetailsProvider extends \Actions implements ThemeDetailsProviderInterface, PhpSerializable
{

    use PhpSerializeStateless;
    

    /**
     * The delivery theme id uri.
     */
    const DELIVERY_THEME_ID_URI = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ThemeName';

    /**
     * @inheritdoc
     */
    public function getThemeId()
    {

        $deliveryExecutionId = \tao_helpers_Uri::decode($this->getRequestParameter('deliveryExecution'));

        $themeId = '';
        if (!empty($deliveryExecutionId)) {
            $deliveryId = $this->getDeliveryIdFromSession($deliveryExecutionId);
            if ($deliveryId !== false) {
                $themeId = $this->getDeliveryThemeId($deliveryId);
            }
        }

        return $themeId;
    }

    /**
     * Tells if the page has to be headless: without header and footer.
     *
     * @return bool|mixed
     */
    public function isHeadless()
    {
        return false;
    }

    /**
     * Returns the deliveryId from session.
     *
     * @param $deliveryExecutionId
     *
     * @return mixed
     */
    public function getDeliveryIdFromSession($deliveryExecutionId)
    {
        if (\PHPSession::singleton()->hasAttribute(static::getDeliveryIdSessionKey($deliveryExecutionId))){
            return \PHPSession::singleton()->getAttribute(static::getDeliveryIdSessionKey($deliveryExecutionId));
        }

        return false;
    }

    /**
     * Returns the delivery theme id.
     *
     * @param $deliveryId
     *
     * @return string
     */
    public function getDeliveryThemeId($deliveryId)
    {
        $themeId = $this->getDeliveryThemeIdFromCache($deliveryId);
        if ($themeId === false) {
            $themeId = $this->getDeliveryThemeIdFromDb($deliveryId);
            $this->storeDeliveryThemeIdToCache($deliveryId, $themeId);
        }

        return $themeId;
    }

    /**
     * Returns the delivery theme id from cache or FALSE when it does not exist.
     *
     * @param $deliveryId
     *
     * @return bool|\common_Serializable
     */
    public function getDeliveryThemeIdFromCache($deliveryId)
    {
        $cache    = $this->getCache();
        $cacheKey = $this->getCacheKey($deliveryId);
        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        return false;
    }

    /**
     * Returns delivery theme id from database.
     *
     * @param $deliveryId
     *
     * @return string
     */
    public function getDeliveryThemeIdFromDb($deliveryId)
    {
        try {
            $delivery = new \core_kernel_classes_Resource($deliveryId);

            $property = $delivery->getProperty(static::DELIVERY_THEME_ID_URI);
            $themeId  = (string)$delivery->getOnePropertyValue($property);

            return $themeId;
        }
        catch (\common_exception_Error $e) {
            return '';
        }
    }

    /**
     * Stores the delivery theme id to cache.
     *
     * @param $deliveryId
     * @param $themeId
     *
     * @return bool
     */
    public function storeDeliveryThemeIdToCache($deliveryId, $themeId)
    {
        try {
            return $this->getCache()->put($themeId, $this->getCacheKey($deliveryId), 60);
        }
        catch (\common_exception_NotImplemented $e) {
            return false;
        }
    }

    /**
     * Returns the cache key.
     *
     * @param $deliveryId
     *
     * @return string
     */
    public function getCacheKey($deliveryId)
    {
        return 'deliveryThemeId:' . $deliveryId;
    }

    /**
     * Returns the delivery id session key.
     *
     * @param $deliveryExecutionId
     *
     * @return string
     */
    public static function getDeliveryIdSessionKey($deliveryExecutionId)
    {
        return 'deliveryIdForDeliveryExecution:' . $deliveryExecutionId;
    }

    /**
     * Returns the cache instance.
     *
     * @return \common_cache_Cache
     */
    public function getCache()
    {
        return ServiceManager::getServiceManager()->get(\common_cache_Cache::SERVICE_ID);
    }
}
