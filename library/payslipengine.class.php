<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of payslipengineclass
 *
 * @author orcons systems
 */
class payslipengineclass {
    //put your code here
    private $session;
    private $sql;

    function  __construct() {
        global $sql,$session;
        $this->sql = $sql;
        $this->session= $session;
    }

    public function generatePaySlipYear($marked="",$sort="0"){
		$currentyear = date('Y');
		$yearing = START_YEAR;
      echo '<select  class="" id="year" name="year"><option value="">Year:</option>';
      do{
      echo '<option value="'.$yearing.'" '.(($marked ==$yearing)?'selected="selected"':'').' >'.$yearing.'</option>';
      $yearing = START_YEAR +1;
      }while ($currentyear >= $yearing);
      echo '</select>';

      
}// end of generatePayYear

   public function getPayslipDetails($month,$year){
	$query = "SELECT STAFF_ID,EMPLOYEE_NUMBER,FULL_NAME,SOCIAL_SECURITY_NUMBER,ORGANIZATION,JOB,GRADE,GRADE_STEP,MINISTRY,DEPARTMENT,REGION,DISTRICT,SALARY_STRUCTURE,PAY_PERIOD FROM ALL_PEOPLE_INFO WHERE EMPLOYEE_NUMBER=".$this->sql->Param('a')." AND MONTH(PAY_PERIOD) =".$this->sql->Param('b')." AND YEAR(PAY_PERIOD) =".$this->sql->Param('c');
   	$staffid = $this->getUserDetails();
    $stmt = $this->sql->Prepare($query);
    $stmt = $this->sql->Execute($stmt,array($staffid->SA_EMPLOYEE_ID,$month,$year));
	if($stmt->RecordCount() > 0){
    	return $stmt->FetchNextObject();
	}else{
		return false;
	}

}

private function getRateBalance($element,$month,$year){
	$staffid = $this->getUserDetails();
	$stmt = $this->sql->Execute($this->sql->Prepare("SELECT BR_AMOUNT_BAL FROM ALL_BALANCE_RATE WHERE BR_ELEMENT_NAME=".$this->sql->Param('a').""),array($element));
	if($stmt->RecordCount() > 0){
		//map BR_AMOUNT_BAL to ALL_LATEST_BAL on BALANCE_NAME_SUFFIX to return AMOUNT(RATE (%) BALANCE)
		$obj = $stmt->FetchNextObject();
		$mapoutvalue = $obj->BR_AMOUNT_BAL;
		$stmtsub = $this->sql->Execute($this->sql->Prepare("SELECT AMOUNT FROM ALL_LATEST_BAL WHERE BALANCE_NAME_AND_SUFFIX=".$this->sql->Param('a')." AND EMPLOYEE_NUMBER=".$this->sql->Param('b')." AND MONTH(PAY_PERIOD) =".$this->sql->Param('c')." AND YEAR(PAY_PERIOD) =".$this->sql->Param('d')." "),array($mapoutvalue,$staffid->SA_EMPLOYEE_ID,$month,$year));
		if($stmtsub->RecordCount() > 0){
			$objsub = $stmtsub->FetchNextObject();
			return $objsub->AMOUNT;
		}else{
			return '';
		}
	}else{
		return '';
	}
	
}
public function getPayslipTransactionDetails($month,$year){
	// array details : array(date,element,balancerate,payment,deducations)
	$transaction = array();
	$staffid = $this->getUserDetails();
	//payments
	$stmt = $this->sql->Execute($this->sql->Prepare("SELECT ORIGINAL_DATE_EARNED,ELEMENT,PAY_VALUE,ELEMENT_GROUP FROM ALL_ASS_PROCESS WHERE ELEMENT_GROUP IN (SELECT EL_NAME FROM element_list WHERE EL_EGID='1' AND EL_STATUS='1') AND PAY_VALUE !='0' AND PAY_VALUE !='' AND EMPLOYEE_NUMBER=".$this->sql->Param('a')." AND MONTH(PAY_PERIOD) =".$this->sql->Param('b')." AND YEAR(PAY_PERIOD) =".$this->sql->Param('c')." AND ELEMENT NOT LIKE '%Adj' AND CLASSIFICATION != 'Information' ORDER BY PROCESSING_PRIORITY,ORIGINAL_DATE_EARNED ASC"),array($staffid->SA_EMPLOYEE_ID,$month,$year));
	if($stmt->RecordCount() > 0){
		while( $obj = $stmt->FetchNextObject()){
			$transaction[] = array($obj->ORIGINAL_DATE_EARNED,$obj->ELEMENT,'',$obj->PAY_VALUE,'');
		}
	}
	//deductions
	$stmt = $this->sql->Execute($this->sql->Prepare("SELECT ORIGINAL_DATE_EARNED,ELEMENT,PAY_VALUE,ELEMENT_GROUP FROM ALL_ASS_PROCESS WHERE ELEMENT_GROUP IN (SELECT EL_NAME FROM element_list WHERE EL_EGID='2' AND EL_STATUS='1') AND PAY_VALUE !='0' AND PAY_VALUE !='' AND EMPLOYEE_NUMBER=".$this->sql->Param('a')." AND MONTH(PAY_PERIOD) =".$this->sql->Param('b')." AND YEAR(PAY_PERIOD) =".$this->sql->Param('c')." AND ELEMENT NOT LIKE '%Adj' AND CLASSIFICATION != 'Information' ORDER BY PROCESSING_PRIORITY,ORIGINAL_DATE_EARNED ASC"),array($staffid->SA_EMPLOYEE_ID,$month,$year));
	if($stmt->RecordCount() > 0){
		while( $obj = $stmt->FetchNextObject()){
			//check for rate balance
			$ratebals = $this->getRateBalance($obj->ELEMENT,$month,$year);
			$transaction[] = array($obj->ORIGINAL_DATE_EARNED,$obj->ELEMENT,$ratebals,'',$obj->PAY_VALUE);
		}
	}
	
	//balance
	
	return $transaction;
}// end of getPayslipTransactionDetails

public function multiArraySearch($arrInArray,$varSearchValue){
	foreach ($arrInArray as $key => $row){
		$row = array_map(strtolower,$row);
		$ergebnis = in_array(strtolower($varSearchValue),$row);
		if ($ergebnis){
			return $row[1];
		}
	}
}//end of multiArraySearch

public  function getPayslipSummary($month,$year){
	//return array details  array()
	$summary = array();
	$staffid = $this->getUserDetails();
	//select  pre payment
	$stmt = $this->sql->Execute($this->sql->Prepare("SELECT BANK_BRANCH_NAME,NET_AMOUNT FROM ALL_PRE_PAYMENTS WHERE EMPLOYEE_NUMBER=".$this->sql->Param('a')." AND MONTH(PAY_PERIOD) =".$this->sql->Param('b')." AND YEAR(PAY_PERIOD) =".$this->sql->Param('c')." AND NET_AMOUNT !='0' "),array($staffid->SA_EMPLOYEE_ID,$month,$year));
	$obj = $stmt->FetchNextObject();
	$summary[8] = $obj->BANK_BRANCH_NAME;
	$summary[9] = $obj->NET_AMOUNT;
	
	// SELECT ANNUAL SALARY
	//ANNUAL SALARY
	$stmt = $this->sql->Execute($this->sql->Prepare("SELECT ANNUAL_SALARY FROM ALL_PEOPLE_INFO WHERE EMPLOYEE_NUMBER=".$this->sql->Param('a')." AND MONTH(PAY_PERIOD) =".$this->sql->Param('b')." AND YEAR(PAY_PERIOD) =".$this->sql->Param('c')." AND (ANNUAL_SALARY !=''OR ANNUAL_SALARY ='0') "),array($staffid->SA_EMPLOYEE_ID,$month,$year));
	$obj = $stmt->FetchNextObject();
	$summary[0] = $obj->ANNUAL_SALARY;
	
	//select latest balance
	$stmt = $this->sql->Execute($this->sql->Prepare("SELECT BALANCE_NAME_AND_SUFFIX,AMOUNT FROM ALL_LATEST_BAL WHERE EMPLOYEE_NUMBER=".$this->sql->Param('a')." AND MONTH(PAY_PERIOD) =".$this->sql->Param('b')." AND YEAR(PAY_PERIOD) =".$this->sql->Param('c')." "),array($staffid->SA_EMPLOYEE_ID,$month,$year));
	$objin = $stmt->GetRows();

	//YTD GROSS PAY
	$search = $this->multiArraySearch($objin,"Total Earnings_ASG_YTD");
	$summary[1] = $search;
	//MONTHLY GROSS PAY
	$search = $this->multiArraySearch($objin,"Total Earnings_ASG_RUN");
	$summary[2] = $search;
	//YTD S.S.F (WORKER)
	$search = $this->multiArraySearch($objin,"SS Employee_ASG_YTD");
	$summary[3] = $search;
	//YTD INCOME TAX
	$search = $this->multiArraySearch($objin,"Income Tax Bal_ASG_YTD");
	$summary[4] = $search;
	//EMPLOYER S.S.F
	$search = $this->multiArraySearch($objin,"Monthly Salary_ASG_RUN");
	//$summary[5] = $search;
	$summary[5] = '';
	//MONTHLY
	$search = $this->multiArraySearch($objin,"SS Employer_ASG_RUN");
	$summary[6] = $search;
	//YTD
	$search = $this->multiArraySearch($objin,"SS Employer_ASG_YTD");
	$summary[7] = $search;
	
	return $summary;
}

public function getUserDetails(){
    $stmt = $this->sql->Prepare("SELECT * FROM payslipmembers WHERE SA_STATE='1' AND SA_ID =".$this->sql->Param('a'));
     $staffid = $this->session->get("pyuserid");
     $stmt = $this->sql->Execute($stmt,array($staffid));
    //print $this->sql->ErrorMsg();
    return $stmt->FetchNextObject();

}

public function getCurrentPayMonth(){
	$query = "SELECT * FROM published_payslip WHERE PP_STATUS='1' ORDER BY PP_YEAR,PP_MONTH DESC LIMIT 1";
	$stmt = $this->sql->Execute($this->sql->Prepare($query));
	$obj = $stmt->FetchNextObject();
    return array($obj->PP_MONTH,$obj->PP_YEAR);
}

public function getModel(){
    $query = "SELECT PM_DISTRICT FROM sortedpayment WHERE PM_STAFFNEW_ID=".$this->sql->Param('a')." ORDER BY PM_ID DESC LIMIT 1";
    $staffid = $this->getUserDetails();
    $stmt = $this->sql->Prepare($query);
    $stmt = $this->sql->Execute($stmt,array($staffid->SA_NEW_STAFF_ID));
    //print $this->sql->ErrorMsg();
   $obj = $stmt->FetchNextObject();

    //select district id
    $query = "SELECT DISTRICT_ID FROM districts WHERE DISTRICT_NAME =".$this->sql->Param('a');
    $stmt = $this->sql->Prepare($query);
    $stmt = $this->sql->Execute($stmt,array($obj->PM_DISTRICT.' District'));

    if($stmt->RecordCount() > 0){
    $obj = $stmt->FetchNextObject();
    return $obj->DISTRICT_ID;
    }else{
        return "0";
    }
}

}
?>
