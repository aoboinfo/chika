<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/07/28
 * Time: 11:39
 */

namespace Price;

use Slim\Views\Twig;
use Slim\Router;
use Mysqli;
//
use Price\LandPrice;


class PrefectureController extends SearchController
{
    //
    public function showPricesFor ($request, $response, $params) {
        //menu
        $prefecture = $params['prefecture'];
        //The posted 10 stations
        $stationsDesc = $this->getTopStationListForTarget(SearchController::POST_VIEW, $prefecture, NULL);
        $stationAsc = $this->getLowStationListForTarget(SearchController::POST_VIEW, $prefecture, NULL);
        //
        $surveyStationsDesc = $this->getTopStationListForTarget(SearchController::SURVEY_VIEW, $prefecture, NULL);
        $surveyStationsAsc = $this->getTopStationListForTarget(SearchController::SURVEY_VIEW, $prefecture, NULL);

        //posted average price/year
        $averagePrices = $this->session->get($prefecture . "avgPrices", NULL);
        if ($averagePrices == NULL) {
            $posted_avg = $this->db->query("select year, FORMAT(ROUND(AVG(price)),0) as avg_price from posted_his where price <> 0 and prefecture = '" . mb_substr($prefecture, 0, 3) . "' group by year order by year");
            //
            $averagePrices = array();
            //
            while ($row = mysqli_fetch_assoc($posted_avg)) {
                $averagePrice = new AveragePrice();
                $averagePrice->setYear($row["year"]);
                $averagePrice->setPostedPrice($row["avg_price"]);
                $averagePrices[] = $averagePrice;
            }
            $posted_avg->close();

            $survey_avg = $this->db->query("select FORMAT(ROUND(AVG(price)),0) as avg_price from survey_his where price <> 0 and prefecture = '" . mb_substr($prefecture, 0, 3) . "' group by year order by year");
            $index = 0;
            while ($row = mysqli_fetch_assoc($survey_avg)) {
                $averagePrice = $averagePrices[$index];
                $averagePrice->setSurveyPrice($row["avg_price"]);
                $index++;
            }
            $survey_avg->close();
            //
            $this->session->set( $prefecture . "avgPrices", $averagePrices);
        }


        $postedTop10City = $this->session->get($prefecture . "postTopCity", NULL);
        if ($postedTop10City == NULL) {
            /*Ranking area*/
            $postedQuery10 = "select city, round(avg(price0)) as pre_price, round(avg(price0) * 3.305785) as tubo_price from post_price where address like '" . $prefecture ."%' group by city order by pre_price";
            //top 10 city
            $top10CityQry = $this->db->query($postedQuery10 . " desc limit 10");
            $postedTop10City = array();
            while ($row = mysqli_fetch_assoc($top10CityQry)) {
                $landPrice = new LandPrice();
                $landPrice->setPrice("¥" . number_format($row["pre_price"]));
                $landPrice->setAddress($row["city"]);
                $landPrice->setPriceOfTubo("¥" . number_format($row["tubo_price"]));
                $postedTop10City[] = $landPrice;
            }
            $top10CityQry->close();
            $this->session->set($prefecture . "postTopCity", $postedTop10City);
            //low 10 city
            $low10CityQry = $this->db->query($postedQuery10 . " limit 10");
            $postedLow10City = array();
            while ($row = mysqli_fetch_assoc($low10CityQry)) {
                $landPrice = new LandPrice();
                $landPrice->setPrice("¥" . number_format($row["pre_price"]));
                $landPrice->setAddress($row["city"]);
                $landPrice->setPriceOfTubo("¥" . number_format($row["tubo_price"]));
                $postedLow10City[] = $landPrice;
            }
            $low10CityQry->close();

            $this->session->set($prefecture . "postLowCity", $postedLow10City);
        }
        $postedLow10City = $this->session->get($prefecture . "postLowCity", NULL);
        //
        $surveyedTop10City = $this->session->get($prefecture . "surveyTopCity",  NULL);
        if ($surveyedTop10City == NULL) {
            $surveyQuery10 = "select city, round(avg(price0)) as pre_price, round(avg(price0) * 3.305785) as tubo_price  from survey_price where address like '" . $prefecture ."%' group by city order by pre_price";
            //top 10 city
            $surveyTop10CityQry = $this->db->query($surveyQuery10 . " desc limit 10");
            $surveyedTop10City = array();
            while ($row = mysqli_fetch_assoc($surveyTop10CityQry)) {
                $landPrice = new LandPrice();
                $landPrice->setPrice("¥" . number_format($row["pre_price"]));
                $landPrice->setAddress($row["city"]);
                $landPrice->setPriceOfTubo("¥" . number_format($row["tubo_price"]));
                $surveyedTop10City[] = $landPrice;
            }
            $surveyTop10CityQry->close();
            $this->session->set($prefecture . "surveyTopCity",  $surveyedTop10City);
            //low 10 city
            $surveyedLow10CityQry = $this->db->query($surveyQuery10 . " limit 10");
            $surveyedLow10City = array();
            while ($row = mysqli_fetch_assoc($surveyedLow10CityQry)) {
                $landPrice = new LandPrice();
                $landPrice->setPrice("¥" . number_format($row["pre_price"]));
                $landPrice->setAddress($row["city"]);
                $landPrice->setPriceOfTubo("¥" . number_format($row["tubo_price"]));
                $surveyedLow10City[] = $landPrice;
            }
            $surveyedLow10CityQry->close();
            $this->session->set($prefecture . "surveyLowCity",  $surveyedLow10City);
        }
        $surveyedLow10City = $this->session->get($prefecture . "surveyLowCity",  NULL);
        //
        $topPostPrice = $this->session->get($prefecture . "topPostPrices", NULL);
        if ($topPostPrice == NULL) {
            $postedPriceOfPrefecture = $this->db->query("select id, price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, usage_id from post_price where address like '" . $prefecture . "%' order by price0 desc limit 6");
            $topPostPrice = array();
            while ($row = mysqli_fetch_assoc($postedPriceOfPrefecture)) {
                $landPrice = new LandPrice();
                $landPrice->setId($row["id"]);
                $landPrice->setPrice("¥" . number_format($row["price0"]));
                $landPrice->setChangeRate($row["rate"]);
                $landPrice->setAddress($row["address"]);
                $landPrice->setStation($row["near_station"]);
                $landPrice->setDistanceFromStation($row["distance_station"]);
                $landPrice->setCurrentUsage($row["current_use"]);
                $landPrice->setStructure($row["build_structure"]);
                $landPrice->setUsage($row["usage_id"]);
                $topPostPrice[] = $landPrice;
            }
            $postedPriceOfPrefecture->close();
            $this->session->set($prefecture . "topPostPrices", $topPostPrice);
        }
        //
        $topSurveyPrice = $this->session->get($prefecture . "topSurveyPrices", NULL);
        if ($topSurveyPrice == NULL) {
            $surveyPriceOfPrefecture = $this->db->query("select id, price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, usage_id from survey_price where address like '" . $prefecture . "%' order by price0 desc limit 6");
            $topSurveyPrice = array();
            while ($row = mysqli_fetch_assoc($surveyPriceOfPrefecture)) {
                $landPrice = new LandPrice();
                $landPrice->setId($row["id"]);
                $landPrice->setPrice("¥" . number_format($row["price0"]));
                $landPrice->setChangeRate($row["rate"]);
                $landPrice->setAddress($row["address"]);
                $landPrice->setStation($row["near_station"]);
                $landPrice->setDistanceFromStation($row["distance_station"]);
                $landPrice->setCurrentUsage($row["current_use"]);
                $landPrice->setStructure($row["build_structure"]);
                $landPrice->setUsage($row["usage_id"]);
                $topSurveyPrice[] = $landPrice;
            }
            $surveyPriceOfPrefecture->close();
            $this->session->set($prefecture . "topSurveyPrices", $topSurveyPrice);
        }
        //
        $this->view->render($response, 'landprice/prefecture.twig',
            [
                "posted_title"=> $prefecture,
                "areas" => [$prefecture, $prefecture], //this is trick for create correct url. post and survey
                "prefecture" => $prefecture,
                "leftMenus" => [$this->getCityList(SearchController::POST_TABLE, $prefecture), $this->getCityList(SearchController::SURVEY_TABLE, $prefecture)],
                "title"=> $prefecture . "地価",
                "stationTop" => $stationsDesc,
                "stationLow" => $stationAsc,
                "surveyStationTop" => $surveyStationsDesc,
                "surveyStationLow" => $surveyStationsAsc,
                "avgPrices" => $averagePrices,
                "postTopCity" => $postedTop10City,
                "postLowCity" => $postedLow10City,
                "surveyTopCity" => $surveyedTop10City,
                "surveyLowCity" => $surveyedLow10City,
                "topPostPrices" => $topPostPrice,
                "topSurveyPrices" => $topSurveyPrice,
                "targetYear"=>$this->getTargetYear()
            ]
        );
    }

    public function showPricesForCity ($request, $response, $params) {
        $prefecture = $params['prefecture'];
        $city = $params['city'];
        //map contents
        $postedItemsOfCity = $this->db->query("select id, price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, usage_id from post_price where city = '" . $city . "' and address like '" . $prefecture . "%' order by price0 desc");
        $surveyItemsOfCity = $this->db->query("select id, price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, usage_id from survey_price where city = '" . $city . "' and address like '" . $prefecture . "%' order by price0 desc");
        $postResultOfCity = array();
        while ($row = mysqli_fetch_assoc($postedItemsOfCity)) {
            $landPrice = new LandPrice();
            $landPrice->setId($row["id"]);
            $landPrice->setPrice("¥" . number_format($row["price0"]));
            $landPrice->setChangeRate($row["rate"]);
            $landPrice->setAddress($row["address"]);
            $landPrice->setStation($row["near_station"]);
            $landPrice->setDistanceFromStation($row["distance_station"]);
            $landPrice->setCurrentUsage($row["current_use"]);
            $landPrice->setStructure($row["build_structure"]);
            $landPrice->setUsage($row["usage_id"]);
            $postResultOfCity[] = $landPrice;
        }
        $postedItemsOfCity->close();

        $surveyResultOfCity = array();
        while ($row = mysqli_fetch_assoc($surveyItemsOfCity)) {
            $landPrice = new LandPrice();
            $landPrice->setId($row["id"]);
            $landPrice->setPrice("¥" . number_format($row["price0"]));
            $landPrice->setChangeRate($row["rate"]);
            $landPrice->setAddress($row["address"]);
            $landPrice->setStation($row["near_station"]);
            $landPrice->setDistanceFromStation($row["distance_station"]);
            $landPrice->setCurrentUsage($row["current_use"]);
            $landPrice->setStructure($row["build_structure"]);
            $landPrice->setUsage($row["usage_id"]);
            $surveyResultOfCity[] = $landPrice;

        }
        $surveyItemsOfCity->close();
        //station
        $postStationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from post_price where price1 <> 0 and city = '" . $city . "' and address like '" . $prefecture . "%' group by near_station order by price0";
        $postQueryResult = $this->db->query($postStationQuery . " desc");
        //
        $postStations = array();
        //
        while ($row = mysqli_fetch_assoc($postQueryResult)) {
            $landPrice = new LandPrice();
            $landPrice->setStation($row["near_station"]);
            $landPrice->setPrice($row["price_jp"]);
            $landPrice->setPriceOfTubo($row["price_tubo"]);
            $landPrice->setChangeRate($row["rate"]);
            $postStations[] = $landPrice;
        }
        $postQueryResult->close();

        $surveyStationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from survey_price where price1 <> 0 and city = '" . $city . "' and address like '" . $prefecture . "%' group by near_station order by price0";
        $surveyQueryResult = $this->db->query($surveyStationQuery . " desc");
        //
        $surveyStations = array();
        //
        while ($row = mysqli_fetch_assoc($surveyQueryResult)) {
            $landPrice = new LandPrice();
            $landPrice->setStation($row["near_station"]);
            $landPrice->setPrice($row["price_jp"]);
            $landPrice->setPriceOfTubo($row["price_tubo"]);
            $landPrice->setChangeRate($row["rate"]);
            $surveyStations[] = $landPrice;
        }
        $surveyQueryResult->close();
        //
        $this->view->render($response, 'landprice/city.twig',
            [
                "posted_title"=> $prefecture . "/" . $city,
                "prefecture" => $prefecture,
                "city" => $city,
                "postedPrices" => $postResultOfCity,
                "surveyPrices" => $surveyResultOfCity,
                "postStations" => $postStations,
                "surveyStations" => $surveyStations,
                "targetYear"=>$this->getTargetYear()
            ]
        );
    }

}