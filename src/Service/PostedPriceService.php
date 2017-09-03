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
    private $recCount;

    /**
     * @return mixed
     */
    public function getRecCount()
    {
        return $this->recCount;
    }

    /**
     * @param mixed $recCount
     */
    public function setRecCount($recCount)
    {
        $this->recCount = $recCount;
    }

    public function __construct(Router $router, Mysqli $db)
    {
        $this->db = $db;
        $this->router = $router;
    }
    public function historyPriceL2 ($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $itemID = $request->getAttribute('id');
        $queryStr = "";
        if (is_null($prefecture) && is_null($itemID)) { //
            $queryStr = "select year, ROUND(AVG(price)) as avg_price from survey_his where price <> 0 group by year order by year";
        } else if (!is_null($prefecture) && is_null($itemID)) { //
            $queryStr = "select ROUND(AVG(price)) as avg_price, year from survey_his where price <> 0 and prefecture = '" . $prefecture . "' group by year order by year";
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
    public function historyPriceOf ($request, $response, $params) {
       // $cities = $this->db->query("select distinct city from posted_price where address like '" . $params['prefecture'] . "%'");
        $prefecture = $request->getAttribute('prefecture');
        $itemID = $request->getAttribute('id');
        //
        $commonQuery = "select year, ROUND(AVG(price)) as avg_price from";
        $postPriceQuery = ""; //公示
        $surveyPriceQuery = ""; //調査
        //
        if (is_null($prefecture) && is_null($itemID)) { //
            $postPriceQuery = $commonQuery . " posted_his where price <> 0 group by year order by year";
            $surveyPriceQuery = $commonQuery . " survey_his where price <> 0 group by year order by year";
        } else if (!is_null($prefecture) && is_null($itemID)) { //
            $postPriceQuery = $commonQuery . " posted_his where price <> 0 and prefecture = '" . $prefecture . "' group by year order by year";
            $surveyPriceQuery = $commonQuery . " survey_his where price <> 0 and prefecture = '" . $prefecture . "' group by year order by year";
        } else {//

        }
        //post prices
        $postHistory = $this->db->query($postPriceQuery);
        $labels = array();
        $postPrices = array();
        while ($row = mysqli_fetch_assoc($postHistory)) {
            $labels[] = $row["year"];
            $postPrices[] = $row["avg_price"];
        }
        $postHistory->close();
        $this->setRecCount(count($labels));
        //
        //survey prices
        $surveyHistory = $this->db->query($surveyPriceQuery);
        $surveyPrices = array();
        while ($row = mysqli_fetch_assoc($surveyHistory)) {
            $surveyPrices[] = $row["avg_price"];
        }
        for ($i = count($surveyPrices); $i < $this->getRecCount(); $i++) {
            $surveyPrices[] = "0";
        }
        //
        $result = ["labels"=>$labels, "postPrices"=>$postPrices, "surveyPrices"=>$surveyPrices];
        //
        $res = $response->withJson($result)
            ->withHeader('Content-type', 'application/json');
        return $res;
    }
    //Price information for changeRate.
    public function changeRate ($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $cityName = $request->getAttribute('city');
        //
        $changeRatePost = null;
        $changeRateSurvey = null;
        $postChangeRateQuery = "select a.prefecture as label, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from posted_his as a inner join posted_his as b on a.id = b.id";
        $surveyChangeRateQuery = "select a.prefecture, format(100*(avg(a.price) - avg(b.price))/avg(b.price), 2) as rate from survey_his as a inner join survey_his as b on a.id = b.id";
        if (is_null($prefecture) && is_null($cityName)) {
            $changeRatePost = $this->db->query($postChangeRateQuery . " where a.year = 2017 and b.year = 2016 and b.price <> 0 group by a.prefecture order by a.prefecture desc");
            $changeRateSurvey = $this->db->query($surveyChangeRateQuery . " where a.year = 2016 and b.year = 2015 and b.price <> 0 group by a.prefecture order by a.prefecture desc");
        } else if (!is_null($prefecture) && is_null($cityName)) {//for prefecture

        } else {//for city

        }
        $labels = array();
        $ratesPost = array();
        while ($row = mysqli_fetch_assoc($changeRatePost)) {
            $labels[] = $row["label"];
            $ratesPost[] = $row["rate"];
        }
        $changeRatePost->close();
        //
        $ratesSurvey = array();
        while ($row = mysqli_fetch_assoc($changeRateSurvey)) {
            $ratesSurvey[] = $row["rate"];
        }
        //
        $result = ["labels" => $labels, "postRates" => $ratesPost, "surveyRates"=>$ratesSurvey];
        $res = $response->withJson($result)
            ->withHeader('Content-type', 'application/json;charset=utf-8');
        return $res;
    }

}