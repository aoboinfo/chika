<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:49
 */

namespace Price;


class UsageSearchController extends SearchController
{
    private function usageResult ($request, $response, $params, $priceType) {
        $prefecture = $request->getAttribute('prefecture');
        $city = $request->getAttribute('city');
        $station = $request->getAttribute('station');
        if (is_null($city)) {

        } else {

        }

    }
    public function findPostUsages ($request, $response, $params) {
        return usageResult ($request, $response, $params, SearchController::POST_TYPE);
    }
    public function findSurveyUsages ($request, $response, $params) {
        return usageResult ($request, $response, $params, SearchController::SURVEY_TYPE);
    }
}