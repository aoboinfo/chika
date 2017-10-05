<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:50
 */

namespace src\Price;


class CityPlanSearchController extends SearchController
{
    public function __construct(Twig $view, Router $router, Mysqli $db, Logger $logger) {
        parent::__construct($view, $router, $db, $logger);
    }

}