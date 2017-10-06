<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/10/05
 * Time: 15:51
 */

namespace Price;


class AddressSearchController extends SearchController
{
    public function __construct(Twig $view, Router $router, Mysqli $db, Logger $logger) {
        parent::__construct($view, $router, $db, $logger);
    }

}