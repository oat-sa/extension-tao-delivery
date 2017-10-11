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
 * Copyright (c) 2017  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoDelivery\test\model\delivery\sample;


use oat\taoDelivery\model\delivery\AbstractDeliveryService;
use oat\taoDelivery\model\delivery\Delivery;

class DeliveryServiceSample extends AbstractDeliveryService
{
    /**
     * @var array
     */
    private $storage = [
        'delivery1' => [
            'param1' => 'valueOfParameter1'
        ]
    ];

    protected function getParameterValue($id, $param = '')
    {
        return $this->parameterExists($id, $param) ? $this->storage[$id][$param] : null;
    }

    public function deliveryExists($id)
    {
        return isset($this->storage[$id]);
    }

    public function parameterExists($id, $param)
    {
        return $this->deliveryExists($id) && isset($this->storage[$id][$param]);
    }

    public function createDelivery(\core_kernel_classes_Class $deliveryClass, $label = '')
    {
        $uri = \common_Utils::getNewUri();
        $delivery = new Delivery($uri, $this);
        $delivery->setLabel($label);
        return $delivery;
    }

    public function setParameter($id, $param = '', $value)
    {
        if (!$this->deliveryExists($id)) {
            $this->storage[$id] = [];
        }
        $this->storage[$id][$param] = $value;
    }

    public function setParameters($id, array $params)
    {
        foreach ($params  as $param => $val) {
            $this->setParameter($id, $param, $val);
        }
    }
}
