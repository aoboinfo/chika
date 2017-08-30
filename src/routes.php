<?php
use Price\LandPrice;
use Price\AveragePrice;

// Routes
$app->get('/', function ($request, $response) {
    // Sample log message
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
    $stationQuery = "select near_station, price0, concat('¥', FORMAT(price0,0)) as price_jp, concat('¥', FORMAT(round(price0/3.305785), 0)) as price_tubo, FORMAT(100*(price0-price1)/price1, 1) as rate from post_price where price1 <> 0 group by near_station order by price0";
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

    //posted average price/year
    $posted_avg_qry = "select year as my, FORMAT(ROUND(AVG(price)),0) as mavg from posted_his where price <> 0 group by year order by my";
    $posted_avg = $this->db->query($posted_avg_qry);
    //
    $averagePrices = array();
    //
    while ($row = mysqli_fetch_row($posted_avg)) {
        $averagePrice = new AveragePrice();
        $averagePrice->setYear($row["my"]);
        $averagePrice->setPostedPrice($row["mavg"]);
        $this->logger->info("avgPrice:" . $row["my"] . "/" . $row["mavg"]);
        $averagePrices[] = $averagePrice;
    }
    $posted_avg->close();

    $survey_avg = $this->db->query("select year, FORMAT(ROUND(AVG(price)),0) as avg_price from survey_his where price <> 0 group by year order by year");
    $index = 0;
    while ($row = mysqli_fetch_row($survey_avg)) {
        $averagePrice = $averagePrices[$index];
        $averagePrice->setSurveyPrice($row["avg_price"]);
        $index++;
    }
    $survey_avg->close();
    //
    return $this->view->render($response, 'top.twig',
        [
            "areas" => $areas,
            "leftMenus" => $prefectures,
            "stationTop" => $stationsDesc,
            "stationLow" => $stationAsc
        ]
    );
});

$app->get('/{prefecture}', 'Price\PrefectureController:showPricesFor')->setName('prefecture-price'); //setName: url name for later reference.
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
$app->get('/station/[{prefecture}/[{stationName}]]', 'Service\PostedPriceService:stationPrice');