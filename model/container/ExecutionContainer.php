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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoDelivery\model\container;

/**
 * Represents the Container used to render the actual Delivery
 * of a Delivery Execution
 */
interface ExecutionContainer
{
    /**
     * @param $url
     */
    public function setReturnUrl($url);

    /**
     * @param $url
     * @todo to be removed after finishing of https://oat-sa.atlassian.net/browse/TAO-3011
     */
    public function setFinishUrl($url);

    /**
     * Returns a renderer for additional header data, alowing
     * the container to add custom JS, CSS and meta-data
     *
     * @return \Renderer
     */
    public function getContainerHeader();

    /**
     * Returns a renderer for the actual container body
     *
     * @return \Renderer
     */
    public function getContainerBody();

}
