<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:49
 */

namespace Price;


class OptionsSearchController extends SearchController
{
    const LIMIT_REC = 10;

    private $priceName;
    private $target;
    /**
     * @return mixed
     */
    public function getPriceName()
    {
        return $this->priceName;
    }

    /**
     * @param mixed $priceName
     */
    public function setPriceName($priceName)
    {
        $this->priceName = $priceName;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    //
    public function findOptions ($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $city = $request->getAttribute('city');
        //
        $allGetVars = $request->getQueryParams();
        //
        $priceType = $allGetVars['type'];
        $option = $allGetVars['option'];
        $value = $allGetVars['value'];
        $offset = $allGetVars['offset'];
        $queryString = null;
        if ($option == 'usage') {
            $queryString = $this->createUsagesSQLString($prefecture, $city, $priceType, $value, $offset);
        } else {
            $queryString = $this->createCityplanSQLString($prefecture, $city, $priceType, $value, $offset);
        }
        $queryOfStation = $this->db->query($queryString);
        $resultOfOption = array();
        while ($row = mysqli_fetch_assoc($queryOfStation)) {
            $landPrice = new LandPrice();
            $landPrice->setStation($row["near_station"]);
            $landPrice->setPrice("¥" . number_format($row["price0"]));
            $landPrice->setChangeRate($row["rate"]);
            $landPrice->setAddress($row["address"]);
            $landPrice->setDistanceFromStation($row["distance_station"]);
            $landPrice->setCurrentUsage($row["current_use"]);
            $landPrice->setStructure($row["build_structure"]);
            $landPrice->setCityPlan($row["city_plan"]);
            $landPrice->setType($this->getPriceName());
            $resultOfOption[] = $landPrice;
        }
        $queryOfStation->close();
        //
        $pageLabel = $prefecture . "/" . $value . "/";
        $linkType = 1; //only prefecture.
        $stationsLowRanking = $this->getLowStationListForTarget($this->getTarget(), $prefecture);
        if (!is_null($city)) {
            $pageLabel = $prefecture . $city . "/" . $value . "/";
            $linkType = 2; //only prefecture, city
            $stationsLowRanking = null;
        }
        if ($priceType == 0) {
            $usages = $this->optionsList(SearchController::POST_TABLE, $prefecture, $city, "0");
            $cityPlans = $this->optionsList(SearchController::POST_TABLE, $prefecture, $city, "1");
            $this->setLeftOptions(["公示：利用現況","公示：用途地域"]);
        } else {
            $usages = $this->optionsList(SearchController::SURVEY_TABLE, $prefecture, $city, "0");
            $cityPlans = $this->optionsList(SearchController::SURVEY_TABLE, $prefecture, $city, "1");
            $this->setLeftOptions(["調査：利用現況","調査：用途地域"]);
        }
        //
        return $this->view->render($response, 'searchResult.twig',
            [
                "areas" => [$prefecture],
                "leftMenus" => [$this->getCityList($this->getTarget(), $prefecture)],
                "stationTop" => $this->getTopStationListForTarget($this->getTarget(), $prefecture, $city),
                "stationLow" => $stationsLowRanking,
                "listings" => $resultOfOption,
                "optionMenus" => [$usages,$cityPlans],
                "options" => $this->getLeftOptions(), //the menu list below is place to be selected
                "prefectureLabel" => $prefecture,
                "pageLabel" => $pageLabel,
                "priceType" => $this->getPriceName(),
                "city" => $city,
                "linkType" => $linkType,
                "offset" => $offset
            ]
        );

    }
    //
    private function createUsagesSQLString ($prefecture, $city, $priceType, $value, $offset) {
        $usageQuery = SearchController::BASIC_QUERY_STR;
        if ($priceType == 0) { //地価公示
            $this->setPriceName(SearchController::POST_KANJI);
            $this->setTarget(SearchController::POST_VIEW);
            $usageQuery = $usageQuery . SearchController::POST_VIEW . " where ";
        } else { //地価調査
            $usageQuery = $usageQuery . SearchController::SURVEY_VIEW. " where ";
            $this->setPriceName(SearchController::SURVEY_KANJI);
            $this->setTarget(SearchController::SURVEY_VIEW);
        }
        if (!is_null($city)) {
            $usageQuery = $usageQuery . "address like '" . $prefecture . "%' and city = '" . $city . "' and current_use = '" . $value . "'" ;
        } else {
            $usageQuery = $usageQuery . "address like '" . $prefecture . "%' and current_use = '" . $value . "'" ;
        }
        $usageQuery = $usageQuery . " order by price0 desc limit " . OptionsSearchController::LIMIT_REC . " offset " . $offset;

        return $usageQuery;
    }
    //
    private function createCityplanSQLString ($prefecture, $city, $priceType, $value, $offset) {
        $cityPlanQuery = SearchController::BASIC_QUERY_STR;
        if ($priceType == 0) { //地価公示
            $cityPlanQuery = $cityPlanQuery . SearchController::POST_VIEW . " where ";
            $this->setPriceName(SearchController::POST_KANJI);
            $this->setTarget(SearchController::POST_VIEW);
        } else { //地価調査
            $cityPlanQuery = $cityPlanQuery . SearchController::SURVEY_VIEW. " where ";
            $this->setPriceName(SearchController::SURVEY_KANJI);
            $this->setTarget(SearchController::SURVEY_VIEW);
        }
        if (!is_null($city)) {
            $cityPlanQuery = $cityPlanQuery . "address like '" . $prefecture . "%' and city = '" . $city . "' and city_plan = '" . $value . "'" ;
        } else {
            $cityPlanQuery = $cityPlanQuery . "address like '" . $prefecture . "%' and city_plan = '" . $value . "'" ;
        }
        $cityPlanQuery = $cityPlanQuery . " order by price0 desc limit " . OptionsSearchController::LIMIT_REC . " offset " . $offset;

        return $cityPlanQuery;
    }
}