<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/08/20
 * Time: 14:07
 */

namespace Service;

use Slim\Router;
use Mysqli;

class PostedPriceService
{
    private $router;
    private $db;

    public function __construct(Router $router, Mysqli $db)
    {
        $this->db = $db;
        $this->router = $router;
    }

    public function historyPriceOf ($request, $response, $params) {
       // $cities = $this->db->query("select distinct city from posted_price where address like '" . $params['prefecture'] . "%'");
        $prefecture = $request->getAttribute('prefecture');
        $itemID = $request->getAttribute('id');
        $queryStr = "";

        if (is_null($prefecture) && is_null($itemID)) { //
            $queryStr = "select year, ROUND(AVG(price)) as avg_price from posted_his where price <> 0 group by year order by year";
        } else if (!is_null($prefecture) && is_null($itemID)) { //
            $queryStr = "select ROUND(AVG(price)) as avg_price, year from posted_his where price <> 0 and prefecture = '" . $prefecture . "' group by year order by year";
        } else {//

        }
        $allHistory = $this->db->query($queryStr);
        $labels = array();
        $prices = array();
        while ($row = mysqli_fetch_assoc($allHistory)) {
            $labels[] = $row["year"];
            $prices[] = $row["avg_price"];
        }
        $allHistory->close();
        //
        $result = ["labels"=>$labels, "prices"=>$prices];
        //
        $res = $response->withJson($result)
            ->withHeader('Content-type', 'application/json');
        return $res;
    }
    //Price information for station.
    public function stationPrice ($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $stationName = $request->getAttribute('stationName');
        //
        $queryStrDesc = ""; //降順
        $queryStrAsc = ""; //昇順
        //
        if (is_null($prefecture)) {
            $queryStrDesc = "select near_station, avg(price0) as price from post_price group by near_station order by price desc";
            $queryStrAsc = "select near_station, avg(price0) as price from post_price group by near_station order by price";
        } else if (!is_null($prefecture) && is_null($stationName)) {
            $queryStrDesc = "select near_station, avg(price0) as price from post_price where address like '" . $prefecture . "%' group by near_station order by price desc";
            $queryStrAsc = "select near_station, avg(price0) as price from post_price where address like '" . $prefecture . "%' group by near_station order by price";
        } else {

        }
        $stationPricesDesc = $this->db->query($queryStrDesc);
        $stationsDesc = array();
        $avgPricesDesc = array();
        while ($row = mysqli_fetch_assoc($stationPricesDesc)) {
            $stationsDesc[] = $row["near_station"];
            $avgPricesDesc[] = $row["price"];
        }
        //
        $stationPricesAsc = $this->db->query($queryStrAsc);
        $stationsAsc = array();
        $avgPricesAsc = array();
        while ($row = mysqli_fetch_assoc($stationPricesAsc)) {
            $stationsAsc[] = $row["near_station"];
            $avgPricesAsc[] = $row["price"];
        }

        $result = ["stationDesc"=> $stationsDesc, "priceDesc" =>$avgPricesDesc, "stationAsc" =>$stationsAsc, "priceAsc"=>$avgPricesAsc];
        //
        $res = $response->withJson($result)
            ->withHeader('Content-type', 'application/json');
        return $res;
    }

}