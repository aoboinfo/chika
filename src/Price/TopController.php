<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/08/31
 * Time: 17:06
 */

namespace Price;

class TopController extends SearchController
{
    public function showTopPage ($request, $response, $params) {

        $this->logger->info("LandPrice '/' site home");

        $stationsDesc = $this->getTopStationListForTarget(SearchController::POST_VIEW, NULL, NULL);
        $stationAsc = $this->getLowStationListForTarget(SearchController::POST_VIEW, NULL);
        //
        $surveyStationsDesc = $this->getTopStationListForTarget(SearchController::SURVEY_VIEW, NULL,NULL);
        $surveyStationsAsc = $this->getLowStationListForTarget(SearchController::SURVEY_VIEW, NULL);
        //
        //posted average price/year

        $averagePrices = $this->session->get(SearchController::ALL_COUNTRY . "avgPrices", NULL);
        if ($averagePrices == NULL) {
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
            $this->session->set(SearchController::ALL_COUNTRY . "avgPrices", $averagePrices);
        }

        /*Ranking area*/
        $postedTop10Pref = $this->session->get(SearchController::ALL_COUNTRY . "postTopPref", NULL);

        if ($postedTop10Pref == NULL) {
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
            $this->session->set(SearchController::ALL_COUNTRY . "postTopPref", $postedTop10Pref);

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
            $this->session->set(SearchController::ALL_COUNTRY . "postLowPref", $postedTop10Pref);
        }
        $postedLow10Pref = $this->session->get(SearchController::ALL_COUNTRY . "postLowPref", NULL);

        //
        $surveyTop10Pref = $this->session->get(SearchController::ALL_COUNTRY . "surveyTopPref", NULL);

        if ($surveyTop10Pref == NULL) {
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

            $this->session->set(SearchController::ALL_COUNTRY . "surveyTopPref", $surveyTop10Pref);
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
            $this->session->set(SearchController::ALL_COUNTRY . "surveyLowPref", $surveyLow10Pref);
        }

        $surveyLow10Pref = $this->session->get(SearchController::ALL_COUNTRY . "surveyLowPref", NULL);

        //
        $topPostPrice = $this->session->get(SearchController::ALL_COUNTRY . "topPostPrices" , NULL);
        if ($topPostPrice == NULL) {
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
            $this->session->set(SearchController::ALL_COUNTRY . "topPostPrices", $topPostPrice);
        }

        //
        $topSurveyPrice = $this->session->get(SearchController::ALL_COUNTRY . "topSurveyPrices" , NULL);
        if ($topSurveyPrice == NULL) {
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
            $this->session->set(SearchController::ALL_COUNTRY . "topSurveyPrices" , $topSurveyPrice);
        }


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