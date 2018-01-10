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
 */

namespace oat\taoDelivery\model;

use oat\taoDelivery\model\execution\DeliveryExecution;

/**
 * This service is used to feed the delivery container with the required data to run a test.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
interface  DeliveryContainerService
{
    /** @deprecated */
    const CONFIG_ID = 'taoDelivery/deliveryContainer';

    const SERVICE_ID = 'taoDelivery/deliveryContainer';

    /** @deprecated  */
    const PROPERTY_RESULT_SERVER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer';
    
    /**
     * Get the list of providers for the current execution
     * @param DeliveryExecution $execution
     * @return array the list of providers
     */
    public function getProviders(DeliveryExecution $execution);
    
    /**
     * Get the list of plugins for the current execution
     * @param DeliveryExecution $execution
     * @return array the list of plugins
     */
    public function getPlugins(DeliveryExecution $execution);

    /**
     * Get the container bootstrap
     * @param DeliveryExecution $execution
     * @return string the bootstrap
     */
    public function getBootstrap(DeliveryExecution $execution);

    /**
     * Get the test definition
     * @param DeliveryExecution $execution
     * @return string the test definition
     */
    public function getTestDefinition(DeliveryExecution $execution);

    /**
     * Get the test compilation
     * @param DeliveryExecution $execution
     * @return string the test compilation
     */
    public function getTestCompilation(DeliveryExecution $execution);

}
