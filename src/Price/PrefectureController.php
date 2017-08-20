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


class PrefectureController
{
    private $view;
    private $router;
    private $db;
    //
    public function __construct(Twig $view, Router $router, Mysqli $db) {

        $this->view = $view;
        $this->router = $router;
        $this->db = $db;
    }
    //
    public function showPricesFor ($request, $response, $params) {

        $cities = $this->db->query("select distinct city from posted_price where address like '" . $params['prefecture'] . "%'");
        $result = array();
        while ($row = mysqli_fetch_assoc($cities)) {
            $result[] = $row["city"];
        }
        $cities->close();
        $this->view->render($response, 'landprice/prefecture.twig',
            ["posted_title"=> $params['prefecture'],
                "areas" => [$params['prefecture']],
                "price_target" => $params['prefecture'],
                "leftMenus" => [$result],
                "title"=> $params['prefecture'] . "地価" ]);
    }

}