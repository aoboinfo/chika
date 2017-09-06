<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/07/28
 * Time: 11:46
 */

namespace Price;


class LandPrice
{
    protected $type; //公示、調査
    protected $price;
    protected $changeRate;
    protected $address;
    protected $station;
    protected $distanceFromStation;
    protected $structure;
    protected $currentUsage;
    protected $cityPlan;
    //
    protected $priceOfTubo; //坪単価
    protected $id;
    //
    protected $lat;
    protected $lng;

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param mixed $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }
    /**
     * @return mixed
     */
    public function getCityPlan()
    {
        return $this->cityPlan;
    }

    /**
     * @param mixed $cityPlan
     */
    public function setCityPlan($cityPlan)
    {
        $this->cityPlan = $cityPlan;
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return mixed
     */
    public function getPriceOfTubo()
    {
        return $this->priceOfTubo;
    }

    /**
     * @param mixed $priceOfTubo
     */
    public function setPriceOfTubo($priceOfTubo)
    {
        $this->priceOfTubo = $priceOfTubo;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getChangeRate()
    {
        return $this->changeRate;
    }

    /**
     * @param mixed $changeRate
     */
    public function setChangeRate($changeRate)
    {
        $this->changeRate = $changeRate;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * @param mixed $station
     */
    public function setStation($station)
    {
        $this->station = $station;
    }

    /**
     * @return mixed
     */
    public function getDistanceFromStation()
    {
        return $this->distanceFromStation;
    }

    /**
     * @param mixed $distanceFromStation
     */
    public function setDistanceFromStation($distanceFromStation)
    {
        $this->distanceFromStation = $distanceFromStation;
    }
    /**
     * @return mixed
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param mixed $structure
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return mixed
     */
    public function getCurrentUsage()
    {
        return $this->currentUsage;
    }

    /**
     * @param mixed $currentUsage
     */
    public function setCurrentUsage($currentUsage)
    {
        $this->currentUsage = $currentUsage;
    }



}