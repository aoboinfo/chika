<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:51
 */

namespace Price;

use phpDocumentor\Reflection\Types\Null_;

class AddressSearchController extends SearchController
{
    public function detailForAddress($request, $response, $params) {
        $address = $request->getAttribute('address');
        $allGetVars = $request->getQueryParams();
        $priceType = $allGetVars['type'];
        $rate = $allGetVars['rate'];
        if ($rate == NULL) {
            $rate = "変動なし";
        }
        //for getting menus
        $halfSpaceAddress = mb_convert_kana($address, 's'); //change to half space at first, if has.
        $items = preg_split('/ /', $halfSpaceAddress);

        $prefecture = $items[0];

        $commQuery = "SELECT id,
                      lat,
                      lng,
                      seqNo,
                      usage_id,
                      city,
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
                      city_usage,
                      city_fire,
                      city_plan,
                      city_forest,
                      city_park,
                      build_coverage,
                      floor_area_ratio FROM ";
        $detailQuery = "";
        $historyQuery = "select year, price from ";
        $aroundCommonQuery = "select price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station from ";
        $aroundQuery = "";
        if ($priceType == 0) {
            $detailQuery = $detailQuery . $commQuery . SearchController::POST_TABLE . " WHERE address = '" . $address . "'";
            $aroundQuery = $aroundCommonQuery . SearchController::POST_VIEW . " WHERE address LIKE '" . $prefecture  . "%' AND city = '";
            $historyQuery = $historyQuery . "posted_his ";

        } else {
            $detailQuery = $detailQuery . $commQuery . SearchController::SURVEY_TABLE . " WHERE address = '" . $address . "'";
            $aroundQuery = $aroundCommonQuery . SearchController::SURVEY_VIEW . " WHERE address LIKE '" . $prefecture . "%' AND city = '";
            $historyQuery = $historyQuery . "survey_his ";
        }
        $itemDetail = $this->db->query($detailQuery);
        //
        $detail = new LandPrice();
        $detail->setType($priceType);
        $detail->setPrice($allGetVars['price']);
        $detail->setChangeRate($rate);

        while ($row = mysqli_fetch_assoc($itemDetail)) {
            $detail->setId($row["id"]);
            $detail->setLat($row["lat"]);
            $detail->setLng($row["lng"]);
            $detail->setSeqNo($row["seqNo"]);
            $detail->setUsage($row["usage_id"]);
            $detail->setCity($row["city"]);
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
            $detail->setRoadFrontStatus($row["road_front_status"]);
            $detail->setRoadPavement($row["road_pavement"]);
            $detail->setSideRoad($row["side_road"]);
            $detail->setSideRroadDirection($row["side_road_direction"]);
            $detail->setAboutTransportArea($row["about_transport_area"]);
            $detail->setAboutNear($row["about_near"]);
            $detail->setStation($row["near_station"]);
            $detail->setDistanceFromStation($row["distance_station"]);
            $detail->setCityPlan($row["city_plan"]);
            $detail->setUsagePlan($row["city_usage"]);
            $detail->setFirePlan($row["city_fire"]);
            $detail->setForestPlan($row["city_forest"]);
            $detail->setParkPlan($row["city_park"]);
            $detail->setBuildCoverage($row["build_coverage"]);
            $detail->setFloorAreaRatio($row["floor_area_ratio"]);
        }
        $itemDetail->close();
        //
        $priceInt = str_replace(",", "", mb_substr($detail->getPrice(), 1));
        $aroundRecordsUp = array();
        $upDataQuery = $aroundQuery . $detail->getCity() . "' and id <> '" . $detail->getId() . "' and price0 > " . $priceInt . " order by price0 limit 10";
        $this->logger->addInfo($upDataQuery);
        $itemsAroundUp = $this->db->query($upDataQuery);
        while ($row = mysqli_fetch_assoc($itemsAroundUp)) {
            $landPrice = new LandPrice();
            $landPrice->setPrice("¥" . number_format($row["price0"]));
            $landPrice->setChangeRate($row["rate"]);
            $landPrice->setAddress($row["address"]);
            $landPrice->setStation($row["near_station"]);
            $landPrice->setDistanceFromStation($row["distance_station"]);
            $aroundRecordsUp[] = $landPrice;
        }
        $itemsAroundUp->close();
        //
        $aroundRecordsDown = array();
        $itemsAroundDown = $this->db->query($aroundQuery . $detail->getCity() . "' and id <> '" . $detail->getId() . "' and price0 < " . $priceInt . " order by price0 limit 10");
        while ($row = mysqli_fetch_assoc($itemsAroundDown)) {
            $landPrice = new LandPrice();
            $landPrice->setPrice("¥" . number_format($row["price0"]));
            $landPrice->setChangeRate($row["rate"]);
            $landPrice->setAddress($row["address"]);
            $landPrice->setStation($row["near_station"]);
            $landPrice->setDistanceFromStation($row["distance_station"]);
            $aroundRecordsDown[] = $landPrice;
        }
        $itemsAroundDown->close();
        //history data
        $historyQuery = $this->db->query($historyQuery . "WHERE id = '" . $detail->getId() . "'");
        $historyYear = array();
        $historyPrice = array();
        while ($row = mysqli_fetch_assoc($historyQuery)) {
            $historyYear[] = $row["year"];
            $historyPrice[] = $row["price"];
        }

        return $this->view->render($response, 'detail.twig',
            [
                "areas" => [$prefecture, $prefecture],
                "leftMenus" => [$this->getCityList(SearchController::POST_TABLE, $prefecture), $this->getCityList(SearchController::SURVEY_TABLE, $prefecture)],
                "listing" => $detail,
                "options" => $this->getLeftOptions(), //the menu list below is place to be selected
                "prefectureLabel" => $prefecture,
                "pageLabel" => $address,
                "aroundUp" => $aroundRecordsUp,
                "aroundDown" => $aroundRecordsDown,
                "historyYear" => json_encode($historyYear),
                "historyPrice" => json_encode($historyPrice),
                "targetYear"=>$this->getTargetYear()
            ]
        );

    }
}