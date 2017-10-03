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
    private $router;
    private $db;
    private $logger;

    private $recCount;

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
        while ($row = mysqli_fetch_assoc($surveyHistory)) {
            $surveyPrices[] = $row["avg_price"];
        }
        for ($i = count($surveyPrices); $i < $this->getRecCount(); $i++) {
            $surveyPrices[] = "0";
        }
        //
        $result = ["labels"=>$labels, "postPrices"=>$postPrices, "surveyPrices"=>$surveyPrices];
        //
        $res = $response->withJson($result)
            ->withHeader('Content-type', 'application/json');
        return $res;
    }
    //Price information for changeRate.
    public function changeRate ($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $cityName = $request->getAttribute('city');
        //
        $changeRatePost = null;
        $changeRateSurvey = null;
        $postChangeRateQuery = "select a.prefecture as label, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from posted_his as a inner join posted_his as b on a.id = b.id";
        $surveyChangeRateQuery = "select a.prefecture, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from survey_his as a inner join survey_his as b on a.id = b.id";
        if (is_null($prefecture) && is_null($cityName)) {
            $changeRatePost = $this->db->query($postChangeRateQuery . " where a.year = 2017 and b.year = 2016 and b.price <> 0 group by a.prefecture order by a.prefecture");
            $changeRateSurvey = $this->db->query($surveyChangeRateQuery . " where a.year = 2016 and b.year = 2015 and b.price <> 0 group by a.prefecture order by a.prefecture");
        } else if (!is_null($prefecture) && is_null($cityName)) {//for prefecture
            $postChangeRateQuery = "select c.city as label, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from posted_his as a inner join posted_his as b on a.id = b.id
                                      inner join posted_price as c on c.id = a.id where a.year = 2017 and b.year = 2016 and b.price <> 0 and c.address like '" . $prefecture . "%' group by c.city order by c.city";
            $surveyChangeRateQuery = "select c.city as label, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from survey_his as a inner join survey_his as b on a.id = b.id
                                      inner join surveyed_price as c on c.id = a.id where a.year = 2016 and b.year = 2015 and b.price <> 0 and c.address like '" . $prefecture . "%' group by c.city order by c.city";
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
            $postedCityPlanPrefecture = $this->db->query("select current_use, count(*) as u_count from posted_price where address like '" . $prefecture . "%' group by current_use order by u_count desc");
            while($row = mysqli_fetch_assoc($postedCityPlanPrefecture)) {
                $cityPlan = ["usage" => $row["current_use"], "count" => $row["u_count"]];
                //
                $postedUsages[] = $cityPlan;
            }
            $postedCityPlanPrefecture->close();
            //
            $surveyedCityPlanPrefecture = $this->db->query("select current_use, count(*) as u_count from surveyed_price where address like '" . $prefecture . "%' group by current_use order by u_count desc");
            while($row = mysqli_fetch_assoc($postedCityPlanPrefecture)) {
                $cityPlan = ["usage" => $row["current_use"], "count" => $row["u_count"]];
                //
                $surveyedUsages[] = $cityPlan;
            }
            $surveyedCityPlanPrefecture->close();

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

}