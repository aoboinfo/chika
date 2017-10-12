<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:51
 */

namespace Price;


use Psr\Http\Message\ServerRequestInterface;

class AddressSearchController extends SearchController
{
    public function detailForAddress($request, $response, $params) {
        $address = $request->getAttribute('address');
        $allGetVars = $request->getQueryParams();
        $priceType = $allGetVars['type'];
        $commQuery = "SELECT lat,
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
            $detailQuery = $detailQuery . $commQuery . SearchController::POST_TABLE . " WHERE address = '" . $address . "'";
        } else {
            $detailQuery = $detailQuery . $commQuery . SearchController::SURVEY_TABLE . " WHERE address = '" . $address . "'";
        }
        $itemDetail = $this->db->query($detailQuery);
        //
        $detail = new LandPrice();
        while ($row = mysqli_fetch_assoc($itemDetail)) {
            $detail->setLat($row["lat"]);
            $detail->setLng($row["lng"]);
            $detail->acreage = $row["acreage"];
            $detail->usage = $row["current_use"];
            $detail->desc = $row["use_desc"];
            $detail->structure = $row["build_structure"];
            $detail->water = $row["water"];
            $detail->gas = $row["gas"];
            $detail-> = $row["sewage"];
            $detail->lat = $row["config"];
            $detail->lat = $row["front_ratio"];
            $detail->lat = $row["depth_ratio"];
            $detail->lat = $row["num_of_floors"];
            $detail->lat = $row["num_of_basefloors"];
            $detail->lat = $row["front_roads"];
            $detail->lat = $row["road_direction"];
            $detail->lat = $row["road_width"];
            $detail->lat = $row["road_front_status"];
            $detail->lat = $row["road_pavement"];
            $detail->lat = $row["side_road"];
            $detail->lat = $row["side_road_direction"];
            $detail->lat = $row["about_transport_area"];
            $detail->lat = $row["about_near"];
            $detail->lat = $row["near_station"];
            $detail->lat = $row["distance_station"];
            $detail->lat = $row["city_plan"];
            $detail->lat = $row["build_coverage"];
            $detail->lat = $row["floor_area_ratio"];
        }
        $itemDetail->close();
    }
}