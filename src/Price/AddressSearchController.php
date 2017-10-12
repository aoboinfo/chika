<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:51
 */

namespace Price;

class AddressSearchController extends SearchController
{
    public function detailForAddress($request, $response, $params) {
        $address = $request->getAttribute('address');
        $allGetVars = $request->getQueryParams();
        $priceType = $allGetVars['type'];
        //for getting menus
        $halfSpaceAddress = mb_convert_kana($address, 's');
        $items = preg_split('/ /', $halfSpaceAddress);
        $prefecture = $items[0];

        $citiesTarget = SearchController::POST_TABLE;
        $stationTarget = SearchController::POST_VIEW;
        $commQuery = "SELECT id,
                      lat,
                      lng,
                      acreage,
                      current_use,
                      use_desc,
                      build_structure,
                      water,
                      gas,
                      sewage,
                      config,
                      front_ratio,
                      depth_ratio,
                      num_of_floors,
                      num_of_basefloors,
                      front_roads,
                      road_direction,
                      road_width,
                      road_front_status,
                      road_pavement,
                      side_road,
                      side_road_direction,
                      about_transport_area,
                      about_near,
                      near_station,
                      distance_station,
                      city_plan,
                      build_coverage,
                      floor_area_ratio FROM ";
        $detailQuery = "";
        if ($priceType == 0) {
            $detailQuery = $detailQuery . $commQuery . $citiesTarget . " WHERE address = '" . $address . "'";
            $usages = $this->optionsList($citiesTarget, $prefecture, NULL, "0");
            $cityPlans = $this->optionsList($citiesTarget, $prefecture, NULL, "1");
            $this->setLeftOptions(["公示：利用現況","公示：用途地域"]);
        } else {
            $citiesTarget = SearchController::SURVEY_TABLE;
            $stationTarget = SearchController::SURVEY_VIEW;
            $detailQuery = $detailQuery . $commQuery . $citiesTarget . " WHERE address = '" . $address . "'";
            $usages = $this->optionsList($citiesTarget, $prefecture, NUll, "0"); //$city = NULL
            $cityPlans = $this->optionsList($citiesTarget, $prefecture, NUll, "1");
            $this->setLeftOptions(["調査：利用現況","調査：用途地域"]);
        }
        $itemDetail = $this->db->query($detailQuery);
        //
        $detail = new LandPrice();

        while ($row = mysqli_fetch_assoc($itemDetail)) {
            $detail->setId($row["id"]);
            $detail->setLat($row["lat"]);
            $detail->setLng($row["lng"]);
            $detail->setAcreage($row["acreage"]);
            $detail->setCurrentUsage($row["current_use"]);
            $detail->setUseDesc($row["use_desc"]);
            $detail->setStructure($row["build_structure"]);
            $detail->setWater($row["water"]);
            $detail->setGas($row["gas"]);
            $detail->setSewage(["sewage"]);
            $detail->setConfig($row["config"]);
            $detail->setFrontRatio($row["front_ratio"]);
            $detail->setDepthRatio($row["depth_ratio"]);
            $detail->setNumOfFloors($row["num_of_floors"]);
            $detail->setNumOfBasefloors($row["num_of_basefloors"]);
            $detail->setFrontRoads($row["front_roads"]);
            $detail->setRoadDirection($row["road_direction"]);
            $detail->setRoadWidth($row["road_width"]);
            $detail->setRoadRrontStatus($row["road_front_status"]);
            $detail->setRoadPavement($row["road_pavement"]);
            $detail->setSideRoad($row["side_road"]);
            $detail->setSideRroadDirection($row["side_road_direction"]);
            $detail->setAboutTransportArea($row["about_transport_area"]);
            $detail->setAboutNear($row["about_near"]);
            $detail->setStation($row["near_station"]);
            $detail->setDistanceFromStation($row["distance_station"]);
            $detail->setCityPlan($row["city_plan"]);
            $detail->setBuildCoverage($row["build_coverage"]);
            $detail->setFloorAreaRatio($row["floor_area_ratio"]);
        }
        $itemDetail->close();

        if ($priceType == 0) {

        } else {

        }

        return $this->view->render($response, 'detail.twig',
            [
                "areas" => [$prefecture, $prefecture],
                "leftMenus" => [$this->getCityList(SearchController::POST_TABLE, $prefecture), $this->getCityList(SearchController::SURVEY_TABLE, $prefecture)],
                "stationTop" => $this->getTopStationListForTarget($stationTarget, $prefecture, NULL),
                "stationLow" => $this->getLowStationListForTarget($stationTarget, $priceType),
                //"listings" => $resultOfOption,
                //"optionMenus" => [$usages,$cityPlans],
                "options" => $this->getLeftOptions(), //the menu list below is place to be selected
                "prefectureLabel" => $prefecture,
                "pageLabel" => $address,
                "priceType" => $priceType
            ]
        );

    }
}