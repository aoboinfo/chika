<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:49
 */

namespace Price;


class StationSearchController extends SearchController
{
    public function findPostListForStation($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $city = $request->getAttribute('city');
        $station = $request->getAttribute('station');
        
        if (is_null($prefecture) && is_null($city)) {

        } else if (is_null($prefecture) && !is_null($city)) {

        } else {

        }

        return $response;
    }

    public function findSurveyListForStation($request, $response, $params) {
        $prefecture = $request->getAttribute('prefecture');
        $city = $request->getAttribute('city');

    }
}