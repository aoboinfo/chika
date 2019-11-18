<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:57
 */

namespace Price;

use Slim\Views\Twig as Twig;
use Slim\Router as Router;
use Monolog\Logger as Logger;
use Mysqli as Mysqli;
use SlimSession\Helper as SessionHelper;

class SearchController
{
    protected $view;
    protected $router;
    protected $db;
    protected $logger;
    protected $session;

    const POST_TYPE = 0;
    const SURVEY_TYPE = 1;
    const POST_TABLE = "posted_price";
    const SURVEY_TABLE = "surveyed_price";
    const POST_VIEW = "post_price";
    const SURVEY_VIEW = "survey_price";
    const POST_KANJI = "地価公示";
    const SURVEY_KANJI = "地価調査";
    //
    const ALL_COUNTRY = "ALL_COUNTRY";
    //
    const BASIC_QUERY_STR = "select price0, FORMAT(100*(price0-price1)/nullif(price1, 0), 1) as rate, address, near_station, distance_station, current_use, build_structure, usage_id from ";
    //
    protected $leftOptions = [];

    protected $stationLabel;

    protected $areas = ["北海道・東北","関東","信越・北陸","東海","近畿","中国","四国","九州・沖縄"];


    protected $prefectures = [
        ["北海道", "青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県"],
        ["東京都", "神奈川県", "千葉県", "埼玉県", "茨城県", "栃木県", "群馬県", "山梨県"],
        ["新潟県", "長野県", "富山県", "石川県", "福井県"],
        ["愛知県", "岐阜県", "静岡県", "三重県"],
        ["大阪府", "京都府", "滋賀県", "兵庫県", "奈良県", "和歌山県"],
        ["鳥取県", "島根県", "岡山県", "広島県", "山口県"],
        ["徳島県", "香川県", "愛媛県", "高知県"],
        ["福岡県", "佐賀県", "長崎県", "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県"]
    ];

    /**
     * @return array
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @return array
     */
    public function getPrefectures()
    {
        return $this->prefectures;
    }

    /**
     * @return array
     */
    public function getLeftOptions()
    {
        return $this->leftOptions;
    }

    /**
     * @param array $leftOptions
     */
    public function setLeftOptions($leftOptions)
    {
        $this->leftOptions = $leftOptions;
    }

    /**
     * @return string
     */
    public function getStationLabel()
    {
        return $this->stationLabel;
    }

    /**
     * @param string $stationLabel
     */
    public function setStationLabel($stationLabel)
    {
        $this->stationLabel = $stationLabel;
    }

    //
    public function __construct(Twig $view, Router $router, Mysqli $db, Logger $logger, SessionHelper $session) {

        $this->view = $view;
        $this->router = $router;
        $this->db = $db;
        $this->logger = $logger;
        $this->stationLabel = "全国";
        $this->session = $session;
    }

    public function getCityList($target, $prefecture) {
        $this->logger->info("getCityList#get data from session");
        $result = $this->session->get($prefecture . $target . "city", NULL);
        if ($result == NULL) {
            $this->logger->info("getCityList#create data for session");
            $cities = $this->db->query("select distinct city from " . $target . " where address like '" . $prefecture . "%' order by city");
            $result = array();
            while ($row = mysqli_fetch_assoc($cities)) {
                $result[] = $row["city"];
            }
            $cities->close();
            $this->session->set($prefecture . $target . "city", $result);
        }
        return $result;
    }
    //
    public function getTopStationListForTarget($target, $prefecture, $city) {
        $stationsDesc = NULL;
        if (is_null($city) && is_null($prefecture)) {
            $this->logger->info("getTopStationListForTarget#get data from session" . $prefecture . "/" . $city);
            $stationsDesc = $this->session->get(SearchController::ALL_COUNTRY . $target . "topStation", NULL);
            if ($stationsDesc == NULL) {

                $this->logger->info("getTopStationListForTarget#create data for session");

                $stationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from " . $target ." where price1 <> 0 group by near_station order by price0";
                //The top 10 stations
                $stationsDesc = array();
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
                $this->session->set(SearchController::ALL_COUNTRY . $target . "topStation", $stationsDesc);
            }

        } else if (!is_null($prefecture) && is_null($city)) {
            $stationsDesc = $this->session->get($prefecture . $target . "topStation", NULL);
            if ($stationsDesc == NULL) {
                $stationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from " . $target . " where price1 <> 0 and address like '" . $prefecture . "%' group by near_station order by price0";
                $stationTop10 = $this->db->query($stationQuery . " desc limit 10");
                //
                $stationsDesc = array();
                while ($row = mysqli_fetch_assoc($stationTop10)) {
                    $landPrice = new LandPrice();
                    $landPrice->setStation($row["near_station"]);
                    $landPrice->setPrice($row["price_jp"]);
                    $landPrice->setPriceOfTubo($row["price_tubo"]);
                    $landPrice->setChangeRate($row["rate"]);
                    $stationsDesc[] = $landPrice;
                }
                $stationTop10->close();
                $this->session->set($prefecture . $target . "topStation", $stationsDesc);
            }
        } else {
            $this->setStationLabel($prefecture);
            $stationsDesc = $this->session->get($prefecture . $target . $city . "topStation", NULL);
            if ($stationsDesc ==  NULL) {
                $stationsQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from " . $target . " where price1 <> 0 and city = '" . $city . "' and address like '" . $prefecture . "%' group by near_station order by price0";
                $allStations = $this->db->query($stationsQuery . " desc");
                $stationsDesc = array();
                while ($row = mysqli_fetch_assoc($allStations)) {
                    $landPrice = new LandPrice();
                    $landPrice->setStation($row["near_station"]);
                    $landPrice->setPrice($row["price_jp"]);
                    $landPrice->setPriceOfTubo($row["price_tubo"]);
                    $landPrice->setChangeRate($row["rate"]);
                    $stationsDesc[] = $landPrice;
                }
                $allStations->close();
                $this->session->set($prefecture . $target . $city . "topStation", $stationsDesc);
            }
        }
        //
        return $stationsDesc;
    }

    public function getLowStationListForTarget($target, $prefecture) {
        $stationAsc = NULL;
        if (is_null($prefecture)) {
            $stationAsc = $this->session->get(SearchController::ALL_COUNTRY . $target . "lowStation",  NULL);
            if ($stationAsc == NULL) {
                $stationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from " . $target ." where price1 <> 0 group by near_station order by price0";
                //The low 10 stations
                $stationAsc = array();
                $stationLow10 = $this->db->query($stationQuery . " limit 10");
                while ($row = mysqli_fetch_assoc($stationLow10)) {
                    $landPrice = new LandPrice();
                    $landPrice->setStation($row["near_station"]);
                    $landPrice->setPrice($row["price_jp"]);
                    $landPrice->setPriceOfTubo($row["price_tubo"]);
                    $landPrice->setChangeRate($row["rate"]);
                    $stationAsc[] = $landPrice;
                }
                $stationLow10->close();
                $this->session->set(SearchController::ALL_COUNTRY . $target . "lowStation" , $stationAsc);
            }

        } else {
            $stationAsc = $this->session->get($prefecture . $target . "lowStation" , NULL);
            if ($stationAsc == NULL) {
                $stationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0*3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from " . $target . " where price1 <> 0 and address like '" . $prefecture . "%' group by near_station order by price0";
                $stationLow10 = $this->db->query($stationQuery . " limit 10");
                //The low 10 stations
                $stationAsc = array();
                while ($row = mysqli_fetch_assoc($stationLow10)) {
                    $landPrice = new LandPrice();
                    $landPrice->setStation($row["near_station"]);
                    $landPrice->setPrice($row["price_jp"]);
                    $landPrice->setPriceOfTubo($row["price_tubo"]);
                    $landPrice->setChangeRate($row["rate"]);
                    $stationAsc[] = $landPrice;
                }
                $stationLow10->close();
                $this->session->set($prefecture . $target . "lowStation" , $stationAsc);
            }
        }
        //
        return $stationAsc;
    }
    //
    public function optionsList($target, $prefecture, $city, $type) {
        $result = array();
        if ($type == "0") {
            if (is_null($city)) {
                $queryString = "select current_use, count(*) as u_count from " . $target . " where address like '" . $prefecture . "%' group by current_use order by u_count desc";
            } else {
                $queryString = "select current_use, count(*) as u_count from " . $target . " where address like '" . $prefecture . "%' and city = '" . $city . "' group by current_use order by u_count desc";
            }
            $usagesQuery = $this->db->query($queryString);
            while($row = mysqli_fetch_assoc($usagesQuery)) {
                $usage = ["name" => $row["current_use"], "count" => $row["u_count"]];
                //
                $result[] = $usage;
            }
            $usagesQuery->close();
        } else {
            if (is_null($city)) {
                $queryString = "select city_plan, count(*) as u_count from " . $target . " where address like '" . $prefecture . "%' group by city_plan order by u_count desc";
            } else {
                $queryString = "select city_plan, count(*) as u_count from " . $target . " where address like '" . $prefecture . "%' and city = '" . $city . "' group by city_plan order by u_count desc";
            }
            $cityPlanQuery = $this->db->query($queryString);
            while($row = mysqli_fetch_assoc($cityPlanQuery)) {
                $cityPlan = ["name" => $row["city_plan"], "count" => $row["u_count"]];
                //
                $result[] = $cityPlan;
            }
            $cityPlanQuery->close();
        }
        return $result;
    }
    public function getTargetYear() {
        $year1 = $this->session->get("year_01", "");
        $year2 = $this->session->get("year_02", "");
        if ($year1 == "") {
            $noticeQuery = $this->db->query("SELECT year_01, year_02 FROM notice ORDER by created DESC limit 1");
            $noticeRec = $noticeQuery->fetch_assoc();
            $this->session->set("year_01", $noticeRec['year_01']);
            $this->session->set("year_02", $noticeRec['year_02']);
            $year = [
                "year01"=> $noticeRec['year_01'],
                "year02"=> $noticeRec['year_02']
            ];
            $noticeQuery->close();
            return $year;
        } else {
            $year = [
                "year01"=> $year1,
                "year02"=> $year2
            ];
            return $year;
        }
    }

    public function getCurrentYear() {
        $noticeQuery = $this->db->query("select current_year from notice order by created desc LIMIT 1");
        $row = $noticeQuery->fetch_assoc();
        $currentYear = $row['current_year'];
        $noticeQuery->close();
        return $currentYear;
    }
}