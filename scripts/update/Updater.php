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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\taoDelivery\scripts\update;


use oat\oatbox\service\ServiceNotFoundException;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\user\TaoRoles;
use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\entryPoint\EntryPointService;
use oat\taoDelivery\model\authorization\AuthorizationService;
use oat\taoDelivery\model\authorization\strategy\AuthorizationAggregator;
use oat\taoDelivery\model\authorization\strategy\StateValidation;
use oat\taoDelivery\model\entrypoint\FrontOfficeEntryPoint;
use oat\taoDelivery\model\entrypoint\GuestAccess;
use oat\taoDelivery\models\classes\ReturnUrlService;
use oat\taoDelivery\model\fields\DeliveryFieldsService;
use taoDelivery_models_classes_execution_ServiceProxy;
use oat\taoDelivery\model\execution\StateService;
use oat\taoDelivery\controller\DeliveryServer;
use oat\taoDelivery\model\RuntimeService;
use oat\taoDelivery\model\container\LegacyRuntime;
use oat\taoDelivery\model\container\delivery\DeliveryContainerRegistry;
use oat\taoDelivery\model\container\delivery\DeliveryServiceContainer;

/**
 *
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends \common_ext_ExtensionUpdater {

    /**
     *
     * @param $initialVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {

        $currentVersion = $initialVersion;

        //migrate from 2.6 to 2.6.1
        if ($currentVersion == '2.6') {

            //data upgrade
            OntologyUpdater::syncModels();
            $currentVersion = '2.6.1';
        }

        if ($currentVersion == '2.6.1') {
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
            $className = $ext->getConfig(taoDelivery_models_classes_execution_ServiceProxy::CONFIG_KEY);
            if (is_string($className)) {
                $impl = null;
                switch ($className) {
                    case 'taoDelivery_models_classes_execution_OntologyService' :
                        $impl = new \taoDelivery_models_classes_execution_OntologyService();
                        break;
                    case 'taoDelivery_models_classes_execution_KeyValueService' :
                        $impl = new \taoDelivery_models_classes_execution_KeyValueService(array(
                            \taoDelivery_models_classes_execution_KeyValueService::OPTION_PERSISTENCE => 'deliveryExecution'
                        ));
                        break;
                    default :
                        \common_Logger::w('Unable to migrate custom execution service');
                }
                if (!is_null($impl)) {
                    $proxy = taoDelivery_models_classes_execution_ServiceProxy::singleton();
                    $proxy->setImplementation($impl);
                    $currentVersion = '2.6.2';
                }
            }
        }
        if ($currentVersion == '2.6.2') {
            $currentVersion = '2.6.3';
        }

        if ($currentVersion == '2.6.3') {

            //data upgrade
            OntologyUpdater::syncModels();
            $currentVersion = '2.7.0';
        }


        if ($currentVersion == '2.7.0') {
            EntryPointService::getRegistry()->registerEntryPoint(new \oat\taoDelivery\model\entrypoint\FrontOfficeEntryPoint());
            $currentVersion = '2.7.1';
        }

        if ($currentVersion == '2.7.1' || $currentVersion == '2.8') {
            $currentVersion = '2.9';
        }

        if( $currentVersion == '2.9'){
            OntologyUpdater::syncModels();

            //grant access to anonymous user
            AclProxy::applyRule(new AccessRule(
               AccessRule::GRANT,
               TaoRoles::ANONYMOUS,
               ['ext' => 'taoDelivery', 'mod' => 'DeliveryServer', 'act' => 'guest']
            ));

            $currentVersion = '2.9.1';
        }

        if( $currentVersion == '2.9.1'){
            OntologyUpdater::syncModels();
            $currentVersion = '2.9.2';
        }

        if ($currentVersion == '2.9.2') {
            //$assignmentService = new \taoDelivery_models_classes_AssignmentService();
            //$this->getServiceManager()->register('taoDelivery/assignment', $assignmentService);
            $currentVersion = '2.9.3';
        }

        if ($currentVersion == '2.9.3') {
            try{
                $currentConfig = $this->getServiceManager()->get(\taoDelivery_models_classes_DeliveryServerService::CONFIG_ID);
                if (is_array($currentConfig)) {
                    $deliveryServerService = new \taoDelivery_models_classes_DeliveryServerService($currentConfig);
                } else {
                    $deliveryServerService = new \taoDelivery_models_classes_DeliveryServerService();
                }
            }catch(ServiceNotFoundException $e){
                $deliveryServerService = new \taoDelivery_models_classes_DeliveryServerService();
            }
            $this->getServiceManager()->register(\taoDelivery_models_classes_DeliveryServerService::CONFIG_ID, $deliveryServerService);
            $currentVersion = '2.9.4';
        }

        $this->setVersion($currentVersion);

        if ($this->isVersion('2.9.4')) {
            OntologyUpdater::syncModels();
            $this->setVersion('3.0.0');
        }

        if ($this->isBetween('3.0.0','3.1.0')) {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
            $config = $extension->getConfig('deliveryServer');
            $config->setOption('deliveryContainer', 'oat\\taoDelivery\\helper\\container\\DeliveryServiceContainer');
            $extension->setConfig('deliveryServer', $config);
            $this->setVersion('3.1.0');
        }

        $this->skip('3.1.0','3.2.0');

        if ($this->isVersion('3.2.0')) {
            // set the test runner controller
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
            $config = $extension->getConfig('testRunner');
            $config['serviceController'] = 'Runner';
            $config['serviceExtension'] = 'taoQtiTest';
            $extension->setConfig('testRunner', $config);

            $this->setVersion('3.3.0');
        }

        $this->skip('3.3.0', '3.10.0');

        if ($this->isVersion('3.10.0')) {

            $service = new AuthorizationAggregator();
            $service->addProvider(new StateValidation());
            $this->getServiceManager()->register(AuthorizationService::SERVICE_ID, $service);

            $this->setVersion('4.0.0');
        }

        $this->skip('4.0.0', '4.4.2');

        if ($this->isVersion('4.4.2')) {

            /*@var $routeService \oat\tao\model\mvc\DefaultUrlService */
            $routeService = $this->getServiceManager()->get(\oat\tao\model\mvc\DefaultUrlService::SERVICE_ID);
            $routeService->setRoute('logoutDelivery',
                        [
                            'ext'        => 'taoDelivery',
                            'controller' => 'DeliveryServer',
                            'action'     => 'logout',
                            'redirect'   => ROOT_URL,
                        ]
                    );
            $this->getServiceManager()->register(\oat\tao\model\mvc\DefaultUrlService::SERVICE_ID , $routeService);

            $this->setVersion('4.4.3');
        }

        $this->skip('4.4.3', '4.8.3');

        if ($this->isVersion('4.8.3')) {
            try {
                $this->getServiceManager()->get(StateService::SERVICE_ID);
            } catch (ServiceNotFoundException $e) {
                $service = new StateService([]);
                $service->setServiceManager($this->getServiceManager());
                $this->getServiceManager()->register(StateService::SERVICE_ID, $service);
            }
            $this->setVersion('4.9.0');
        }

        $this->skip('4.9.0', '6.1.2');

        if ($this->isVersion('6.1.2')) {
            AclProxy::revokeRule(new AccessRule('grant', TaoRoles::ANONYMOUS, array('ext'=>'taoDelivery', 'mod'=>'DeliveryServer', 'action'=>'logout')));
            AclProxy::applyRule(new AccessRule('grant', TaoRoles::ANONYMOUS, DeliveryServer::class.'@logout'));
            $this->setVersion('6.1.3');
        }

        if ($this->isVersion('6.1.3')) {

            /*@var $routeService \oat\tao\model\mvc\DefaultUrlService */
            $routeService = $this->getServiceManager()->get(\oat\tao\model\mvc\DefaultUrlService::SERVICE_ID);
            $routeService->setRoute('logoutDelivery',
                [
                    'ext'        => 'taoDelivery',
                    'controller' => 'DeliveryServer',
                    'action'     => 'logout',
                    'redirect'   =>
                        [
                            'class'   => \oat\tao\model\mvc\DefaultUrlModule\TaoActionResolver::class,
                            'options' => [
                                'action' => 'entry',
                                'controller' => 'Main',
                                'ext' => 'tao'
                            ]
                        ],
                ]
            );
            $this->getServiceManager()->register(\oat\tao\model\mvc\DefaultUrlService::SERVICE_ID , $routeService);

            $this->setVersion('6.1.4');
        }

        $this->skip('6.1.4', '6.1.5');

        // added runtime service
        if ($this->isVersion('6.1.5')) {
            $this->getServiceManager()->register(RuntimeService::SERVICE_ID, new LegacyRuntime());
            $this->setVersion('6.2.0');
        }

        // Added Delivery Fields Service
        if ($this->isVersion('6.2.0')) {
            $service = new DeliveryFieldsService([
                DeliveryFieldsService::PROPERTY_CUSTOM_LABEL => [
                    INSTANCE_ROLE_DELIVERY
                ]
            ]);
            $service->setServiceManager($this->getServiceManager());
            $this->getServiceManager()->register(DeliveryFieldsService::SERVICE_ID, $service);
            $this->setVersion('6.3.0');
        }

        $this->skip('6.3.0', '6.4.0');

        if ($this->isVersion('6.4.0')) {
            if(!$this->getServiceManager()->has(ReturnUrlService::SERVICE_ID)){
                $service = new ReturnUrlService();
                $this->getServiceManager()->propagate($service);
                $this->getServiceManager()->register(ReturnUrlService::SERVICE_ID, $service);
            }
            $this->setVersion('6.5.0');
        }

        if ($this->isVersion('6.5.0')) {
            $registry = DeliveryContainerRegistry::getRegistry();
            $registry->setServiceLocator($this->getServiceManager());
            $registry->registerContainerType(
                DeliveryServiceContainer::DEFAULT_ID, new DeliveryServiceContainer());
            $this->setVersion('6.6.0');

        }
      
       if ($this->isVersion('6.6.0')) {
           /** @var EntryPointService $entryPointService */
           $entryPointService = $this->safeLoadService(EntryPointService::SERVICE_ID);

           foreach ([EntryPointService::OPTION_POSTLOGIN, EntryPointService::OPTION_PRELOGIN] as $type) {
               $entryPoints = $entryPointService->getEntryPoints($type);
               foreach ($entryPoints as $k => $v) {

                   if (is_a($v, 'taoDelivery_models_classes_entrypoint_FrontOfficeEntryPoint')) {
                       $entryPointService->overrideEntryPoint($k, new FrontOfficeEntryPoint());
                   }

                   if (is_a($v, 'taoDelivery_models_classes_entrypoint_GuestAccess')) {
                       $entryPointService->overrideEntryPoint($k, new GuestAccess());
                   }
               }
           }

           $this->getServiceManager()->register(EntryPointService::SERVICE_ID, $entryPointService);

           $this->setVersion('6.7.0');
       }

        $this->skip('6.7.0', '6.7.1');

    }
}
