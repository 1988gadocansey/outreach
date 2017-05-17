<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if(function_exists('mcrypt_encrypt'))
{
	function RC4($key, $data)
	{
		return mcrypt_encrypt(MCRYPT_ARCFOUR, $key, $data, MCRYPT_MODE_STREAM, '');
	}
}
else
{
	function RC4($key, $data)
	{
		static $last_key, $last_state;

		if($key != $last_key)
		{
			$k = str_repeat($key, 256/strlen($key)+1);
			$state = range(0, 255);
			$j = 0;
			for ($i=0; $i<256; $i++){
				$t = $state[$i];
				$j = ($j + $t + ord($k[$i])) % 256;
				$state[$i] = $state[$j];
				$state[$j] = $t;
			}
			$last_key = $key;
			$last_state = $state;
		}
		else
			$state = $last_state;

		$len = strlen($data);
		$a = 0;
		$b = 0;
		$out = '';
		for ($i=0; $i<$len; $i++){
			$a = ($a+1) % 256;
			$t = $state[$a];
			$b = ($b+$t) % 256;
			$state[$a] = $state[$b];
			$state[$b] = $t;
			$k = $state[($state[$a]+$state[$b]) % 256];
			$out .= chr(ord($data[$i]) ^ $k);
		}
		return $out;
	}
}


/**
 * Description of pdfclass
 *
 * @author orcons systems
 */
class pdfclass extends FPDF{
	var $encrypted = false;  //whether document is protected
	var $Uvalue;             //U entry in pdf document
	var $Ovalue;             //O entry in pdf document
	var $Pvalue;             //P entry in pdf document
	var $enc_obj_id;         //encryption object id
	
	//payslip employee details
	var $date,$datefrom,$dateto,$name,$region="",$district="",$agency="",$staffid="",$staffidold="XXXXXXX",$ssfno="",$department="",$grade="",$unit="",$cost="",$level="",$point="",$scale="";
   
    //put your code here
    function  __construct() {
        parent::__construct('L');
                
    }
    
    /**
     * Function to set permissions as well as user and owner passwords
     *
     * - permissions is an array with values taken from the following list:
     *   copy, print, modify, annot-forms
     *   If a value is present it means that the permission is granted
     * - If a user password is set, user will be prompted before document is opened
     * - If an owner password is set, document can be opened in privilege mode with no
     *   restriction if that password is entered
     */
    function SetProtection($permissions=array(), $user_pass='', $owner_pass=null)
    {
    	$options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32 );
    	$protection = 192;
    	foreach($permissions as $permission)
    	{
    		if (!isset($options[$permission]))
    			$this->Error('Incorrect permission: '.$permission);
    		$protection += $options[$permission];
    	}
    	if ($owner_pass === null)
    		$owner_pass = uniqid(rand());
    	$this->encrypted = true;
    	$this->padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
    			"\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
    	$this->_generateencryptionkey($user_pass, $owner_pass, $protection);
    }
    
    /****************************************************************************
     *                                                                           *
    *                              Private methods                              *
    *                                                                           *
    ****************************************************************************/
    
    function _putstream($s)
    {
    	if ($this->encrypted) {
    		$s = RC4($this->_objectkey($this->n), $s);
    	}
    	parent::_putstream($s);
    }
    
    function _textstring($s)
    {
    	if ($this->encrypted) {
    		$s = RC4($this->_objectkey($this->n), $s);
    	}
    	return parent::_textstring($s);
    }
    
    /**
     * Compute key depending on object number where the encrypted data is stored
     */
    function _objectkey($n)
    {
    	return substr($this->_md5_16($this->encryption_key.pack('VXxx',$n)),0,10);
    }
    
    function _putresources()
    {
    	parent::_putresources();
    	if ($this->encrypted) {
    		$this->_newobj();
    		$this->enc_obj_id = $this->n;
    		$this->_out('<<');
    		$this->_putencryption();
    		$this->_out('>>');
    		$this->_out('endobj');
    	}
    }
    
    function _putencryption()
    {
    	$this->_out('/Filter /Standard');
    	$this->_out('/V 1');
    	$this->_out('/R 2');
    	$this->_out('/O ('.$this->_escape($this->Ovalue).')');
    	$this->_out('/U ('.$this->_escape($this->Uvalue).')');
    	$this->_out('/P '.$this->Pvalue);
    }
    
    function _puttrailer()
    {
    	parent::_puttrailer();
    	if ($this->encrypted) {
    		$this->_out('/Encrypt '.$this->enc_obj_id.' 0 R');
    		$this->_out('/ID [()()]');
    	}
    }
    
    /**
     * Get MD5 as binary string
     */
    function _md5_16($string)
    {
    	return pack('H*',md5($string));
    }
    
    /**
     * Compute O value
     */
    function _Ovalue($user_pass, $owner_pass)
    {
    	$tmp = $this->_md5_16($owner_pass);
    	$owner_RC4_key = substr($tmp,0,5);
    	return RC4($owner_RC4_key, $user_pass);
    }
    
    /**
     * Compute U value
     */
    function _Uvalue()
    {
    	return RC4($this->encryption_key, $this->padding);
    }
    
    /**
     * Compute encryption key
     */
    function _generateencryptionkey($user_pass, $owner_pass, $protection)
    {
    	// Pad passwords
    	$user_pass = substr($user_pass.$this->padding,0,32);
    	$owner_pass = substr($owner_pass.$this->padding,0,32);
    	// Compute O value
    	$this->Ovalue = $this->_Ovalue($user_pass,$owner_pass);
    	// Compute encyption key
    	$tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
    	$this->encryption_key = substr($tmp,0,5);
    	// Compute U value
    	$this->Uvalue = $this->_Uvalue();
    	// Compute P value
    	$this->Pvalue = -(($protection^255)+1);
    }
    
  public function  Header(){
    //Put watermark
    if(file_exists('../../../library/payVouchersLogo2_smaller.jpg')){
	$this->Image('../../../library/payVouchersLogo2_smaller.jpg',90,60,100);  //put in coat of arms
    }
	$this->SetFont('Arial', '',10);
	
//employee details
	//Column widths
	$w=array(25,97,25,100);
	//work on job as grade in here
	$strholder = $this->grade;
	if(strlen($this->grade)>24){
		$strholder = substr($this->grade,0,40);
	}
	$this->Cell(32,6,"CONTROLLER",'TLR');
	$this->Cell($w[0],6,"DATE",'1');
	$this->Cell($w[1],6,strtoupper($this->date),'1');
	$this->Cell($w[2],6,"REGION",'1');
	$this->Cell($w[3],6,$this->region,'1');
	$this->Ln();
	
	$this->Cell(32,6,"AND",'LR');
	$this->Cell($w[0],6,"PERIOD",'LRB');
	$this->Cell($w[1],6,"FROM     ".strtoupper(date('01-M-y',strtotime($this->datefrom)))."       TO  ".strtoupper(date('d-M-y',strtotime($this->dateto))),'LRB'); //evaluate the no of days in a month
	$this->Cell($w[2],6,"DISTRICT",'LRB');
	$this->Cell($w[3],6,$this->district,'LRB');
	$this->Ln();
	
	$this->Cell(32,6,"ACCOUNTANT",'LR');
	$this->Cell($w[0],6,"NAME",'LRB');
	$this->Cell($w[1],6, $this->name,'LRB');
	$this->Cell($w[2],6,"AGENCY",'LRB');
	$this->Cell($w[3],6,$this->agency,'LRB');  //need to include agency in db
	$this->Ln();
	
	$this->Cell(32,6,"GENERAL'S",'LR');
	$this->Cell($w[0],6,"STAFF ID",'LRB');
	$this->Cell($w[1],6, $this->staffid.'   '.$this->staffidold.'   S.S.F. No  '.$this->ssfno,'LRB');
	$this->Cell($w[2],6,"DEPT.",'LRB');
	$this->Cell($w[3],6,$this->department,'LRB');
	$this->Ln();
	
	$this->Cell(32,6,"DEPARTMENT",'LR');
	$this->Cell($w[0],6,"GRADE",'LRB');
	$this->Cell($w[1],6, $strholder,'LRB');
	$this->Cell($w[2],6,"UNIT",'LRB');		//cost center is included after this
	$this->Cell($w[3],6,$this->unit."  COST CENTRE   ".$this->cost,'LRB');
	$this->Ln();
	
	$this->Cell(32,6,"",'LRB');
	$this->Cell($w[0],6,"LEVEL",'LRB');
	$this->Cell($w[1],6, ereg_replace("[^0-9]", "",$this->level)."          POINT  ".$this->point."             SCALE TYPE   ".$this->scale,'LRB');
	$this->Cell($w[2],6,"",'LB');
	$this->Cell($w[3],6,"",'LRB');
	$this->Ln();
	//place this as an image
	
	$w=array(25,16,22,84,45,36,23,28);
	$this->Cell($w[0],6,"MONTH/YEAR",'1');
	$this->Cell($w[1],6,"NATURE",'1');
	$this->Cell($w[2],6,"ITEM CODE",'1');
	$this->Cell($w[3],6,"DESCRIPTION",'1');
	$this->Cell($w[4],6,"HRS/ORIGINAL AMOUNT",'1');
	$this->Cell($w[5],6,"RATE(%) BALANCE",'1');
	$this->Cell($w[6],6,"PAYMENTS",'1');
	$this->Cell($w[7],6,"DEDUCTIONS",'1');
	$this->Ln();
}

function Footer(){
    //Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}//end Footer

function PeopleData($date,$datefrom,$dateto,$name,$region="",$district="",$agency="",$staffid="",$staffidold="XXXXXXX",$ssfno="",$department="",$grade="",$unit="",$cost="",$level="",$point="",$scale=""){
    //Column widths
    $w=array(25,97,25,100);
    //work on job as grade in here
    $strholder = $grade;
    if(strlen($grade)>24){
    $strholder = substr($grade,0,40);
    }
	$this->Cell(32,6,"CONTROLLER",'TLR');
	$this->Cell($w[0],6,"DATE",'1');
	$this->Cell($w[1],6,strtoupper($date),'1');
	$this->Cell($w[2],6,"REGION",'1');
	$this->Cell($w[3],6,$region,'1');
	$this->Ln();

	$this->Cell(32,6,"AND",'LR');
	$this->Cell($w[0],6,"PERIOD",'LRB');
	$this->Cell($w[1],6,"FROM     ".strtoupper(date('01-M-y',strtotime($datefrom)))."       TO  ".strtoupper(date('d-M-y',strtotime($dateto))),'LRB'); //evaluate the no of days in a month
	$this->Cell($w[2],6,"DISTRICT",'LRB');
	$this->Cell($w[3],6,$district,'LRB');
	$this->Ln();

	$this->Cell(32,6,"ACCOUNTANT",'LR');
        $this->Cell($w[0],6,"NAME",'LRB');
	$this->Cell($w[1],6, $name,'LRB');
	$this->Cell($w[2],6,"AGENCY",'LRB');
	$this->Cell($w[3],6,$agency,'LRB');  //need to include agency in db
	$this->Ln();

	$this->Cell(32,6,"GENERAL'S",'LR');
	$this->Cell($w[0],6,"STAFF ID",'LRB');
	$this->Cell($w[1],6, $staffid.'   '.$staffidold.'   S.S.F. No  '.$ssfno,'LRB');
	$this->Cell($w[2],6,"DEPT.",'LRB');
	$this->Cell($w[3],6,$department,'LRB');
	$this->Ln();

	$this->Cell(32,6,"DEPARTMENT",'LR');
	$this->Cell($w[0],6,"GRADE",'LRB');
	$this->Cell($w[1],6, $strholder,'LRB');
	$this->Cell($w[2],6,"UNIT",'LRB');		//cost center is included after this
	$this->Cell($w[3],6,$unit."  COST CENTRE   ".$cost,'LRB');
	$this->Ln();

	$this->Cell(32,6,"",'LRB');
	$this->Cell($w[0],6,"LEVEL",'LRB');
	$this->Cell($w[1],6, ereg_replace("[^0-9]", "",$level)."          POINT  ".$point."             SCALE TYPE   ".$scale,'LRB');
	$this->Cell($w[2],6,"",'LB');
	$this->Cell($w[3],6,"",'LRB');
	$this->Ln();
	
	

    //Closure line
	//$this->Ln();
   //$this->Cell(array_sum($w),0,'','T');
}//end

function DetailTable($transactionarray){
$totalpayment=0;
$totaldedamt=0;
    //Column widths

    $w=array(25,16,22,84,45,36,23,28);
	
        



        

        $this->Cell($w[0],6,"",'LR');
		$this->Cell($w[1],6,"",'LR');
		$this->Cell($w[2],6,"",'LR');
		$this->Cell($w[3],6,"",'LR');
		$this->Cell($w[4],6,"",'LR');
		$this->Cell($w[5],6,"",'LR');
		$this->Cell($w[6],6,"",'LR');
		$this->Cell($w[7],6,"",'LR');
		$this->Ln();
		
		
        //payment report
		$effectcount =1;
        if(count($transactionarray) > 0){
        	foreach($transactionarray as $value){
        $this->Cell($w[0],6,date("d-M-y",strtotime($value[0])),'LR');
		$this->Cell($w[1],6,"",'LR');
		$this->Cell($w[2],6,"",'LR');
		$this->Cell($w[3],6,substr($value[1],0,49),'LR');
		$this->Cell($w[4],6,"",'LR');
		$this->Cell($w[5],6,(($value[2] !="")?number_format($value[2],2):''),'LR');
		$this->Cell($w[6],6,(($value[3]!="")?number_format($value[3],2):''),'LR','0','R');
		$this->Cell($w[7],6,(($value[4]!="")?number_format($value[4],2):''),'LR');
		$this->Ln();
		$totalpayment = $totalpayment +$value[3];
		$totaldedamt = $totaldedamt +$value[4];
		$effectcount ++;
        	}
        }
        
     
        
        //This part can be deleted if it over flows
        $mod = $effectcount % 22;
		//echo $effectcount;
		//exit;
        if($mod >=0){
			$pointer =18- $mod;        
        if($pointer > 0){
        	for($j=1;$j<$pointer;$j++){
        		$this->Cell($w[0],6,"",'LR');
        		$this->Cell($w[1],6,"",'LR');
        		$this->Cell($w[2],6,"",'LR');
        		$this->Cell($w[3],6,"",'LR');
        		$this->Cell($w[4],6,"",'LR');
        		$this->Cell($w[5],6,"",'LR');
        		$this->Cell($w[6],6,"",'LR');
        		$this->Cell($w[7],6,"",'LR');
        		$this->Ln();
        	}
        
        }
       }
	/**
        //arrears report
        if($arrears !="" && $arrears !=0){
                $this->Cell($w[0],6,"",'LR');
		$this->Cell($w[1],6,"",'LR');
		$this->Cell($w[2],6,"",'LR');
		$this->Cell($w[3],6,$arrearsstr,'LR');
		$this->Cell($w[4],6,"",'LR');
		$this->Cell($w[5],6,"",'LR');
		$this->Cell($w[6],6,number_format($arrears,2),'LR','0','R');
		$this->Cell($w[7],6,"",'LR');
		$this->Ln();
        }
                $this->Cell($w[0],6,"",'LR');
		$this->Cell($w[1],6,"",'LR');
		$this->Cell($w[2],6,"",'LR');
		$this->Cell($w[3],6,"",'LR');
		$this->Cell($w[4],6,"",'LR');
		$this->Cell($w[5],6,"",'LR');
		$this->Cell($w[6],6,"",'LR');
		$this->Cell($w[7],6,"",'LR');
		$this->Ln();
        

	//deduction loopings
        $loopded = explode(";",$deductions);
        $loopratebal = explode(";",$ratebal);
        $loopdedamt = explode(";",$dedamt);
	
        
	//generating blank rows
        $effectcount =count($loopded);
	for($i=0;$i<$effectcount;$i++){
		$this->Cell($w[0],6,"",'LR');
		$this->Cell($w[1],6,"",'LR');
		$this->Cell($w[2],6,"",'LR');
		$this->Cell($w[3],6,$loopded[$i],'LR');
		$this->Cell($w[4],6,"",'LR');
		$this->Cell($w[5],6,$loopratebal[$i],'LR','0','R');
		$this->Cell($w[6],6,"",'LR');
		$this->Cell($w[7],6,number_format($loopdedamt[$i],2),'LR','0','R');
		$this->Ln();
	}

      
            */
	//do totals
	$this->Cell($w[0],6,"",'LR');
	$this->Cell($w[1],6,"",'LR');
	$this->Cell($w[2],6,"",'LR');
	$this->Cell($w[3],6,"",'LR');
	$this->Cell($w[4],6,"",'LR');
	$this->Cell($w[5],6,"TOTALS",'1');
	$this->Cell($w[6],6,number_format($totalpayment,2),'1','0','R');
	$this->Cell($w[7],6,number_format($totaldedamt,2),'1','0','R');
	$this->Ln();

	$this->Cell(array_sum($w),0,'','T');
	$this->Ln();

}

function SummaryTable($summary)
{
$w=array(41,38,38,28,20,30,56,28);


    //Column widths
	$this->Cell($w[0],6,"ANNUAL SALARY",'LRB');
	$this->Cell($w[1],6,number_format($summary[0],2),'1','0','R');
	$this->Cell($w[2],6,"YTD SSF (WORKER)",'LRB');
	$this->Cell($w[3],6,number_format($summary[3],2),'1','0','R');
	$this->Cell($w[4],6,"EMPLOYER S.S.F. ",'LB');
        $this->Cell($w[5],6,$summary[5],'RB','0','R');
	$this->Cell($w[6],6,"PAYMENT MODE",'LRB');
	$this->Cell($w[7],6,"NET SALARY",'1');
	$this->Ln();

        $paymentmode = explode(",",$summary[8]);
        $paymentmode[0]  = (count($paymentmode)>1)? $paymentmode[0].',':$paymentmode[0] ;
	$this->Cell($w[0],6,"YTD GROSS PAY",'LRB');
	$this->Cell($w[1],6,number_format($summary[1],2),'1','0','R');
	$this->Cell($w[2],6,"YTD INCOME TAX",'LRB');
	$this->Cell($w[3],6,number_format($summary[4],2),'1','0','R');
	$this->Cell($w[4],6,"MONTHLY ",'LB');
        $this->Cell($w[5],6,number_format($summary[6],2),'RB','0','R');
	$this->Cell($w[6],6,$paymentmode[0],'LR');  //the bank   //MultiCell
	$this->Cell($w[7],6,"",'LR');  //part of net slaray column
	$this->Ln();

	$this->Cell($w[0],6,"MONTHLY GROSS PAY",'LRB');
	$this->Cell($w[1],6,number_format($summary[2],2),'LRB','0','R');
	$this->Cell($w[2],6,"PAGE",'LRB');
	$this->Cell($w[3],6, $this->PageNO(),'LRB','0','R');
	$this->Cell($w[4],6,"YTD   ",'LB');
    $this->Cell($w[5],6,number_format($summary[7],2),'RB','0','R');
	$this->Cell($w[6],6,$paymentmode[1],'LRB');
	$this->Cell($w[7],6,number_format($summary[9],2),'LRB','0','R');
	$this->Ln();
	

}//summary table

function DetermineLastPayslip($theStaffID){
	global $peopleID;
	
}//last pay slip


function firstOfMonth($date) {
return date("d-M-y", strtotime($date));
}

function lastOfMonth($date) {
return date("d-M-y", strtotime('-1 second',strtotime('+1 month',strtotime($date))));
}


}
?>
