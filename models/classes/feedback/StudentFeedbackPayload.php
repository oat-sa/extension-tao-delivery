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
 *
 *
 */

namespace oat\taoDelivery\models\classes\feedback;

class StudentFeedbackPayload implements \JsonSerializable
{

    private $title;

    private $descritpion;

    private $categories;

    private $thresholds;

    private $data;

    /**
     * StudentFeedbackPayload constructor.
     * @param string $title
     * @param string $descritpion
     * @param array $categories
     * @param array $thresholds
     * @param array $data
     */
    public function __construct($title = '', $descritpion = '', $categories = [], $thresholds = [], $data = [])
    {
        $this->setTitle($title);
        $this->setDescritpion($descritpion);
        $this->setCategories($categories);
        $this->setThresholds($thresholds);
        $this->setData($data);
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescritpion()
    {
        return $this->descritpion;
    }

    /**
     * @param mixed $descritpion
     */
    public function setDescritpion($descritpion)
    {
        $this->descritpion = $descritpion;
    }



    /**
     * @return array [["label" => "Number and Calculus", "key" => "number"],["label" => "Geometry", "key" => "space]]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories($categories)
    {
        $formattedCategories = [];
        foreach ($categories as $key => $value){
            $formattedCatgeories[] = ["label" => $value, "key" => $key];
        }
        $this->categories = $formattedCategories;
    }

    /**
     * @return array [["label" => "bad", "value" => 20],["label" => "good", "value" => 35]]
     */
    public function getThresholds()
    {
        return $this->thresholds;
    }

    /**
     * @param array $thresholds
     */
    public function setThresholds($thresholds)
    {
        $formattedThresholds = [];
        foreach ($thresholds as $key => $value){
            $formattedThresholds[] = ["label" => $key, "value" => $value];
        }
        $this->thresholds = $formattedThresholds;
    }

    /**
     * @return array ["number" => ["value" => 1, "error" => 0.2], "space" => ["value" => 3, "error" => 0.1]]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


    /**
     * @return array
     */
    function jsonSerialize()
    {

        return [
            'title' => $this->getTitle(),
            'categories' => $this->getCategories(),
            'thresholds' => $this->getThresholds(),
            'data' => $this->getData(),
            'success' => true
        ];
    }


}