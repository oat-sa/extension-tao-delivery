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

class CompilationTestCase extends UnitTestCase {
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
		
	}
	
	public function tearDown() {
    }
	
	public function testCompile(){
		$itemClass	= taoItems_models_classes_ItemsService::singleton()->getItemClass();
		$qtiFile	= dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR.'qti.zip';
		
		$qtiItems = taoQTI_models_classes_QTI_ImportService::singleton()->importQTIPACKFile($qtiFile, $itemClass, false);
		$this->assertEqual(count($qtiItems), 1);
		$qtiItem = current($qtiItems);

		$owiFile	= dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR.'owi.zip';
		$owiItem	= taoItems_models_classes_XHTML_ImportService::singleton()->importXhtmlFile($owiFile, $itemClass, false);
		
		$testsService = taoTests_models_classes_TestsService::singleton();
		$test = $testsService->createInstance(new core_kernel_classes_Class(TAO_TEST_CLASS), 'UnitTest Test');
		$this->assertTrue($testsService->setTestItems($test, array($qtiItem, $owiItem)));
		
		$deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
		$delivery = $deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTest Delivery');
		$this->assertTrue($deliveryService->setDeliveryTests($delivery, array($test)));
		
		$resultArray = $deliveryService->compileTest($delivery, $test);
		$this->assertEqual($resultArray, array("success"=> 1,  "failed" => array()));
		
		$compiledFolder = $deliveryService->getCompiledFolder($delivery);
		$this->assertTrue(file_exists($compiledFolder));
		
		$qtiPath = $deliveryService->getCompiledItemFolder($delivery, $test, $qtiItem, array(DEFAULT_LANG));
		$this->assertTrue(is_dir($qtiPath));
		$this->assertTrue(file_exists($qtiPath.'index.html'));
		$this->assertTrue(file_exists($qtiPath.'smiling.jpg'));
		$this->verifyLinks($qtiPath.'index.html');
		
		$owiPath = $deliveryService->getCompiledItemFolder($delivery, $test, $owiItem, array(DEFAULT_LANG));
		$this->assertTrue(is_dir($owiPath));
		$this->assertTrue(file_exists($owiPath.'index.html'));
		$data = file_get_contents($owiPath.'index.html');
		$this->assertTrue(file_exists($owiPath.'styles/simple.css'));
		$this->assertTrue(file_exists($owiPath.'scripts/simple.js'));
		$this->assertTrue(file_exists($owiPath.'media/simple.png'));
		$this->verifyLinks($owiPath.'index.html');
		
		$deliveryService->deleteDelivery($delivery);
		
		// test if deleted
		$this->assertFalse(is_dir($qtiPath));
		$this->assertFalse(is_dir($owiPath));
		$this->assertFalse(file_exists($compiledFolder));
		
		$testsService->deleteTest($test);
		$qtiItem->delete();
		$owiItem->delete();
	}
	
	/**
	 * 
	 */
	private function verifyLinks($htmlFile) {
		$dom = new DOMDocument();
        $dom->loadHTMLFile($htmlFile);
        $toCheck = array();
        foreach ($dom->getElementsByTagName('img') as $img) {
        	$toCheck[] = $img->getAttribute('src');
        };
        foreach ($dom->getElementsByTagName('script') as $img) {
        	$toCheck[] = $img->getAttribute('src');
        };
        foreach ($dom->getElementsByTagName('link') as $img) {
        	$toCheck[] = $img->getAttribute('href');
        };
        foreach ($toCheck as $url) {
        	if (!empty($url)) {
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $url);
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 20 );
				
				// make "HEAD" request
				curl_setopt( $ch, CURLOPT_HEADER, true );
				curl_setopt( $ch, CURLOPT_NOBODY, true );
				
				$res    = curl_exec( $ch );
				$res    = explode( ' ', substr( $res, 0, strpos( $res, "\n" ) ) );
				if (curl_error($ch)) {
					$this->fail('curl request failed');
				} else {
					$this->assertNotEqual($res[1], 404, $url.' not found');
				}
        	}
        }
	}
	
}

