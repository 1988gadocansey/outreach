<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formatingclass
 *
 * @author orcons systems
 */
class formatingclass {
    //put your code here
    public function validateEmail($email){
		return preg_match("/^[-+\\.0-9=a-z_]+@([-0-9a-z]+\\.)+([0-9a-z]){2,4}$/i",$email);
	}

	public function  validatePhoneNumber($phone){
		return preg_match("/^([0-9])+$/i", $phone);
	//return preg_match("/^([0-9]{3}-)?[0-9]{3}-[0-9]{3}-[0-9]{3}$/i", $phone);
	}
	
/**
 *
 * @param <type> $inputarray
 * @return <type> 1 if no error and error message if error.
 */
    public function validateInput($inputarray){
        $returnstr="";
        if(is_array($inputarray)){
            foreach($inputarray as $pointer=>$holdings){
            foreach($holdings as $value => $condiction){
                if($condiction == 'R'){
                    $returnstr.=(empty($value))? $pointer.' is required. ':"";
                }else if($condiction == 'N'){
                    $returnstr.=(is_nan($value))? $pointer.' must be a number. ':"";
                }else if($condiction == 'RN'){
                     $returnstr.=(empty($value) && is_nan($value))? $pointer.' is required and must be a number. ':"";
                }else if($condiction == 'E'){
                    $returnstr.=(!validateEmail($value))? $pointer.' is not a valid email address. ':"";
                    } 
                    
                }
                if($pointer == 'PC'){
                       $returnstr.=($holdings[0] != $holdings[1])? 'Password comfirmation is wrong. ':"";
                    }
            }
            return (count($returnstr) > 0)? $returnstr : 1;
        }else{
            return "Invalid input";
        }
    }// end of validateInput
    
    
    public function autopassword($len = 6){
    	return substr(md5(rand().rand()), 0, $len);
    }//end


   
}
?>
