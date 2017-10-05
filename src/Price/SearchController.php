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
    private $view;
    private $router;
    private $db;
    private $logger;
    //
    public function __construct(Twig $view, Router $router, Mysqli $db, Logger $logger) {

        $this->view = $view;
        $this->router = $router;
        $this->db = $db;
        $this->logger = $logger;
    }

}