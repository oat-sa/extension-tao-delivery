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
     * @param string $executionId
     * @param string $executionContextId
     * @param string $type
     * @param string $label
     *
     * @dataProvider dataProviderConstructInvalidValues
     */
    public function testConstructThrowsException($executionId, $executionContextId, $type, $label)
    {
        $this->expectException(\InvalidArgumentException::class);

        new DeliveryExecutionContext($executionId, $executionContextId, $type, $label);
    }

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
            'execution_id' => 'http://test-execution-uri.dev',
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

    /**
     * @return array
     */
    public function dataProviderConstructInvalidValues()
    {
        return [
            'Empty execution and context IDs' => [
                'executionId'           => '',
                'executionContextId'    => '',
                'type'                  => '',
                'label'                 => '',
            ],
            'Empty execution ID' => [
                'executionId'           => '',
                'executionContextId'    => 'TEST_CONTEXT_ID',
                'type'                  => '',
                'label'                 => '',
            ],
            'Empty context ID' => [
                'executionId'           => 'http://test-execution-uri.dev',
                'executionContextId'    => '',
                'type'                  => '',
                'label'                 => '',
            ],
            'Execution ID is not valid URI' => [
                'executionId'           => 'INVALID_URI',
                'executionContextId'    => 'TEST_CONTEXT_ID',
                'type'                  => '',
                'label'                 => '',
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataProviderTestCreateFromArray()
    {
        return [
            'Empty optional parameters' => [
                'contextData' => [
                    'execution_id' => 'http://test-execution-uri.dev',
                    'context_id' => 'TEST CONTEXT ID',
                ]
            ],
            'Correct values' => [
                'contextData' => [
                    'execution_id' => 'http://test-execution-uri.dev',
                    'context_id' => 'TEST CONTEXT ID',
                    'type' => 'TEST EXEC TYPE',
                    'label' => 'TEST LABEL'
                ]
            ]
        ];
    }
}

