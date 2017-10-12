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
    //latest added
    protected $acreage;
    protected $useDesc;
    protected $water;
    protected $gas;
    protected $sewage;
    protected $config;
    protected $frontRatio;
    protected $depthRatio;
    protected $numOfFloors;
    protected $numOfBasefloors;
    protected $frontRoads;
    protected $roadDirection;
    protected $roadWidth;
    protected $roadRrontStatus;
    protected $roadPavement;
    protected $sideRoad;
    protected $sideRroadDirection;
    protected $aboutTransportArea;
    protected $aboutNear;
    protected $buildCoverage;
    protected $floorAreaRatio;

    /**
     * @return mixed
     */
    public function getAcreage()
    {
        return $this->acreage;
    }

    /**
     * @param mixed $acreage
     */
    public function setAcreage($acreage)
    {
        $this->acreage = $acreage;
    }

    /**
     * @return mixed
     */
    public function getUseDesc()
    {
        return $this->useDesc;
    }

    /**
     * @param mixed $useDesc
     */
    public function setUseDesc($useDesc)
    {
        $this->useDesc = $useDesc;
    }

    /**
     * @return mixed
     */
    public function getWater()
    {
        return $this->water;
    }

    /**
     * @param mixed $water
     */
    public function setWater($water)
    {
        $this->water = $water;
    }

    /**
     * @return mixed
     */
    public function getGas()
    {
        return $this->gas;
    }

    /**
     * @param mixed $gas
     */
    public function setGas($gas)
    {
        $this->gas = $gas;
    }

    /**
     * @return mixed
     */
    public function getSewage()
    {
        return $this->sewage;
    }

    /**
     * @param mixed $sewage
     */
    public function setSewage($sewage)
    {
        $this->sewage = $sewage;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getFrontRatio()
    {
        return $this->frontRatio;
    }

    /**
     * @param mixed $frontRatio
     */
    public function setFrontRatio($frontRatio)
    {
        $this->frontRatio = $frontRatio;
    }

    /**
     * @return mixed
     */
    public function getDepthRatio()
    {
        return $this->depthRatio;
    }

    /**
     * @param mixed $depthRatio
     */
    public function setDepthRatio($depthRatio)
    {
        $this->depthRatio = $depthRatio;
    }

    /**
     * @return mixed
     */
    public function getNumOfFloors()
    {
        return $this->numOfFloors;
    }

    /**
     * @param mixed $numOfFloors
     */
    public function setNumOfFloors($numOfFloors)
    {
        $this->numOfFloors = $numOfFloors;
    }

    /**
     * @return mixed
     */
    public function getNumOfBasefloors()
    {
        return $this->numOfBasefloors;
    }

    /**
     * @param mixed $numOfBasefloors
     */
    public function setNumOfBasefloors($numOfBasefloors)
    {
        $this->numOfBasefloors = $numOfBasefloors;
    }

    /**
     * @return mixed
     */
    public function getFrontRoads()
    {
        return $this->frontRoads;
    }

    /**
     * @param mixed $frontRoads
     */
    public function setFrontRoads($frontRoads)
    {
        $this->frontRoads = $frontRoads;
    }

    /**
     * @return mixed
     */
    public function getRoadDirection()
    {
        return $this->roadDirection;
    }

    /**
     * @param mixed $roadDirection
     */
    public function setRoadDirection($roadDirection)
    {
        $this->roadDirection = $roadDirection;
    }

    /**
     * @return mixed
     */
    public function getRoadWidth()
    {
        return $this->roadWidth;
    }

    /**
     * @param mixed $roadWidth
     */
    public function setRoadWidth($roadWidth)
    {
        $this->roadWidth = $roadWidth;
    }

    /**
     * @return mixed
     */
    public function getRoadRrontStatus()
    {
        return $this->roadRrontStatus;
    }

    /**
     * @param mixed $roadRrontStatus
     */
    public function setRoadRrontStatus($roadRrontStatus)
    {
        $this->roadRrontStatus = $roadRrontStatus;
    }

    /**
     * @return mixed
     */
    public function getRoadPavement()
    {
        return $this->roadPavement;
    }

    /**
     * @param mixed $roadPavement
     */
    public function setRoadPavement($roadPavement)
    {
        $this->roadPavement = $roadPavement;
    }

    /**
     * @return mixed
     */
    public function getSideRoad()
    {
        return $this->sideRoad;
    }

    /**
     * @param mixed $sideRoad
     */
    public function setSideRoad($sideRoad)
    {
        $this->sideRoad = $sideRoad;
    }

    /**
     * @return mixed
     */
    public function getSideRroadDirection()
    {
        return $this->sideRroadDirection;
    }

    /**
     * @param mixed $sideRroadDirection
     */
    public function setSideRroadDirection($sideRroadDirection)
    {
        $this->sideRroadDirection = $sideRroadDirection;
    }

    /**
     * @return mixed
     */
    public function getAboutTransportArea()
    {
        return $this->aboutTransportArea;
    }

    /**
     * @param mixed $aboutTransportArea
     */
    public function setAboutTransportArea($aboutTransportArea)
    {
        $this->aboutTransportArea = $aboutTransportArea;
    }

    /**
     * @return mixed
     */
    public function getAboutNear()
    {
        return $this->aboutNear;
    }

    /**
     * @param mixed $aboutNear
     */
    public function setAboutNear($aboutNear)
    {
        $this->aboutNear = $aboutNear;
    }

    /**
     * @return mixed
     */
    public function getBuildCoverage()
    {
        return $this->buildCoverage;
    }

    /**
     * @param mixed $buildCoverage
     */
    public function setBuildCoverage($buildCoverage)
    {
        $this->buildCoverage = $buildCoverage;
    }

    /**
     * @return mixed
     */
    public function getFloorAreaRatio()
    {
        return $this->floorAreaRatio;
    }

    /**
     * @param mixed $floorAreaRatio
     */
    public function setFloorAreaRatio($floorAreaRatio)
    {
        $this->floorAreaRatio = $floorAreaRatio;
    }
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