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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoDelivery\test\unit\model\execution;

use oat\generis\test\TestCase;
use oat\taoDelivery\model\execution\DeliveryExecutionContext;
use oat\taoDelivery\model\execution\DeliveryExecutionContextInterface;

class DeliveryExecutionContextTest extends TestCase
{
    /**
     * @param array $contextData
     * @dataProvider dataProviderTestCreateFromArray
     */
    public function testCreateFromArray(array $contextData)
    {
        $contextObject = DeliveryExecutionContext::createFromArray($contextData);

        $this->assertInstanceOf(
            DeliveryExecutionContextInterface::class,
            $contextObject,
            'Method must return an object of DeliveryExecutionContextInterface'
        );
    }

    public function testJsonSerialize()
    {
        $contextData = [
            'execution_id' => 'TEST EXECUTION ID',
            'context_id' => 'TEST CONTEXT ID',
            'type' => 'TEST EXEC TYPE',
            'label' => 'TEST LABEL'
        ];
        $contextObject = new DeliveryExecutionContext(
            $contextData['execution_id'],
            $contextData['context_id'],
            $contextData['type'],
            $contextData['label']
        );

        $result = $contextObject->jsonSerialize();

        $this->assertEquals($contextData, $result, 'jsonSerialize method must return array with correct data');
    }

    public function dataProviderTestCreateFromArray()
    {
        return [
            'Empty values' => [
                'contextData' => []
            ],
            'Correct values' => [
                'contextData' => [
                    'execution_id' => 'TEST EXECUTION ID',
                    'context_id' => 'TEST CONTEXT ID',
                    'type' => 'TEST EXEC TYPE',
                    'label' => 'TEST LABEL'
                ]
            ]
        ];
    }
}

