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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */
namespace oat\taoDelivery\scripts\install;


use oat\oatbox\service\ConfigurableService;
use oat\oatbox\action\Action;
use \core_kernel_classes_Resource;
use \tao_models_classes_RoleService;
use oat\oatbox\service\ServiceManager;
use \funcAcl_models_classes_ActionAccessService;

class RestrictPlatformToDelivery extends ConfigurableService implements Action {
    
    
    /**
     *
     * @param unknown $params
     */
    public function __invoke($params) {

        $this->removeGlobalManagerSubRole();
        $this->removeDeliveryAction();
        $cache = ServiceManager::getServiceManager()->get('generis/cache');
        $cache->purge();
        $report = new \common_report_Report(\common_report_Report::TYPE_SUCCESS,'Restrict access to only delivery features');
        return $report;
        
    }
    
    /**
     * 
     */
    private function removeGlobalManagerSubRole() {
        $gmRole = new core_kernel_classes_Resource(INSTANCE_ROLE_GLOBALMANAGER);
        
        $roleToUninclude  = array(
            'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole',
            'http://www.tao.lu/Ontologies/TAOTest.rdf#TestsManagerRole',
            'http://www.tao.lu/Ontologies/TAOItem.rdf#QTIManagerRole',
            'http://www.tao.lu/Ontologies/TAOTest.rdf#TaoQtiManagerRole',
            
        );
        foreach($roleToUninclude as $role) {
            $role = new core_kernel_classes_Resource($role);
            tao_models_classes_RoleService::singleton()->unincludeRole($gmRole,$role);
        }

    }
    /**
     * 
     */
    private function removeDeliveryAction() {
        $deliveryMgtRole = 'http://www.tao.lu/Ontologies/generis.rdf#taoDeliveryRdfManager';
        
        $actionToRemove = array(
            'http://www.tao.lu/Ontologies/taoFuncACL.rdf#a_taoDeliveryRdf_DeliveryMgmt_wizard',
            'http://www.tao.lu/Ontologies/taoFuncACL.rdf#a_taoDeliveryRdf_DeliveryMgmt_delete'
        );
        foreach($actionToRemove as $uri) {
            funcAcl_models_classes_ActionAccessService::singleton()->remove($deliveryMgtRole, $uri);
        }
    }
    
    
}