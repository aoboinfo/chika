<?php
use Price\LandPrice;
use Price\AveragePrice;

// Routes
$app->get('/', 'Price\TopController:showTopPage')->setName('topPage');
$app->get('/{prefecture}', 'Price\PrefectureController:showPricesFor')->setName('prefecture-price'); //setName: url name for later reference.
$app->get('/{prefecture}/{city}', 'Price\PrefectureController:showPricesForCity')->setName('city-price'); //setName: url name for later reference.
//get price history at country, prefecture, id level
/*$app->get('/avgs/[{city}/[{id}]]', function ($request, $response, $args) {
    $prefecture = $request->getAttribute('city');
    $itemID = $request->getAttribute('id');
    $response->getBody()->write("Hello, $prefecture" . " " . $itemID);

    return $response;
});*/
//
$app->get('/avgs/[{prefecture}/[{id}]]', 'Service\PostedPriceService:historyPriceOf');
$app->get('/avg2/[{prefecture}/[{id}]]', 'Service\PostedPriceService:historyPriceL2');
$app->get('/changeRate/[{prefecture}/[{city}]]', 'Service\PostedPriceService:changeRate');
$app->get('/mapItems/[{prefecture}/[{city}]]', 'Service\PostedPriceService:itemsOnMap');