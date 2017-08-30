<?php
/**
 * Created by PhpStorm.
 * User: shutoukin
 * Date: 2017/08/30
 * Time: 20:28
 */

namespace Price;


class AveragePrice
{
    public $year;
    public $postedPrice;
    public $surveyPrice;

    public function __construct()
    {
        $this->year = "";
        $this->postedPrice = "";
        $this->surveyPrice = "";
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getPostedPrice()
    {
        return $this->postedPrice;
    }

    /**
     * @param mixed $postedPrice
     */
    public function setPostedPrice($postedPrice)
    {
        $this->postedPrice = $postedPrice;
    }

    /**
     * @return mixed
     */
    public function getSurveyPrice()
    {
        return $this->surveyPrice;
    }

    /**
     * @param mixed $surveyPrice
     */
    public function setSurveyPrice($surveyPrice)
    {
        $this->surveyPrice = $surveyPrice;
    }

}