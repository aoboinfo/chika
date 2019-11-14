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
    //
    protected $cityPlan; //都市計画区分
    //new added 2019/11/14
    protected $usagePlan; //用途区分
    protected $firePlan; //防火区分
    protected $forestPlan; //森林区分
    protected $parkPlan; //公園区分
    //new added end.
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
    protected $roadFrontStatus;
    protected $roadPavement;
    protected $sideRoad;
    protected $sideRroadDirection;
    protected $aboutTransportArea;
    protected $aboutNear;
    protected $buildCoverage;
    protected $floorAreaRatio;
    //
    protected $city;
    protected $seqNo;
    //2019/11/07
    protected $usage;
    //only getter
    protected $systemNo;
    protected $waterLabel;
    protected $gasLabel;
    protected $sewageLabel;
    protected $structureLabel;
    //2019/11/07
    protected $usageLabel;

    /**
     * @return mixed
     */
    public function getUsageLabel()
    {
        /*000：住宅地 003：宅地見込地 005：商業地 007：準工業地 009：工業地 010：市街化調整区域内の現況宅地 013：市街化調整区域内の現況林地*/
        $ret = "";
        switch ($this->usage) {
            case "000":
                $ret = "住宅地";
                break;
            case "003":
                $ret = "宅地見込地";
                break;
            case "005":
                $ret = "商業地";
                break;
            case "007":
                $ret = "準工業地";
                break;
            case "009":
                $ret = "工業地";
                break;
            case "010":
                $ret = "市街化調整区域内の現況宅地";
                break;
            case "020":
                $ret = "林地";
                break;
            default:
                $ret = "市街化調整区域内の現況林地";
        }
        return $ret;
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


    private function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

/**
     * @return mixed
     */
    public function getStructureLabel()
    {
        $ret = "";
        $leftStr = "";
        if ($this->startsWith($this->structure, "SRC")) {
            $ret = $ret . "鉄骨・鉄筋コンクリート造";
            $leftStr = substr($this->structure,3);
        } else if ($this->startsWith($this->structure, "RC")) {
            $ret = $ret . "鉄筋コンクリート造";
            $leftStr = substr($this->structure,2);
        } else if ($this->startsWith($this->structure, "S")) {
            $ret = $ret . "鉄骨造";
            $leftStr = substr($this->structure,1);
        } else if ($this->startsWith($this->structure, "LS")) {
            $ret = $ret . "軽量鉄骨造";
            $leftStr = substr($this->structure,2);
        } else if ($this->startsWith($this->structure, "B")) {
            $ret = $ret . "ブロック造";
            $leftStr = substr($this->structure,1);
        } else if ($this->startsWith($this->structure, "W")) {
            $ret = $ret . "木造";
            $leftStr = substr($this->structure,1);
        }
        //
        if ($leftStr && strlen($leftStr) > 0) {
            $leftStr = strtoupper($leftStr);
            $pos1 = stripos($leftStr, "F");
            if ($pos1 === false) {
                return $ret . $leftStr."階";
            }
            $pieces = explode("F", $leftStr);
            $parts = count($pieces);
            if ($parts > 1) {
                $BfloorStr = substr($pieces[1], 0, -1);
                $ret = $ret . $pieces[0] . "階地下" . $BfloorStr . "階";
            } else {
                $ret = $ret . $pieces[0] . "階";
            }
        }
        return $ret;
    }

    /**
     * @return mixed
     */
    public function getWaterLabel()
    {
        if ($this->getWater() == '1') {
            return "水道";
        } else {
            return "";
        }
    }

    /**
     * @return mixed
     */
    public function getGasLabel()
    {
        if ($this->getGas() == '1') {
            return "ガス";
        } else {
            return "";
        }
    }

    /**
     * @return mixed
     */
    public function getSewageLabel()
    {
        if ($this->getSewage() == '1') {
            return "下水";
        } else {
            return "";
        }
    }
    /**
     * @return mixed
     */
    public function getSystemNo()
    {
        return $this->getCity() . (int)$this->getSeqNo();
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getSeqNo()
    {
        return $this->seqNo;
    }

    /**
     * @param mixed $seqNo
     */
    public function setSeqNo($seqNo)
    {
        $this->seqNo = $seqNo;
    }

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
        if (trim($this->useDesc) == "") {
            return "-";
        }
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
        if(trim($this->config) == "") {
            return "-";
        }
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
        if (trim($this->roadDirection) == "") {
            return "-";
        }
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
        if (trim($this->roadWidth) == "") {
            return "-";
        }
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
    public function getRoadFrontStatus()
    {
        if (trim($this->roadFrontStatus) == "") {
            return "-";
        }
        return $this->roadFrontStatus;
    }

    /**
     * @param mixed $roadFrontStatus
     */
    public function setRoadFrontStatus($roadFrontStatus)
    {
        $this->roadFrontStatus = $roadFrontStatus;
    }

    /**
     * @return mixed
     */
    public function getRoadPavement()
    {
        if (trim($this->roadPavement) == "") {
            return "-";
        }
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
        if (trim($this->sideRoad) == "") {
            return "-";
        }
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
        if (trim($this->sideRroadDirection) == "") {
            return "-";
        }
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
        if (trim($this->aboutTransportArea) == "") {
            return "-";
        }
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
//new added from here
    /**
     * @return mixed
     */
    public function getUsagePlan()
    {
        return $this->usagePlan;
    }

    /**
     * @param mixed $usagePlan
     */
    public function setUsagePlan($usagePlan)
    {
        $this->usagePlan = $usagePlan;
    }

    /**
     * @return mixed
     */
    public function getFirePlan()
    {
        return $this->firePlan;
    }

    /**
     * @param mixed $firePlan
     */
    public function setFirePlan($firePlan)
    {
        $this->firePlan = $firePlan;
    }

    /**
     * @return mixed
     */
    public function getForestPlan()
    {
        return $this->forestPlan;
    }

    /**
     * @param mixed $forestPlan
     */
    public function setForestPlan($forestPlan)
    {
        $this->forestPlan = $forestPlan;
    }

    /**
     * @return mixed
     */
    public function getParkPlan()
    {
        return $this->parkPlan;
    }

    /**
     * @param mixed $parkPlan
     */
    public function setParkPlan($parkPlan)
    {
        $this->parkPlan = $parkPlan;
    }
}