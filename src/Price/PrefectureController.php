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
        //menu
        $prefecture = $params['prefecture'];
        $cities = $this->db->query("select distinct city from posted_price where address like '" . $prefecture . "%' order by city");
        $result = array();
        while ($row = mysqli_fetch_assoc($cities)) {
            $result[] = $row["city"];
        }
        $cities->close();
        //The posted 10 stations
        $postStationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from post_price where price1 <> 0 and address like '" . $prefecture . "%' group by near_station order by price0";
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
        //The survey 10 station
        $surveyStationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from survey_price where price1 <> 0 and address like '" . $prefecture . "%' group by near_station order by price0";
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
        $posted_avg = $this->db->query("select year, FORMAT(ROUND(AVG(price)),0) as avg_price from posted_his where price <> 0 and prefecture = '" . $prefecture . "' group by year order by year");
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
        $survey_avg = $this->db->query("select FORMAT(ROUND(AVG(price)),0) as avg_price from survey_his where price <> 0 and prefecture = '" . $prefecture . "' group by year order by year");
        $index = 0;
        while ($row = mysqli_fetch_assoc($survey_avg)) {
            $averagePrice = $averagePrices[$index];
            $averagePrice->setSurveyPrice($row["avg_price"]);
            $index++;
        }
        $survey_avg->close();

        $this->view->render($response, 'landprice/prefecture.twig',
            [
                "posted_title"=> $params['prefecture'],
                "areas" => [$params['prefecture']],
                "price_target" => $params['prefecture'],
                "leftMenus" => [$result],
                "title"=> $params['prefecture'] . "地価",
                "stationTop" => $stationsDesc,
                "stationLow" => $stationAsc,
                "surveyStationTop" => $surveyStationsDesc,
                "surveyStationLow" => $surveyStationsAsc,
                "avgPrices" => $averagePrices
            ]
        );
    }

}