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
use Price\LandPrice;

class PrefectureController
{
    private $view;
    private $router;
    //
    public function __construct(Twig $view, Router $router) {
        $this->view = $view;
        $this->router = $router;
    }
    //
    public function showPricesFor ($request, $response, $params) {
        $this->view->render($response, 'landprice/prefecture.twig', ["areas" => [$params['prefecture']], "leftMenus" => [['aa', 'bb']]]);
    }

}