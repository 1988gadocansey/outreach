<?php 
error_reporting(0);
include "config.php";


 if(isset($_GET['action'])=="login"){
     
    $username=stripslashes(strip_tags($_POST['username']));

     
    $msg = array();
    $session->set("sniper",$sniper);

    if(empty($username)){
    $msg[] = "Please enter your admissions number.";
     }
     $query = "SELECT * FROM outreach WHERE admitted=1 AND    applicationNumber ='$username' ";
           // print_r($query);
             
       
            $stmt = $sql->Prepare($query);

            $stmt = $sql->Execute($stmt);
             
            $sql->ErrorMsg();
            
            if($stmt){      

                if($stmt->RecordCount() > 0){
                     $row = $stmt->FetchRow();
                     @$session->set("applicant",$row['applicationNumber']);
                     @$session->set("name",$row['name']);       
                        
                     @header("location:letter.php");
                 }

                
                else{
                    $msg[]="Admission Letter not ready try again later";
                } 
  
            }


}
 


?>
<!doctype html>
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no"/>
     
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32">

    <title>Takoradi Technical University | Admissions</title>

    

    <!-- uikit -->
    <link rel="stylesheet" href="public/plugins/uikit/css/uikit.almost-flat.min.css"/>

    <!-- altair admin login page -->
    <link rel="stylesheet" href="public/css/login_page.min.css" />

</head>
<body class="login_page">

    <div class="login_page_wrapper">
         <!-- if there are login errors, show them here -->
    <?php if (count($msg) > 0){
?>
                <div class="uk-form-row">
                    <div class="alert alert-danger" style="background-color: red;color: white">
                       
                          <?php print_r($msg[0])?>
                </div>
              </div>
            <?php }?>
             <div class="login_heading">
                    <img src="public/img/logo.png"class="thumbnail" style="width:120px;height: auto"/>
                </div>
            <center><h5 class="uk-text-primary uk-text-upper uk-text-large">TTU ADMISSIONS - OUTREACH SECTION</h5></center>
        <div class="md-card" id="login_card">
            <div class="md-card-content large-padding" id="login_form">
               
                <form action="index.php?action=login&pg=letter" method="Post">
                    <div class="uk-form-row">
                        <label for="login_username">Admission Number</label>
                        <input class="md-input" type="text"  required="" id="username" name="username"   />
                    
                    </div>
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                    
                    <div class="uk-margin-medium-top">
                        <button type="submit"  class="md-btn md-btn-primary md-btn-block md-btn-large">Login </button>
                    </div>
                    <div class="uk-margin-top">
                         
                        <span class="icheck-inline">
                            <input type="checkbox" name="remember" id="login_page_stay_signed" data-md-icheck />
                            <label for="login_page_stay_signed" class="inline-label">Stay signed in</label>
                        </span>
                    </div>
                </form>
            </div>
             
             
        </div>
            <p style=""align='center' class="uk-text-small">  <a style="text-decoration: none" href='http://www.ttu.edu.gh' target='_'>&copy<?php echo date("Y")?> | Takoradi Technical University. All Rights Reserved</a> </p>
    </div>

    <!-- common functions -->
    <script src="public/js/common.min.js"></script>
    <!-- altair core functions -->
    <script src="public/js/altair_admin_common.min.js"></script>

    <!-- altair login page functions -->
    <script src="public/js/pages/login.min.js"></script>
    <script>
      
    </script>
</body>

 </html>