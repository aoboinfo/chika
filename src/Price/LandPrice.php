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
    protected $status;
    protected $structure;
    protected $usage;
    //
    protected $priceOfTubo; //坪単価

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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @param mixed $usage
     */
    public function setUsage($usage)
    {
        $this->usage = $usage;
    }

}