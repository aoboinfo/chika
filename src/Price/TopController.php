<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/08/31
 * Time: 17:06
 */

namespace Price;

use Slim\Views\Twig;
use Slim\Router;
use Monolog\Logger;
use Mysqli;

class TopController
{
    private $view;
    private $router;
    private $db;
    private $logger;
    //
    public function __construct(Twig $view, Router $router, Mysqli $db, Logger $logger) {

        $this->view = $view;
        $this->router = $router;
        $this->db = $db;
        $this->logger = $logger;
    }
    public function showTopPage ($request, $response, $params) {

        $this->logger->info("LandPrice '/' site home");
        //
        $areas = ["北海道・東北","関東","信越・北陸","東海","近畿","中国","四国","九州・沖縄"];
        $prefectures = [
            ["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県"],
            ["東京都","神奈川県","千葉県","埼玉県", "茨城県","栃木県","群馬県","山梨県"],
            ["新潟県","長野県","富山県","石川県","福井県"],
            ["愛知県","岐阜県","静岡県","三重県"],
            ["大阪府","京都府","滋賀県","兵庫県","奈良県","和歌山県"],
            ["鳥取県","島根県","岡山県","広島県","山口県"],
            ["徳島県","香川県","愛媛県","高知県"],
            ["福岡県","佐賀県","長崎県","熊本県","大分県","宮崎県","鹿児島県","沖縄県"]
        ];
        // Render index view
        $stationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from post_price where price1 <> 0 group by near_station order by price0";
        //The top 20 stations
        $stationTop10 = $this->db->query($stationQuery . " desc limit 10");
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
        $stationLow10 = $this->db->query($stationQuery .  " limit 10");
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
        //posted average price/year
        $posted_avg = $this->db->query("select year, FORMAT(ROUND(AVG(price)),0) as avg_price from posted_his where price <> 0 group by year order by year");
        //
        $averagePrices = array();
        //
        while ($row = mysqli_fetch_assoc($posted_avg)) {
            //$this->logger->info("avgPrice:" . $row["year"] . "/" . $row["avg_price"]);
            $averagePrice = new AveragePrice();
            $averagePrice->setYear($row["year"]);
            $averagePrice->setPostedPrice($row["avg_price"]);
            $averagePrices[] = $averagePrice;
        }
        $posted_avg->close();
        //
        $survey_avg = $this->db->query("select year, FORMAT(ROUND(AVG(price)),0) as avg_price from survey_his where price <> 0 group by year order by year");
        $index = 0;
        while ($row = mysqli_fetch_assoc($survey_avg)) {
            $averagePrice = $averagePrices[$index];
            $averagePrice->setSurveyPrice($row["avg_price"]);
            $index++;
        }
        $survey_avg->close();
        /*Ranking area*/
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
        //
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

        //render the top page
        return $this->view->render($response, 'top.twig',
            [
                "areas" => $areas,
                "leftMenus" => $prefectures,
                "stationTop" => $stationsDesc,
                "stationLow" => $stationAsc,
                "avgPrices" => $averagePrices,
                "postTopPref" => $postedTop10Pref,
                "postLowPref" => $postedLow10Pref,
                "surveyTopPref" => $surveyTop10Pref,
                "surveyLowPref" => $surveyLow10Pref
            ]
        );
    }
}