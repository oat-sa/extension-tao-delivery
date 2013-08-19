<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class VariableServiceTestCase extends UnitTestCase {

	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
	}
	
	public function tearDown() {
    }
	
	
	public function testService() {
		$service = taoDelivery_models_classes_itemVariables_VariableProxy::singleton();
		$user = new core_kernel_classes_Resource(LOCAL_NAMESPACE.'#inexistentTestUser');

		// is not set		
		$this->assertFalse($service->has($user, 'testkey'));
		$value = $service->get($user, 'testkey');
		$this->assertNull($value);
		
		//  test set		
		$this->assertTrue($service->set($user, 'testkey', 'testvalue'));
		$this->assertTrue($service->has($user, 'testkey'));
		$value = $service->get($user, 'testkey');
		$this->assertEqual($value, 'testvalue');
		
		//  test replace		
		$this->assertTrue($service->set($user, 'testkey', 'testvalue2'));
		$this->assertTrue($service->has($user, 'testkey'));
		$value = $service->get($user, 'testkey');
		$this->assertEqual($value, 'testvalue2');

		//  test delete		
		$this->assertTrue($service->del($user, 'testkey'));
		$this->assertFalse($service->has($user, 'testkey'));
		$value = $service->get($user, 'testkey');
		$this->assertNull($value);
		$this->assertFalse($service->del($user, 'testkey'));
	}
}

