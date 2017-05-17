<?php
    /**
     *@desc this class handles all the client end log in details and methods
     *@desc this depands on the connect.php and Session.class.php
     */
	 
	
	class Login{
		private $session;
		private $redirect;
		private $hashkey;
		private $md5;
		private $remoteip;
		private $useragent;
		private $sessionid;
		private $result;
		private $connect;
		private $crypt;
        private $jconfig;

	
		
		
		public function __construct(){
			 
			global $sql,$session;
            $this->jconfig = new JConfig();
			$this->redirect ="index.php?action=Login";
			$this->hashkey	=$_SERVER['HTTP_HOST'];
			$this->md5=true;
			$this->remoteip = $_SERVER['REMOTE_ADDR'];
			$this->useragent = $_SERVER['HTTP_USER_AGENT'];
			$this->session	=$session;
			$this->connect = $sql;
			$this->crypt = new cryptCls();
            $this->sessionid = $this->session->getSessionID();
			//$this->signin();
			
		}
		
		private function signin($passwrd){
			
			 
			
			 
				 
						
			$query = "SELECT * FROM outreach WHERE admitted='1' AND  applicationNumber =".$this->connect->Param('a')."";
			
			$stmt = $this->connect->Prepare($query);
			$stmt = $this->connect->Execute($stmt,array($passwrd));
			print $this->connect->ErrorMsg();
			
			if($stmt){		

				if($stmt->RecordCount() > 0){
					
				$this->session->del("logincountpay");		
				
				list($userid)=$stmt->FetchRow();
				$this->storeAuth($userid, $passwrd);
				$this->setLog("1");
				header('Location: ' . $this->redirect);	
				
					//actions
					
				}else{
					$this->logout("wrong");
					//$this->direct("wrong");
				}
				
				
			 
		}//end
                }
		public function direct($direction=''){
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Cache-Control: no-store, no-cache, must-validate');
			header('Cache-Control: post-check=0, pre-check=0',FALSE);
			header('Pragma: no-cache');
			
			if($direction == 'empty'){
			header('Location: ' . $this->redirect.'&attempt_in=0');	
			}else if($direction == 'wrong'){
			header('Location: ' .$this->redirect.'&attempt_in=1');	
			}else if($direction=="out"){
			header('Location: ' .$this->redirect);	
			}else if ( $direction =='captchax'){
					header('Location: ' .$this->redirect.'&attempt_in=11');
					}else{
						header('Location: ' .$this->redirect);
						}
			exit;
			
		}
		
		public function storeAuth($userid,$login)
	{
		$this->session->set('pyuserid',$userid);
		$this->session->set('h20',$login);
		
		$this->session->set('random_seed_pay',md5(uniqid(microtime())));

		$hashkey = md5($this->hashkey . $login .$this->remoteip.$this->sessionid.$this->useragent);
		$this->session->set('login_hash_pay',$hashkey);
		$this->session->set("LAST_REQUEST_TIME",time());
	}//end
	
		public function logout($msg="out")
	{
		$this->setLog("0");
		$this->session->del('pyuserid');
		$this->session->del('h20');
		$this->session->del('random_seed_pay');
		$this->session->del('login_hash_pay');
		$_SESSION = array();
		session_destroy();
		$this->direct($msg);
	}//end
	
	public function confirmAuth(){
		
		$login = $this->session->get("h20");
		$hashkey = $this->session->get('login_hash_pay');
	
		if(md5($this->hashkey . $login .$this->remoteip.$this->sessionid.$this->useragent) != $hashkey)
		{
			$this->logout();
		}
		
	}//end
	
	private function setLog($activity){
		$userid=$this->session->get("pyuserid");
          // $stmt = $this->connect->Execute("INSERT INTO logactivities (LOG_MG_ID,LOG_ACTIVITY,LOG_REMOTE_IP,LOG_BROWSER_AGENT,LOG_SESSION_ID,LOG_DATE,LOG_AREA) VALUES ('".$userid."','".$activity."','".$this->remoteip."','".$this->useragent."','".$this->session->getSessionID()."',CURDATE(),'1')");
          
       }
		
		
	}
?>
