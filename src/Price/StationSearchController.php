<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:49
 */

namespace Price;


class StationSearchController extends SearchController
{
    private function stationResult($request, $response, $params, $priceType) {

        $prefecture = $request->getAttribute('prefecture');
        $city = $request->getAttribute('city');
        $station = $request->getAttribute('station');

        $target = SearchController::POST_VIEW;
        $priceName = SearchController::POST_KANJI;
        if ($priceType == SearchController::SURVEY_TYPE) {
            $target = SearchController::SURVEY_VIEW;
            $priceName = SearchController::SURVEY_KANJI;
        }
        $stationsDesc = array();
        $stationAsc = array();
        $usages = array();
        $cityPlans = array();

        //
        if (is_null($prefecture) && is_null($city)) {
            $stationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from " . $target ." where price1 <> 0 group by near_station order by price0";
            //The top 10 stations
            $stationTop10 = $this->db->query($stationQuery . " desc limit 10");
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
            $stationLow10 = $this->db->query($stationQuery .  " limit 10");
            while ($row = mysqli_fetch_assoc($stationLow10)) {
                $landPrice = new LandPrice();
                $landPrice->setStation($row["near_station"]);
                $landPrice->setPrice($row["price_jp"]);
                $landPrice->setPriceOfTubo($row["price_tubo"]);
                $landPrice->setChangeRate($row["rate"]);
                $stationAsc[] = $landPrice;
            }
            $stationLow10->close();
            //the listings for the station
            $queryOfStation = $this->db->query("select price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, city_plan from " . $target . " where near_station = '" . $station . "' order by price0 desc");
            $resultOfStation = array();

            while ($row = mysqli_fetch_assoc($queryOfStation)) {
                $landPrice = new LandPrice();
                $landPrice->setStation($station);
                $landPrice->setPrice("¥" . number_format($row["price0"]));
                $landPrice->setChangeRate($row["rate"]);
                $landPrice->setAddress($row["address"]);
                $landPrice->setStation($row["near_station"]);
                $landPrice->setDistanceFromStation($row["distance_station"]);
                $landPrice->setCurrentUsage($row["current_use"]);
                $landPrice->setStructure($row["build_structure"]);
                $landPrice->setCityPlan($row["city_plan"]);
                $resultOfStation[] = $landPrice;
            }
            $queryOfStation->close();

            return $this->view->render($response, 'searchResult.twig',
                [
                    "areas" => $this->getAreas(),
                    "leftMenus" => $this->getPrefectures(),
                    "stationTop" => $stationsDesc,
                    "stationLow" => $stationAsc,
                    "listings" => $resultOfStation,
                    "optionMenus" => [$usages,$cityPlans],
                    "options" => $this->getLeftOptions(), //the menu list below place to be selected
                    "prefecture" => $this->getStationLabel(),
                    "station" => $station,
                    "priceType" => $priceName
                ]
            );

        } else if (!is_null($prefecture) && is_null($city)) {
            //
            $this->setStationLabel($prefecture);
            //
            $postStationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from " . $target . " where price1 <> 0 and address like '" . $prefecture . "%' group by near_station order by price0";
            $stationTop10 = $this->db->query($postStationQuery . " desc limit 10");
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
            //
            $cities = $this->db->query("select distinct city from " . $target . " where address like '" . $prefecture . "%' order by city");
            $result = array();
            while ($row = mysqli_fetch_assoc($cities)) {
                $result[] = $row["city"];
            }
            $cities->close();
            //
            $queryOfStation = $this->db->query("select price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, city_plan from " . $target . " where near_station = '" . $station . "' and address like '" . $prefecture . "%' order by price0 desc");
            $resultOfStation = array();

            while ($row = mysqli_fetch_assoc($queryOfStation)) {
                $landPrice = new LandPrice();
                $landPrice->setStation($station);
                $landPrice->setPrice("¥" . number_format($row["price0"]));
                $landPrice->setChangeRate($row["rate"]);
                $landPrice->setAddress($row["address"]);
                $landPrice->setStation($row["near_station"]);
                $landPrice->setDistanceFromStation($row["distance_station"]);
                $landPrice->setCurrentUsage($row["current_use"]);
                $landPrice->setStructure($row["build_structure"]);
                $landPrice->setCityPlan($row["city_plan"]);
                $resultOfStation[] = $landPrice;
            }
            $queryOfStation->close();
            //
            return $this->view->render($response, 'searchResult.twig',
                [
                    "areas" => [$prefecture],
                    "leftMenus" => [$result],
                    "stationTop" => $stationsDesc,
                    "stationLow" => $stationAsc,
                    "listings" => $resultOfStation,
                    "optionMenus" => [$usages,$cityPlans],
                    "options" => $this->getLeftOptions(), //the menu list below is place to be selected
                    "stationLabel" => $this->getStationLabel(),
                    "station" => $station,
                    "priceType" => $priceName
                ]
            );

        } else {
            $postStationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from " . $target . " where price1 <> 0 and city = '" . $city . "' and address like '" . $prefecture . "%' group by near_station order by price0";
            $postQueryResult = $this->db->query($postStationQuery . " desc");
            while ($row = mysqli_fetch_assoc($postQueryResult)) {
                $landPrice = new LandPrice();
                $landPrice->setStation($row["near_station"]);
                $landPrice->setPrice($row["price_jp"]);
                $landPrice->setPriceOfTubo($row["price_tubo"]);
                $landPrice->setChangeRate($row["rate"]);
                $stationsDesc[] = $landPrice;
            }
            $postQueryResult->close();

        }
        return $response;
    }
    public function findPostListForStation($request, $response, $params) {
        return $this->stationResult($request, $response, $params, SearchController::POST_TYPE);
    }
    public function findSurveyListForStation($request, $response, $params) {
        return $this->stationResult($request, $response, $params, SearchController::SURVEY_TYPE);
    }
}