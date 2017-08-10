<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/07/28
 * Time: 11:39
 */

namespace Price;

use Slim\Views\Twig;
use Slim\Router;
use Mysqli;
//
use Price\LandPrice;


class PrefectureController
{
    private $view;
    private $router;
    private $db;
    //
    public function __construct(Twig $view, Router $router, Mysqli $db) {
        $this->view = $view;
        $this->router = $router;
        $this->db = $db;
    }
    //
    public function showPricesFor ($request, $response, $params) {
        $this->view->render($response, 'landprice/prefecture.twig', ["areas" => [$params['prefecture']], "leftMenus" => [['aa', 'bb']]]);
    }

}