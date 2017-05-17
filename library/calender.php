<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Calender
 *
 * @author gadoo
 */
class Calender {

    private $config;
    private $session;
    private $connect;

    public function __construct() {

        global $config;
        global $sql, $session;
        $this->config = $config;
        $this->session = $session;
        $this->connect = $sql;
    }

    public function getCalender() {

        $query = "SELECT * FROM tpoly_academic_settings  ";

        $stmt = $this->connect->Prepare($query);
        $stmt = $this->connect->Execute($stmt);
        return $stmt->FetchNextObject();
    }

}
