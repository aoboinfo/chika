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
    const STATION_POSTFIX = "駅";
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

        $usages = array();
        $cityPlans = array();

        //
        if (is_null($prefecture) && is_null($city)) {

            $stationsDesc = $this->getTopStationListForTarget($target, $prefecture, $city);
            //
            $stationAsc = $this->getLowStationListForTarget($target, $prefecture);
            //the listings for the station
            $queryOfStation = $this->db->query("select price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, distance_station, current_use, build_structure, city_plan from " . $target . " where near_station = '" . $station . "' order by price0 desc");
            $resultOfStation = array();

            while ($row = mysqli_fetch_assoc($queryOfStation)) {
                $landPrice = new LandPrice();
                $landPrice->setStation($station);
                $landPrice->setPrice("¥" . number_format($row["price0"]));
                $landPrice->setChangeRate($row["rate"]);
                $landPrice->setAddress($row["address"]);
                $landPrice->setDistanceFromStation($row["distance_station"]);
                $landPrice->setCurrentUsage($row["current_use"]);
                $landPrice->setStructure($row["build_structure"]);
                $landPrice->setCityPlan($row["city_plan"]);
                $landPrice->setType($priceName);
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
                    "prefectureLabel" => $this->getStationLabel(),
                    "pageLabel" => $station . StationSearchController::STATION_POSTFIX,
                    "priceType" => $priceName,
                    "linkType" => "0"
                ]
            );

        } else if (!is_null($prefecture) && is_null($city)) {
            //
            $this->setStationLabel($prefecture);
            //
            $stationsDesc = $this->getTopStationListForTarget($target, $prefecture, $city);
            //
            $stationAsc = $this->getLowStationListForTarget($target, $prefecture);
            //
            $queryOfStation = $this->db->query("select price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, distance_station, current_use, build_structure, city_plan from " . $target . " where near_station = '" . $station . "' and address like '" . $prefecture . "%' order by price0 desc");
            $resultOfStation = array();

            while ($row = mysqli_fetch_assoc($queryOfStation)) {
                $landPrice = new LandPrice();
                $landPrice->setStation($station);
                $landPrice->setPrice("¥" . number_format($row["price0"]));
                $landPrice->setChangeRate($row["rate"]);
                $landPrice->setAddress($row["address"]);
                $landPrice->setDistanceFromStation($row["distance_station"]);
                $landPrice->setCurrentUsage($row["current_use"]);
                $landPrice->setStructure($row["build_structure"]);
                $landPrice->setCityPlan($row["city_plan"]);
                $landPrice->setType($priceName);
                $resultOfStation[] = $landPrice;
            }
            $queryOfStation->close();
            //
            return $this->view->render($response, 'searchResult.twig',
                [
                    "areas" => [$prefecture],
                    "leftMenus" => [$this->getCityList($target, $prefecture)],
                    "stationTop" => $stationsDesc,
                    "stationLow" => $stationAsc,
                    "listings" => $resultOfStation,
                    "optionMenus" => [$usages,$cityPlans],
                    "options" => $this->getLeftOptions(), //the menu list below is place to be selected
                    "prefectureLabel" => $this->getStationLabel(),
                    "pageLabel" => $station . StationSearchController::STATION_POSTFIX,
                    "priceType" => $priceName,
                    "linkType" => "1"
                ]
            );

        } else {
            $this->setStationLabel($prefecture);
            $stationsDesc = $this->getTopStationListForTarget($target, $prefecture, $city);
            //
            $listOfStation = $this->db->query("select price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, distance_station, current_use, build_structure, city_plan from " . $target . " where near_station = '" . $station . "' and city = '" . $city . "' and address like '" . $prefecture . "%' order by price0 desc");

            $resultOfStation = array();

            while ($row = mysqli_fetch_assoc($listOfStation)) {
                $landPrice = new LandPrice();
                $landPrice->setStation($station);
                $landPrice->setPrice("¥" . number_format($row["price0"]));
                $landPrice->setChangeRate($row["rate"]);
                $landPrice->setAddress($row["address"]);
                $landPrice->setDistanceFromStation($row["distance_station"]);
                $landPrice->setCurrentUsage($row["current_use"]);
                $landPrice->setStructure($row["build_structure"]);
                $landPrice->setCityPlan($row["city_plan"]);
                $landPrice->setType($priceName);
                $resultOfStation[] = $landPrice;
            }
            $listOfStation->close();
            //
            return $this->view->render($response, 'searchResult.twig',
                [
                    "areas" => [$prefecture],
                    "leftMenus" => [$this->getCityList($target, $prefecture)],
                    "stationTop" => $stationsDesc,
                    "listings" => $resultOfStation,
                    "optionMenus" => [$usages,$cityPlans],
                    "options" => $this->getLeftOptions(), //the menu list below is place to be selected
                    "prefectureLabel" => $this->getStationLabel(),
                    "pageLabel" => $station . StationSearchController::STATION_POSTFIX,
                    "priceType" => $priceName,
                    "city" => $city,
                    "linkType" => "2"
                ]
            );

        }
    }
    public function findPostListForStation($request, $response, $params) {
        return $this->stationResult($request, $response, $params, SearchController::POST_TYPE);
    }
    public function findSurveyListForStation($request, $response, $params) {
        return $this->stationResult($request, $response, $params, SearchController::SURVEY_TYPE);
    }
}