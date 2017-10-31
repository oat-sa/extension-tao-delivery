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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\taoDelivery\model\authorization\strategy;

use oat\oatbox\event\EventManager;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\authorization\AuthorizationProvider;
use oat\taoDelivery\model\authorization\AuthorizationService;
use oat\oatbox\user\User;
use oat\taoDelivery\model\execution\DeliveryExecutionInterface;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionVerified;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * An Authorization Aggregator, that requires all internal
 * authorization providers to allow access
 */
class AuthorizationAggregator extends ConfigurableService  implements AuthorizationService, AuthorizationProvider
{
    const OPTION_PROVIDERS = 'providers';
    
    private $providers;

    /**
     * Returns the base authorization provider.
     *
     * @return AuthorizationProvider 
     */
    public function getAuthorizationProvider()
    {
        return $this;
    }
    
    /**
     * Verify that a given delivery is allowed to be started
     *
     * @param string $deliveryId
     * @throws \common_exception_Unauthorized
     */
    public function verifyStartAuthorization($deliveryId, User $user)
    {
        foreach ($this->getProviders() as $provider) {
            $provider->verifyStartAuthorization($deliveryId, $user);
        }
    }

    /**
     * Verify that a given delivery execution is allowed to be executed
     *
     * @param DeliveryExecutionInterface $deliveryExecution
     * @param User $user
     */
    public function verifyResumeAuthorization(DeliveryExecutionInterface $deliveryExecution, User $user)
    {
        foreach ($this->getProviders() as $provider) {
            $provider->verifyResumeAuthorization($deliveryExecution, $user);
        }
        $this->getServiceManager()->get(EventManager::SERVICE_ID)->trigger(new DeliveryExecutionVerified($deliveryExecution));
    }
    
    /**
     * Returns a list of providers that need to be verified
     * 
     * @return AuthorizationProvider[]
     */
    protected function getProviders()
    {
        if (is_null($this->providers)) {
            $this->providers = array();
            if ($this->hasOption(self::OPTION_PROVIDERS)) {
                foreach ($this->getOption(self::OPTION_PROVIDERS) as $provider) {
                    if ($provider instanceof ServiceLocatorAwareInterface) {
                        $provider->setServiceLocator($this->getServiceLocator());
                    }
                    $this->providers[] = $provider;
                }
            }
        }
        return $this->providers;
    }
    
    /**
     * Add an additional authorization provider that needs
     * to be satisfied as well
     * 
     * @param AuthorizationProvider $provider
     */
    public function addProvider(AuthorizationProvider $provider)
    {
        $providers = $this->getOption(self::OPTION_PROVIDERS);
        $providers[] = $provider;
        $this->setOption(self::OPTION_PROVIDERS, $providers);
    }

    /**
     * Remove an existing authorization provider, identified by
     * exact class
     *
     * @param $providerClass
     * @internal param AuthorizationProvider $provider
     */
    public function unregister($providerClass)
    {
        $providers = $this->getOption(self::OPTION_PROVIDERS);
        foreach ($providers as $key => $provider) {
            if (get_class($provider) == $providerClass) {
                unset($providers[$key]);
            }
        }
        $this->setOption(self::OPTION_PROVIDERS, $providers);
    }
}
