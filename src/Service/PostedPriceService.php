<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/08/20
 * Time: 14:07
 */

namespace Service;

use Slim\Router;
use Monolog\Logger;
use Mysqli;
use Price\LandPrice;

class PostedPriceService
{
    //
    const RET_MSG = "msg";
    const RET_NG = "NG";
    const RET_OK = "OK";
    //
    private $router;
    private $db;
    private $logger;
    private $recCount;
    //

    /**
     * @return mixed
     */
    public function getRecCount()
    {
        return $this->recCount;
    }

    /**
     * @param mixed $recCount
     */
    public function setRecCount($recCount)
    {
        $this->recCount = $recCount;
    }

    public function __construct(Router $router, Mysqli $db, Logger $logger)
    {
        $this->db = $db;
        $this->router = $router;
        $this->logger = $logger;
    }
    private function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    private function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    public function detail($request, $response, $params) {
        $this->logger->info("TEST");
        $landID = $request->getParsedBodyParam('id');
        $userID = $request->getParsedBodyParam('user');
        $userQuery = $this->db->query("SELECT * FROM mapuser WHERE map_user='" . $userID . "'");
        $ret = [];
        if ($userQuery->num_rows == 0) {
            $ret = ["Result"=>"NG", "msg"=>""];
        }
        $detailSQL = "SELECT a.lat,a.lng,a.usage_id,a.seqNo,a.city,a.address,a.acreage,a.current_use,a.use_desc,a.build_structure,a.water,a.gas,a.sewage,a.config,a.front_ratio,a.depth_ratio,a.num_of_floors,a.num_of_basefloors,a.front_roads,a.road_direction,a.road_width,a.road_front_status,a.road_pavement,a.side_road,a.side_road_direction,a.about_transport_area,a.about_near,a.near_station,a.distance_station,a.city_usage,a.city_fire,a.city_plan,a.city_forest,a.city_park,a.build_coverage,a.floor_area_ratio,FORMAT(b.price,0) as price";
        $subSql ="SELECT year, price FROM ";
        if ($this->startsWith($landID,"L1")) {
            $detailSQL = $detailSQL . " FROM posted_price AS a INNER JOIN posted_his AS b ON a.id = b.id AND a.year = b.year";
            $subSql = $subSql . "posted_his";
        } else {
            $detailSQL = $detailSQL . " FROM surveyed_price AS a INNER JOIN survey_his AS b ON a.id = b.id AND a.year = b.year";
            $subSql = $subSql . "survey_his";
        }
        $detailSQL .= " WHERE a.id='" . $landID . "'";
        $subSql .= " WHERE id = '". $landID."' ORDER BY year";
        //
        $detailQuery = $this->db->query($detailSQL);
        $detailRec = $detailQuery->fetch_assoc();
        $landPrice = new LandPrice();
        $landPrice->setUsage($detailRec['usage_id']);
        $landPrice->setWater($detailRec['water']);
        $landPrice->setGas($detailRec['gas']);
        $landPrice->setSewage($detailRec['sewage']);
        $landPrice->setStructure($detailRec['build_structure']);
        $detailQuery->close();
        $history = array();
        $historyQuery = $this->db->query($subSql);
        $oldPrice = 0;
        while ($hisRow = $historyQuery->fetch_assoc()) {
            $currentPrice = (int)$hisRow['price'];
            $priceRate = 0;
            if ($oldPrice == 0) {
                $priceRate = 0;
            } else {
                $priceRate = round(($currentPrice - $oldPrice)*100/(int)$oldPrice, 2);
            }
            $oldPrice = $currentPrice;
            $hisItem = [
                "year" => $hisRow['year'],
                "price" => $hisRow['price'],
                "rate" => $priceRate
            ];
            $history[] = $hisItem;
        }
        $res = $response->withJson([
            "landNo"=>$detailRec['seqNo'],
            "address"=>$detailRec['address'],
            "city"=>$detailRec['city'],
            "price"=>$detailRec['price'],
            "acreage"=>$detailRec['acreage'],
            "station"=>$detailRec['near_station'] . " " . $detailRec['distance_station'] . "m",
            "structure"=>$detailRec['build_structure'],
            "usage"=>$detailRec['current_use'],
            "facility"=> $landPrice->getWaterLabel() . " " . $landPrice->getGasLabel() . " " . $landPrice->getSewageLabel(),
            "config"=>$detailRec['config'],
            "typeRatio"=>$detailRec['front_ratio'] . ", " . $detailRec['depth_ratio'],
            "floor"=>$detailRec['num_of_floors'],
            "subfloor"=>$detailRec['num_of_basefloors'],
            "road"=>$detailRec['front_roads'],
            "roadDir"=>$detailRec['road_direction'],
            "roadWidth"=>$detailRec['road_width'],
            "pavement"=>$detailRec['road_pavement'],
            "sideRoad"=>$detailRec['side_road'],
            "sideRoadDir"=>$detailRec['side_road_direction'],
            "buildingCover"=>$detailRec['build_coverage'],
            "floorRatio"=>$detailRec['floor_area_ratio'],
            "cityUsage"=>$detailRec['city_usage'],
            "cityFire"=>$detailRec['city_fire'],
            "plan"=>$detailRec['city_plan'],
            "cityForest"=>$detailRec['city_forest'],
            "cityPark" => $detailRec['city_park'],
            "remark"=>$detailRec['use_desc'],
            "latLng"=>$detailRec['lat'] . " " . $detailRec['lng'],
            "road_front_status"=>$detailRec['road_front_status'],
            "about_transport_area"=>$detailRec['about_transport_area'],
            "about_near"=>$detailRec['about_near'],
            "his"=>$history
        ])->withHeader('Content-type', 'application/json;charset=utf-8');
        return $res;
    }

    public function historyPriceOf ($request, $response, $params) {
       // $cities = $this->db->query("select distinct city from posted_price where address like '" . $params['prefecture'] . "%'");
        $prefecture = $request->getAttribute('prefecture');
        $city = $request->getAttribute('city');
        //
        $commonQuery = "select year, ROUND(AVG(price)) as avg_price from";
        $postPriceQuery = ""; //公示
        $surveyPriceQuery = ""; //調査
        //
        if (is_null($prefecture) && is_null($city)) { //
            $postPriceQuery = $commonQuery . " posted_his where price <> 0 group by year order by year";
            $surveyPriceQuery = $commonQuery . " survey_his where price <> 0 group by year order by year";
        } else if (!is_null($prefecture) && is_null($city)) { //
            $postPriceQuery = $commonQuery . " posted_his where price <> 0 and prefecture = '" . $prefecture . "' group by year order by year";
            $surveyPriceQuery = $commonQuery . " survey_his where price <> 0 and prefecture = '" . $prefecture . "' group by year order by year";
        } else {//
            $postPriceQuery = "select a.year, round(avg(a.price)) as avg_price from posted_his as a inner join posted_price as b on a.id = b.id";
            $postPriceQuery = $postPriceQuery . " where a.prefecture = '" . $prefecture . "' and b.city = '" . $city .  "'";
            $postPriceQuery = $postPriceQuery . " and a.price <> 0 group by a.year order by a.year";
            //
            $surveyPriceQuery = "select a.year, round(avg(a.price)) as avg_price from survey_his as a inner join surveyed_price as b on a.id = b.id";
            $surveyPriceQuery = $surveyPriceQuery . " where a.prefecture = '" . $prefecture . "' and b.city = '" . $city .  "'";
            $surveyPriceQuery = $surveyPriceQuery . " and a.price <> 0 group by a.year order by a.year";
        }
        //post prices
        $postHistory = $this->db->query($postPriceQuery);
        $labels = array();
        $postPrices = array();
        while ($row = mysqli_fetch_assoc($postHistory)) {
            $labels[] = $row["year"];
            $postPrices[] = $row["avg_price"];
        }
        $postHistory->close();
        $this->setRecCount(count($labels));
        //
        //survey prices
        $surveyHistory = $this->db->query($surveyPriceQuery);
        $surveyPrices = array();
        //
        if ($this->getRecCount() > 0) {
            while ($row = mysqli_fetch_assoc($surveyHistory)) {
                $surveyPrices[] = $row["avg_price"];
            }
            for ($i = count($surveyPrices); $i < $this->getRecCount(); $i++) {
                $surveyPrices[] = "0";
            }
        } else {
            while ($row = mysqli_fetch_assoc($surveyHistory)) {
                $labels[] = $row["year"];
                $surveyPrices[] = $row["avg_price"];
            }
        }

        //
        $result = ["labels"=>$labels, "postPrices"=>$postPrices, "surveyPrices"=>$surveyPrices];
        //
        $res = $response->withJson($result)
            ->withHeader('Content-type', 'application/json;charset=utf-8');
        return $res;
    }
    //Price information for changeRate.
    public function changeRate ($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $cityName = $request->getAttribute('city');
        //
        $noticeQuery = $this->db->query("select current_year from notice order by created desc LIMIT 1");
        $row = $noticeQuery->fetch_assoc();
        $currentYear = $row['current_year'];
        $theYearBefore = (int)$currentYear - 1;
        $noticeQuery->close();
        //
        $changeRatePost = null;
        $changeRateSurvey = null;
        $postChangeRateQuery = "select a.prefecture as label, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from posted_his as a inner join posted_his as b on a.id = b.id";
        $surveyChangeRateQuery = "select a.prefecture, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from survey_his as a inner join survey_his as b on a.id = b.id";
        if (is_null($prefecture) && is_null($cityName)) {
            $changeRatePost = $this->db->query($postChangeRateQuery . " where a.year = 2017 and b.year = 2016 and b.price <> 0 group by a.prefecture order by a.prefecture");
            $changeRateSurvey = $this->db->query($surveyChangeRateQuery . " where a.year = 2017 and b.year = 2016 and b.price <> 0 group by a.prefecture order by a.prefecture");
        } else if (!is_null($prefecture) && is_null($cityName)) {//for prefecture
            $postChangeRateQuery = "select c.city as label, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from posted_his as a inner join posted_his as b on a.id = b.id
                                      inner join posted_price as c on c.id = a.id where a.year = " . $currentYear . " and b.year = " . $theYearBefore . " and b.price <> 0 and c.address like '" . $prefecture . "%' group by c.city order by c.city";
            $surveyChangeRateQuery = "select c.city as label, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from survey_his as a inner join survey_his as b on a.id = b.id
                                      inner join surveyed_price as c on c.id = a.id where a.year = " . $currentYear . " and b.year = " . $theYearBefore . " and b.price <> 0 and c.address like '" . $prefecture . "%' group by c.city order by c.city";
            $changeRatePost = $this->db->query($postChangeRateQuery);
            $changeRateSurvey = $this->db->query($surveyChangeRateQuery);
        } else {//for city

        }
        $labels = array();
        $ratesPost = array();
        while ($row = mysqli_fetch_assoc($changeRatePost)) {
            $labels[] = $row["label"];
            $ratesPost[] = $row["rate"];
        }
        $changeRatePost->close();
        //
        $ratesSurvey = array();
        while ($row = mysqli_fetch_assoc($changeRateSurvey)) {
            $ratesSurvey[] = $row["rate"];
        }
        //
        $result = ["labels" => $labels, "postRates" => $ratesPost, "surveyRates"=>$ratesSurvey];
        $res = $response->withJson($result)
            ->withHeader('Content-type', 'application/json;charset=utf-8');
        return $res;
    }
    //items on map and the data for city plan and current usage.
    public function itemsOnMap ($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $city = $request->getAttribute('city');
        $postedItems = array();
        $surveyedItems = array();
        if (!is_null($prefecture) && !is_null($city)) {//for prefecture
            $mapItemPosted = "select lat, lng, price0, price1, address from post_price where city = '" . $city . "' and address like '" . $prefecture . "%'";
            $postedOnMap = $this->db->query($mapItemPosted);
            //Must not use class for json in Json only output.
            while ($row = mysqli_fetch_assoc($postedOnMap)) {
                $landPrice = ["lat"=>$row["lat"], "lng"=>$row["lng"], "price0"=>$row["price0"], "price1"=>$row["price1"], "address"=>$row["address"]];
                $postedItems[] = $landPrice;
            }
            $postedOnMap->close();
            //
            $mapItemSurveyed = "select lat, lng, price0, price1, address from survey_price where city = '" . $city . "' and address like '" . $prefecture . "%'";
            $surveyedOnMap = $this->db->query($mapItemSurveyed);
            //Must not use class for json in Json only output.
            while ($row = mysqli_fetch_assoc($surveyedOnMap)) {
                $landPrice = ["lat"=>$row["lat"], "lng"=>$row["lng"], "price0"=>$row["price0"], "price1"=>$row["price1"], "address"=>$row["address"]];
                $surveyedItems[] = $landPrice;
            }
            $surveyedOnMap->close();
            $res = $response->withJson(["postedItems"=> $postedItems, "surveyedItems"=>$surveyedItems])
                ->withHeader('Content-type', 'application/json;charset=utf-8');

            return $res;
        } else {//for city
            return $response;
        }

    }

    //items on map and the data for city plan and current usage.
    public function itemsForCityPlanAndUsage ($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $city = $request->getAttribute('city');
        //
        $postedUsages = array();
        $surveyedUsages = array();
        $postedCityPlans = array();
        $surveyedCityPlans = array();
        //
        if (!is_null($prefecture) && is_null($city)) {
            $postedUsagesPrefecture = $this->db->query("select current_use, count(*) as u_count from posted_price where address like '" . $prefecture . "%' group by current_use order by u_count desc");
            while($row = mysqli_fetch_assoc($postedUsagesPrefecture)) {
                $cityPlan = ["usage" => $row["current_use"], "count" => $row["u_count"]];
                //
                $postedUsages[] = $cityPlan;
            }
            $postedUsagesPrefecture->close();
            //
            $postedCityPlanCity = $this->db->query("select city_plan, count(*) as u_count from posted_price where address like '" . $prefecture . "%' group by city_plan order by u_count desc");
            while($row = mysqli_fetch_assoc($postedCityPlanCity)) {
                $cityPlan = ["cityPlan" => $row["city_plan"], "count" => $row["u_count"]];
                //
                $postedCityPlans[] = $cityPlan;
            }
            $postedCityPlanCity->close();
            //
            $surveyedUsagesPrefecture = $this->db->query("select current_use, count(*) as u_count from surveyed_price where address like '" . $prefecture . "%' group by current_use order by u_count desc");
            while($row = mysqli_fetch_assoc($surveyedUsagesPrefecture)) {
                $cityPlan = ["usage" => $row["current_use"], "count" => $row["u_count"]];
                //
                $surveyedUsages[] = $cityPlan;
            }
            $surveyedUsagesPrefecture->close();
            //
            $surveyedCityPlanCity = $this->db->query("select city_plan, count(*) as u_count from surveyed_price where address like '" . $prefecture . "%' group by city_plan order by u_count desc");
            while($row = mysqli_fetch_assoc($surveyedCityPlanCity)) {
                $cityPlan = ["cityPlan" => $row["city_plan"], "count" => $row["u_count"]];
                //
                $surveyedCityPlans[] = $cityPlan;
            }
            $surveyedCityPlanCity->close();

        } else if (!is_null($prefecture) && !is_null($city)) {//for city
            $postedUsageCity = $this->db->query("select current_use, count(*) as u_count from posted_price where address like '" . $prefecture . "%' and city = '" . $city . "' group by current_use order by u_count desc");
            while($row = mysqli_fetch_assoc($postedUsageCity)) {
                $cityPlan = ["usage" => $row["current_use"], "count" => $row["u_count"]];
                //
                $postedUsages[] = $cityPlan;
            }
            $postedUsageCity->close();
            //
            $postedCityPlanCity = $this->db->query("select city_plan, count(*) as u_count from posted_price where address like '" . $prefecture . "%' and city = '" . $city . "' group by city_plan order by u_count desc");
            while($row = mysqli_fetch_assoc($postedCityPlanCity)) {
                $cityPlan = ["cityPlan" => $row["city_plan"], "count" => $row["u_count"]];
                //
                $postedCityPlans[] = $cityPlan;
            }
            $postedCityPlanCity->close();
            //
            $surveyedUsageCity = $this->db->query("select current_use, count(*) as u_count from surveyed_price where address like '" . $prefecture . "%' and city = '" . $city . "' group by current_use order by u_count desc");
            while($row = mysqli_fetch_assoc($surveyedUsageCity)) {
                $cityPlan = ["usage" => $row["current_use"], "count" => $row["u_count"]];
                //
                $surveyedUsages[] = $cityPlan;
            }
            $surveyedUsageCity->close();
            //
            $surveyedCityPlanCity = $this->db->query("select city_plan, count(*) as u_count from surveyed_price where address like '" . $prefecture . "%' and city = '" . $city . "' group by city_plan order by u_count desc");
            while($row = mysqli_fetch_assoc($surveyedCityPlanCity)) {
                $cityPlan = ["cityPlan" => $row["city_plan"], "count" => $row["u_count"]];
                //
                $surveyedCityPlans[] = $cityPlan;
            }
            $surveyedCityPlanCity->close();

        } else {//for city
            $res = $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write('No result');
            return $res;
        }
        $res = $response->withJson(["postedUsages"=> $postedUsages, "surveyedUsages"=>$surveyedUsages,"postedCityPlans" => $postedCityPlans,"surveyedCityPlans"=>$surveyedCityPlans])
            ->withHeader('Content-type', 'application/json;charset=utf-8');
        return $res;

    }
    public function detailForAddress($request, $response, $params)
    {
        $data = $request->getParsedBody();
        $this->logger->info($data["type"]);
        $this->logger->info($data["address"]);
        $priceType = $data["type"];
        $address = $data["address"];
        $queryComm = "select id, price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, city_plan from ";

        if ($priceType == 0) { //post
            $resultQuery = $this->db->query($queryComm . " post_price where address like '%" . $address . "%' order by price0 desc limit 101");
            if ($resultQuery->num_rows == 0 || $resultQuery->num_rows > 100) {
                return $this->overSizeMessage($resultQuery->num_rows, $response);
            } else {
                return $this->searchResult($resultQuery, $response);
            }
        } else if ($priceType == 1) {//survey
            $resultQuery = $this->db->query($queryComm . " survey_price where address like '%" . $address . "%' order by price0 desc limit 101");
            if ($resultQuery->num_rows == 0 || $resultQuery->num_rows > 100) {
                return $this->overSizeMessage($resultQuery->num_rows, $response);
            } else {
                return $this->searchResult($resultQuery, $response);
            }
        } else {//both
            $resultQuery0 = $this->db->query($queryComm . " post_price where address like '%" . $address . "%' order by price0 desc limit 101");
            $resultQuery1 = $this->db->query($queryComm . " survey_price where address like '%" . $address . "%' order by price0 desc limit 101");
            $totalRecordCount = $resultQuery0->num_rows + $resultQuery1->num_rows;
            if ($totalRecordCount == 0 || $totalRecordCount > 200) {
                return $this->overSizeMessage($totalRecordCount, $response);
            } else {
                $result = array();
                while($row = mysqli_fetch_assoc($resultQuery0)) {
                    $record = ["id"=>$row["id"], "price"=>"¥" . number_format($row["price0"]),
                        "rate"=>$row["rate"],
                        "address"=>$row["address"],
                        "station"=>$row["near_station"],
                        "distance"=>$row["distance_station"],
                        "usage"=> $row["current_use"],
                        "structure"=>$row["build_structure"],
                        "cityPlan"=>$row["city_plan"]
                    ];
                    $result[] = $record;
                }
                $resultQuery0->close();
                while($row = mysqli_fetch_assoc($resultQuery1)) {
                    $record = ["id"=>$row["id"], "price"=>"¥" . number_format($row["price0"]),
                        "rate"=>$row["rate"],
                        "address"=>$row["address"],
                        "station"=>$row["near_station"],
                        "distance"=>$row["distance_station"],
                        "usage"=> $row["current_use"],
                        "structure"=>$row["build_structure"],
                        "cityPlan"=>$row["city_plan"]
                    ];
                    $result[] = $record;
                }
                $resultQuery1->close();
                $newResponse = $response->withJson([PostedPriceService::RET_MSG=>PostedPriceService::RET_OK, "result"=>$result])
                    ->withHeader('Content-type', 'application/json;charset=utf-8');
                return $newResponse;
            }

        }
    }
    //Send notice message to user.
    public function showNotice($request, $response, $params) {
        $noticeQuery = $this->db->query("select notice, DATE_FORMAT(created, '%Y年%m月%d日') as day from notice order by created desc");
        $result = array();
        while ($row = $noticeQuery->fetch_assoc()) {
            $record = ["notice"=>$row["notice"],
                "created"=> $row["day"]];
            $result[] = $record;
        }
        $noticeQuery->close();
        $newResponse = $response->withJson(["result"=>$result])
            ->withHeader('Content-type', 'application/json;charset=utf-8');
        return $newResponse;
    }
    //private functions
    private function overSizeMessage($recordSize, $res) {
        if ($recordSize == 0) {
            $newResponse = $res->withJson([PostedPriceService::RET_MSG=>PostedPriceService::RET_NG, "msg_idx"=>"0"])
                ->withHeader('Content-type', 'application/json;charset=utf-8');
            return $newResponse;
        } else if ($recordSize > 100 && $recordSize < 200) {
            $newResponse = $res->withJson([PostedPriceService::RET_MSG=>PostedPriceService::RET_NG, "msg_idx"=>"1"])
                ->withHeader('Content-type', 'application/json;charset=utf-8');
            return $newResponse;
        } else {
            $newResponse = $res->withJson([PostedPriceService::RET_MSG => PostedPriceService::RET_NG, "msg_idx" => "3"])
                ->withHeader('Content-type', 'application/json;charset=utf-8');
            return $newResponse;
        }
    }
    private function searchResult($queryObj, $res) {
        $result = array();
        while($row = mysqli_fetch_assoc($queryObj)) {
            $record = ["id"=>$row["id"], "price"=>"¥" . number_format($row["price0"]),
                "rate"=>$row["rate"],
                "address"=>$row["address"],
                "station"=>$row["near_station"],
                "distance"=>$row["distance_station"],
                "usage"=> $row["current_use"],
                "structure"=>$row["build_structure"],
                "cityPlan"=>$row["city_plan"]
            ];
            $result[] = $record;
        }
        $queryObj->close();
        $newResponse = $res->withJson([PostedPriceService::RET_MSG=>PostedPriceService::RET_OK, "result"=>$result, "msg_idx"=>"2"])
            ->withHeader('Content-type', 'application/json;charset=utf-8');
        return $newResponse;
    }
}