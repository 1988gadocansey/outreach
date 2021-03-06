<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of massageclass
 *
 * @author orcons systems
 */
class massageclass {
    public $sql;
    //put your code here
    function  __construct() {
        global $sql,$session;
        $this->session= $session;
        $this->sql = $sql;
    }

    public function getMessages($state="1",$start="0",$limit="30",$staffid=""){
        if($state == "2"){
   $query = "SELECT * FROM mail ML LEFT JOIN (SELECT * FROM mailread WHERE MR_SYS='1' AND MR_EMPLOYEE_ID =".$this->sql->Param('a').") AS MR ON ML.MI= MR.MR_MAIL_ID WHERE MR.MR_EMPLOYEE_ID IS NOT NULL ORDER BY ML.MI_DATE ASC LIMIT ".$this->sql->Param('b').",".$this->sql->Param('c');
   $stmt = $this->sql->Prepare($query);
   $stmt =$this->sql->Execute($stmt,array($staffid,$start,$limit));
    }else if($state == "3"){
       //$query = "SELECT * FROM mail AS ML LEFT JOIN mailread AS MR ON ML.MI=MR.MR_MAIL_ID WHERE MR.MR_STAFF_ID='".$staffid."' LIMIT ".$this->sql->Param('a').",".$this->sql->Param('b');
       $query = "SELECT * FROM mail ML LEFT JOIN (SELECT * FROM mailread WHERE MR_SYS='1' AND MR_EMPLOYEE_ID =".$this->sql->Param('a').") AS MR ON ML.MI= MR.MR_MAIL_ID WHERE MR.MR_EMPLOYEE_ID IS NULL ORDER BY ML.MI_DATE ASC LIMIT ".$this->sql->Param('b').",".$this->sql->Param('c');
       $stmt = $this->sql->Prepare($query);
       $stmt =$this->sql->Execute($stmt,array($staffid,$start,$limit));
    }else{
        $query = "SELECT * FROM mail AS ML ORDER BY MI_DATE ASC LIMIT ".$this->sql->Param('a').",".$this->sql->Param('b')."";
        $stmt = $this->sql->Prepare($query);
        $stmt =$this->sql->Execute($stmt,array($start,$limit));
        print $this->sql->ErrorMsg();
        }
      
        
        return $stmt;

    }

    public function getMessageBy($msgid,$area,$userid){

   if($area=="pv"){
       $sys = '1';
       $query = "SELECT * FROM mail WHERE MD5(MI)=".$this->sql->Param('a');
   }else if($area=="payslip"){
       $sys = '2';
       $query = "SELECT * FROM mail WHERE MI_LEVEL='0' AND MD5(MI)=".$this->sql->Param('a');
   }
    
        $stmt = $this->sql->Prepare($query);
        $stmt =$this->sql->Execute($stmt,array($msgid));
        $obj = $stmt->FetchNextObject();
        $this->setMailToRead($userid, $obj->MI,$sys);
         return $obj;
    }

    private function setMailToRead($userid,$mailid,$sys){
        $stmt = $this->sql->Prepare("SELECT * FROM mailread WHERE MR_EMPLOYEE_ID=".$this->sql->Param('a')." AND MR_MAIL_ID=".$this->sql->Param('b')." AND MR_SYS=".$this->sql->Param('c'));
        $stmt =$this->sql->Execute($stmt,array($userid,$mailid,$sys));
        if($stmt->RecordCount() == 0){
        $record = array();
        $record["MR_MAIL_ID"]=$mailid;
        $record["MR_EMPLOYEE_ID"]=$userid;
        $record["MR_SYS"]=$sys;
         $insertSQL = $this->sql->GetInsertSQL($stmt, $record);
         $stmt = $this->sql->Execute($insertSQL);
        }
    }

    public function getMessagesPayslip($state="1",$start="0",$limit="30",$staffid=""){
        if($state == "2"){
$query = "SELECT * FROM mail ML LEFT JOIN (SELECT * FROM mailread WHERE MR_SYS='2' AND MR_EMPLOYEE_ID =".$this->sql->Param('a').") AS MR ON ML.MI= MR.MR_MAIL_ID WHERE ML.MI_LEVEL='0'  AND MR.MR_EMPLOYEE_ID IS NOT NULL ORDER BY ML.MI_DATE ASC LIMIT ".$this->sql->Param('b').",".$this->sql->Param('c');
$stmt = $this->sql->Prepare($query);
$stmt =$this->sql->Execute($stmt,array($staffid,$start,$limit));
}else if($state == "3"){
       $query = "SELECT * FROM mail ML LEFT JOIN (SELECT * FROM mailread WHERE MR_SYS='2' AND MR_EMPLOYEE_ID =".$this->sql->Param('a').") AS MR ON ML.MI= MR.MR_MAIL_ID WHERE ML.MI_LEVEL='0'  AND MR.MR_EMPLOYEE_ID IS NULL ORDER BY ML.MI_DATE ASC LIMIT ".$this->sql->Param('b').",".$this->sql->Param('c');
       $stmt = $this->sql->Prepare($query);
       $stmt =$this->sql->Execute($stmt,array($staffid,$start,$limit));
    }else{
        $query = "SELECT * FROM mail AS ML  WHERE ML.MI_LEVEL='0' ORDER BY ML.MI_DATE ASC LIMIT ".$this->sql->Param('a').",".$this->sql->Param('b');
        $stmt = $this->sql->Prepare($query);
        $stmt =$this->sql->Execute($stmt,array($start,$limit));

        }

        
        return $stmt;

    }//end of getMessagesPayslip
    
    public function getNumberOfUnreadMail($level,$staffid){
        if($level=="pv"){
        $stmt = $this->sql->Prepare("SELECT * FROM mail ML LEFT JOIN (SELECT * FROM mailread WHERE MR_SYS='1' AND MR_EMPLOYEE_ID =".$this->sql->Param('a').") AS MR ON ML.MI= MR.MR_MAIL_ID WHERE MR.MR_EMPLOYEE_ID IS NULL");
        }else if($level=="payslip"){
         $stmt = $this->sql->Prepare("SELECT * FROM mail ML LEFT JOIN (SELECT * FROM mailread WHERE MR_SYS='2' AND MR_EMPLOYEE_ID =".$this->sql->Param('a').") AS MR ON ML.MI= MR.MR_MAIL_ID WHERE ML.MI_LEVEL='0'  AND MR.MR_EMPLOYEE_ID IS NULL");
        }
       
         $stmt =$this->sql->Execute($stmt,array($staffid));
          print $this->sql->ErrorMsg();
         return $stmt->RecordCount();

    }

    public function getMailTotal($level){
        if($level=="pv"){
             $query = "SELECT * FROM mail AS ML";
        }else{
         $query = "SELECT * FROM mail AS ML WHERE ML.MI_LEVEL='0'";
        }
       
         $stmt = $this->sql->Prepare($query);
        $stmt =$this->sql->Execute($stmt);
        return $stmt->RecordCount();
    }
    public function getTotalMailRead($staffid){
        $query = "SELECT * FROM mailread AS MR WHERE MR.MR_EMPLOYEE_ID=".$this->sql->Param('a');
         $stmt = $this->sql->Prepare($query);
        $stmt =$this->sql->Execute($stmt,array($staffid));
        return $stmt->RecordCount();
    }

public function checkIfRead($staffid,$mailid,$level){
   $query = "SELECT * FROM mailread AS MR WHERE MR.MR_EMPLOYEE_ID=".$this->sql->Param('a')." AND MR.MR_MAIL_ID=".$this->sql->Param('b')." AND MR.MR_SYS=".$this->sql->Param('c');
         $stmt = $this->sql->Prepare($query);
        $stmt =$this->sql->Execute($stmt,array($staffid,$mailid,$level));
        if($stmt->RecordCount() > 0){
        return true;}else{
        return false;
        }
}
}
?>
