<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:57
 */

namespace Price;

use Slim\Views\Twig;
use Slim\Router;
use Monolog\Logger;
use Mysqli;

class SearchController
{
    protected $view;
    protected $router;
    protected $db;
    protected $logger;

    const POST_TYPE = 0;
    const SURVEY_TYPE = 1;
    const POST_TABLE = "posted_price";
    const SURVEY_TABLE = "surveyed_price";
    const POST_VIEW = "post_price";
    const SURVEY_VIEW = "survey_price";

    //
    protected $leftOptions = [];

    protected $stationLabel = "全国";

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
    public function __construct(Twig $view, Router $router, Mysqli $db, Logger $logger) {

        $this->view = $view;
        $this->router = $router;
        $this->db = $db;
        $this->logger = $logger;
    }

}