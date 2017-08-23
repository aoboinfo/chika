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

        if (is_null($prefecture) && is_null($itemID)) {//
            $allHistory = $this->db->query("select year, ROUND(AVG(price)) as avg_price from posted_his where price <> 0 group by year order by year");
            $labels = array();
            $prices = array();
            while ($row = mysqli_fetch_assoc($allHistory)) {
                $labels[] = $row["year"];
                $prices[] = $row["avg_price"];
            }
            $data = [
                "label"=>"Bar dataset",
                "fillColor"=>"#46BFBD",
                "strokeColor"=>"#46BFBD",
                "highlightFill"=>"rgba(70, 191, 189, 0.4)",
                "highlightStroke"=>"rgba(70, 191, 189, 0.9)",
                "data"=> $prices];
            $result = ["labels"=>$labels, "datasets"=>[$data]];
            //
            $res = $response->withJson($result)
                            ->withHeader('Content-type', 'application/json');
            return $res;
        } else if (is_null($prefecture) && !is_null($itemID)) { //

        } else {//

        }

        return $response;
    }

}