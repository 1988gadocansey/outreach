<?php

class Applicant {

    private $config;
    private $session;
    private $connect;
    public function __construct() {
         
        global $config;global $sql,$session;
        $this->config = $config;
        $this->session	=$session;
	$this->connect = $sql;
        
    }
    public function getApplicant($applicant) {
        			
			$query = "SELECT * FROM outreach WHERE admitted='1' AND  applicationNumber =".$this->connect->Param('a')."";
			print_r($query);
                         
			$stmt = $this->connect->Prepare($query);
			$stmt = $this->connect->Execute($stmt,array($applicant));
			return $stmt->FetchNextObject(); 
    }
    public function getFaculty($program) {
        $query = "SELECT * FROM tpoly_programme AS p LEFT JOIN tpoly_department AS d ON p.DEPTCODE=d.DEPTCODE LEFT JOIN tpoly_faculty AS f ON d.FACCODE=f.FACCODE WHERE  p.PROGRAMMECODE =".$this->connect->Param('a')."";
        
			$stmt = $this->connect->Prepare($query);
			$stmt = $this->connect->Execute($stmt,array($program));
			$data=$stmt->FetchNextObject(); 
                       
                      return  $data;
    }
    public function getAdmittedProgram($program) {
        $query = "SELECT PROGRAMME FROM tpoly_programme AS p LEFT JOIN tpoly_department AS d ON p.DEPTCODE=d.DEPTCODE LEFT JOIN tpoly_faculty AS f ON d.FACCODE=f.FACCODE WHERE  p.PROGRAMMECODE =".$this->connect->Param('a')."";
        
			$stmt = $this->connect->Prepare($query);
			$stmt = $this->connect->Execute($stmt,array($program));
			$data=$stmt->FetchNextObject(); 
                       
                      return  $data->PROGRAMME;
    }
  public function hallData($hall) {
          $info = \DB::table('tpoly_hall')->where("HALL_NAME",$hall)->first();
             
         return $info;
         
              
    }
    public function hallAccount($hall) {
          $info = \DB::table('tpoly_hall')->where("HALL_NAME",$hall)->first();
             
         return $info->ACCOUNTNUMBER;
              
    }
    public function hallFees($hall) {
          $info = \DB::table('tpoly_hall')->where("HALL_NAME",$hall)->first();
             
         return $info->AMOUNT;
              
    }
     public function getDepartmentName($deptCode){
        
        $department = \DB::table('tpoly_department')->where('DEPTCODE',$deptCode)->get();
                 
        return @$department[0]->DEPARTMENT;
     
    }
    
    
    public function getSchoolCode($dept){
        
        $school = \DB::table('tpoly_department')->where('DEPTCODE',$dept)->get();
                 
        return @$school[0]->FACCODE;
     
    }
    public function getProgramName($code){
        
        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$code)->get();
                 
        return @$programme[0]->PROGRAMME;
     
    }
}
