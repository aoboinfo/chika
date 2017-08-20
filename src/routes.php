<?php
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
    return $this->view->render($response, 'top.twig', ["areas" => $areas, "leftMenus" => $prefectures]);
});

$app->get('/{prefecture}', 'Price\PrefectureController:showPricesFor')->setName('prefecture-price'); //setName: url name for later reference.