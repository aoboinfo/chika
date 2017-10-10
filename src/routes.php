<?php

//Routes
$app->get('/', 'Price\TopController:showTopPage')->setName('topPage');
$app->get('/{prefecture}', 'Price\PrefectureController:showPricesFor')->setName('prefecture-price'); //setName: url name for later reference.
$app->get('/{prefecture}/{city}', 'Price\PrefectureController:showPricesForCity')->setName('city-price'); //setName: url name for later reference.
$app->get('/list/stationPost/{station}/[{prefecture}/[{city}]]', 'Price\StationSearchController:findPostListForStation')->setName('post-station');
$app->get('/list/stationSurvey/{station}/[{prefecture}/[{city}]]', 'Price\StationSearchController:findSurveyListForStation')->setName('survey-station');
//get price history at country, prefecture, id level
/*$app->get('/avgs/[{city}/[{id}]]', function ($request, $response, $args) {
    $prefecture = $request->getAttribute('city');
    $itemID = $request->getAttribute('id');
    $response->getBody()->write("Hello, $prefecture" . " " . $itemID);

    return $response;
});*/
//
$app->get('/avgs/[{prefecture}/[{city}]]', 'Service\PostedPriceService:historyPriceOf');
$app->get('/changeRate/[{prefecture}/[{city}]]', 'Service\PostedPriceService:changeRate');
$app->get('/mapItems/[{prefecture}/[{city}]]', 'Service\PostedPriceService:itemsOnMap');
$app->get('/listingCityPlan/{prefecture}/[{city}]', 'Service\PostedPriceService:itemsForCityPlanAndUsage');