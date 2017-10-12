<?php

//Routes
$app->get('/', 'Price\TopController:showTopPage')->setName('topPage');
$app->get('/{prefecture}', 'Price\PrefectureController:showPricesFor')->setName('prefecture-price'); //setName: url name for later reference.
$app->get('/{prefecture}/{city}', 'Price\PrefectureController:showPricesForCity')->setName('city-price'); //setName: url name for later reference.
$app->get('/list/stationPost/{station}/[{prefecture}/[{city}]]', 'Price\StationSearchController:findPostListForStation')->setName('post-station');
$app->get('/list/stationSurvey/{station}/[{prefecture}/[{city}]]', 'Price\StationSearchController:findSurveyListForStation')->setName('survey-station');
$app->get('/list/options/{prefecture}/[{city}]', 'Price\OptionsSearchController:findOptions')->setName('find-options');
$app->get('/item/detail/{address}','Price\AddressSearchController:detailForAddress')->setName('item-detail');

/*$app->get('/item/detail/{address}', function ($request, $response, $args) {
    $address = $request->getAttribute('address');
    $response->getBody()->write("Hello, " . $address);
    return $response;
});*/
//
$app->get('/avgs/[{prefecture}/[{city}]]', 'Service\PostedPriceService:historyPriceOf');
$app->get('/changeRate/[{prefecture}/[{city}]]', 'Service\PostedPriceService:changeRate');
$app->get('/mapItems/[{prefecture}/[{city}]]', 'Service\PostedPriceService:itemsOnMap');
$app->get('/listingCityPlanAndUsage/{prefecture}/[{city}]', 'Service\PostedPriceService:itemsForCityPlanAndUsage');
//They must be initialized in dependencies.