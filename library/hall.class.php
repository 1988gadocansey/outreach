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
 * PPO ie Plain PHP Object
 */
class Hall {

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

    public function getHall($hall) {

       $query = "SELECT * FROM tpoly_hall WHERE   HALL_NAME =".$this->connect->Param('a')."";
			

        $stmt = $this->connect->Prepare($query);
        $stmt = $this->connect->Execute($stmt,array($hall));
        return $stmt->FetchNextObject();
    }

}
