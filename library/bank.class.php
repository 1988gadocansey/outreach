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
class Bank {

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

    public function getBank($bank) {

       $query = "SELECT * FROM tpoly_banks WHERE    ID =".$this->connect->Param('a')."";
			

        $stmt = $this->connect->Prepare($query);
        $stmt = $this->connect->Execute($stmt,array($bank));
        return $stmt->FetchNextObject();
    }

}
