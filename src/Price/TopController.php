<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/08/31
 * Time: 17:06
 */

namespace Price;


class TopController  extends SearchController
{
    public function showTopPage ($request, $response, $params) {

        $this->logger->info("LandPrice '/' site home");
        //
        //Render index view
        $postStationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from post_price where price1 <> 0 group by near_station order by price0";
        //The top 10 stations
        $stationTop10 = $this->db->query($postStationQuery . " desc limit 10");
        $stationsDesc = array();
        //
        while ($row = mysqli_fetch_assoc($stationTop10)) {
            $landPrice = new LandPrice();
            $landPrice->setStation($row["near_station"]);
            $landPrice->setPrice($row["price_jp"]);
            $landPrice->setPriceOfTubo($row["price_tubo"]);
            $landPrice->setChangeRate($row["rate"]);
            $stationsDesc[] = $landPrice;
        }
        $stationTop10->close();
        //
        $stationLow10 = $this->db->query($postStationQuery .  " limit 10");
        $stationAsc = array();
        //
        while ($row = mysqli_fetch_assoc($stationLow10)) {
            $landPrice = new LandPrice();
            $landPrice->setStation($row["near_station"]);
            $landPrice->setPrice($row["price_jp"]);
            $landPrice->setPriceOfTubo($row["price_tubo"]);
            $landPrice->setChangeRate($row["rate"]);
            $stationAsc[] = $landPrice;
        }
        $stationLow10->close();
        //survey station
        $surveyStationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from survey_price where price1 <> 0 group by near_station order by price0";
        //The top 10 stations
        $surveyStationTop10 = $this->db->query($surveyStationQuery . " desc limit 10");
        $surveyStationsDesc = array();
        while ($row = mysqli_fetch_assoc($surveyStationTop10)) {
            $landPrice = new LandPrice();
            $landPrice->setStation($row["near_station"]);
            $landPrice->setPrice($row["price_jp"]);
            $landPrice->setPriceOfTubo($row["price_tubo"]);
            $landPrice->setChangeRate($row["rate"]);
            $surveyStationsDesc[] = $landPrice;
        }
        $surveyStationTop10->close();

        $surveyStationLow10 = $this->db->query($surveyStationQuery . " limit 10");
        $surveyStationsAsc = array();
        //
        while ($row = mysqli_fetch_assoc($surveyStationLow10)) {
            $landPrice = new LandPrice();
            $landPrice->setStation($row["near_station"]);
            $landPrice->setPrice($row["price_jp"]);
            $landPrice->setPriceOfTubo($row["price_tubo"]);
            $landPrice->setChangeRate($row["rate"]);
            $surveyStationsAsc[] = $landPrice;
        }
        $surveyStationLow10->close();
        //
        //posted average price/year
        $posted_avg = $this->db->query("select year, FORMAT(ROUND(AVG(price)),0) as avg_price from posted_his where price <> 0 group by year order by year");
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
        //
        $survey_avg = $this->db->query("select FORMAT(ROUND(AVG(price)),0) as avg_price from survey_his where price <> 0 group by year order by year");
        $index = 0;
        while ($row = mysqli_fetch_assoc($survey_avg)) {
            $averagePrice = $averagePrices[$index];
            $averagePrice->setSurveyPrice($row["avg_price"]);
            $index++;
        }
        $survey_avg->close();
        /*Ranking area*/
        $postedQuery10 = "select prefecture, round(avg(price)) as pre_price, round(avg(price) * 3.305785) as tubo_price  from posted_his where year = 2017 group by prefecture order by pre_price";
        //top 10 prefecture
        $top10PrefectureQry = $this->db->query($postedQuery10 . " desc limit 10");
        $postedTop10Pref = array();
        while ($row = mysqli_fetch_assoc($top10PrefectureQry)) {
            $landPrice = new LandPrice();
            $landPrice->setPrice("¥" . number_format($row["pre_price"]));
            $landPrice->setAddress($row["prefecture"]);
            $landPrice->setPriceOfTubo("¥" . number_format($row["tubo_price"]));
            $postedTop10Pref[] = $landPrice;
        }
        $top10PrefectureQry->close();
        //low 10 prefecture
        $low10PrefecturyQry = $this->db->query($postedQuery10 . " limit 10");
        $postedLow10Pref = array();
        while ($row = mysqli_fetch_assoc($low10PrefecturyQry)) {
            $landPrice = new LandPrice();
            $landPrice->setPrice("¥" . number_format($row["pre_price"]));
            $landPrice->setAddress($row["prefecture"]);
            $landPrice->setPriceOfTubo("¥" . number_format($row["tubo_price"]));
            $postedLow10Pref[] = $landPrice;
        }
        $low10PrefecturyQry->close();
        //
        $survey10Query = "select prefecture, round(avg(price)) as pre_price, round(avg(price) * 3.305785) as tubo_price from survey_his where year = 2016 group by prefecture order by pre_price";
        //
        $top10SurveyPrefectureQry = $this->db->query($survey10Query . " desc limit 10");
        $surveyTop10Pref = array();
        while ($row = mysqli_fetch_assoc($top10SurveyPrefectureQry)) {
            $landPrice = new LandPrice();
            $landPrice->setPrice("¥" . number_format($row["pre_price"]));
            $landPrice->setAddress($row["prefecture"]);
            $landPrice->setPriceOfTubo("¥" . number_format($row["tubo_price"]));
            $surveyTop10Pref[] = $landPrice;
        }
        $top10SurveyPrefectureQry->close();
        //
        $low10SurveyPrefectureQry = $this->db->query($survey10Query . " limit 10");
        $surveyLow10Pref = array();
        while ($row = mysqli_fetch_assoc($low10SurveyPrefectureQry)) {
            $landPrice = new LandPrice();
            $landPrice->setPrice("¥" . number_format($row["pre_price"]));
            $landPrice->setAddress($row["prefecture"]);
            $landPrice->setPriceOfTubo("¥" . number_format($row["tubo_price"]));
            $surveyLow10Pref[] = $landPrice;
        }
        $low10SurveyPrefectureQry->close();
        //
        $postedPriceOfAll = $this->db->query("select id, price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, city_plan from post_price order by price0 desc limit 6");
        $topPostPrice = array();
        while ($row = mysqli_fetch_assoc($postedPriceOfAll)) {
            $landPrice = new LandPrice();
            $landPrice->setId($row["id"]);
            $landPrice->setPrice("¥" . number_format($row["price0"]));
            $landPrice->setChangeRate($row["rate"]);
            $landPrice->setAddress($row["address"]);
            $landPrice->setStation($row["near_station"]);
            $landPrice->setDistanceFromStation($row["distance_station"]);
            $landPrice->setCurrentUsage($row["current_use"]);
            $landPrice->setStructure($row["build_structure"]);
            $landPrice->setCityPlan($row["city_plan"]);
            $topPostPrice[] = $landPrice;
        }
        $postedPriceOfAll->close();
        //
        $surveyPriceOfAll = $this->db->query("select id, price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, city_plan from survey_price order by price0 desc limit 6");
        $topSurveyPrice = array();
        while ($row = mysqli_fetch_assoc($surveyPriceOfAll)) {
            $landPrice = new LandPrice();
            $landPrice->setId($row["id"]);
            $landPrice->setPrice("¥" . number_format($row["price0"]));
            $landPrice->setChangeRate($row["rate"]);
            $landPrice->setAddress($row["address"]);
            $landPrice->setStation($row["near_station"]);
            $landPrice->setDistanceFromStation($row["distance_station"]);
            $landPrice->setCurrentUsage($row["current_use"]);
            $landPrice->setStructure($row["build_structure"]);
            $landPrice->setCityPlan($row["city_plan"]);
            $topSurveyPrice[] = $landPrice;
        }
        $surveyPriceOfAll->close();

        //render the top page
        return $this->view->render($response, 'top.twig',
            [
                "areas" => $this->getAreas(),
                "leftMenus" => $this->getPrefectures(),
                "stationTop" => $stationsDesc,
                "stationLow" => $stationAsc,
                "surveyStationTop" => $surveyStationsDesc,
                "surveyStationLow" => $surveyStationsAsc,
                "avgPrices" => $averagePrices,
                "postTopPref" => $postedTop10Pref,
                "postLowPref" => $postedLow10Pref,
                "surveyTopPref" => $surveyTop10Pref,
                "surveyLowPref" => $surveyLow10Pref,
                "topPostPrices" => $topPostPrice,
                "topSurveyPrices" => $topSurveyPrice
            ]
        );
    }
}