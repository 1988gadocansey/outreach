<?php
error_reporting(0);
include "config.php";
if(!isset($_SESSION['applicant'])){
     header("location:logout.php?auth=false&salt=empty");
}
 
$appClass = new Applicant();
$applicant = $session->get('applicant');
$query = "SELECT * FROM outreach WHERE admitted=1 and applicationNumber='$applicant'";
//print_r($query);
$stmt = $sql->Prepare($query);

$stmt = $sql->Execute($stmt);

$data = $stmt->FetchNextObject();
//print_r($data->TYPE);
$info = @$appClass->getFaculty($data->PROGRAMMEADMITTED);
$bankInfo = new Bank();
$calender = new Calender();
$hall = new Hall();
$calenderInfo = $calender->getCalender();
$bankInfo = @$bankInfo->getBank($info->BANK);
$hallInfo = @$hall->getHall($data->HALLADMITTED);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Takoradi Technical University - Admissions</title> 
        <link href="media/css/style.css" rel="stylesheet" type="text/css" />
        <style>
            @page {
                size: A4;
            }
            body{
                background-image:url("{{url('public/assets/img/background.jpgs') }}");
                background-repeat: no-repeat;
                background-attachment: fixed;
                line-height:1.5;
            }
            .watermark {

                position:absolute;
                overflow:hidden;
            }

            .watermark::after {
                content: "";
                background:url(http://srms.tpolyonline.com/public/logins/images/logo.png);
                opacity: 0.2;
                top: 0;
                left: 30;
                bottom: 0;
                right: 0;
                position: absolute;
                z-index: -1;   
                background-size: contain;
                content: " ";
                display: block;
                position: absolute;
                height: 100%;
                width: 100%;
                background-repeat: no-repeat;
            }
            @media print {
                .watermark {
                    display: block;
                    table {float: none !important; }
                    div { float: none !important; }
                }
                .uk-grid, to {display: inline !important} s
                #page1  {page-break-before:always;}
                .condition  {page-break-before:always;}
                #page2  {page-break-before:always;}
                .school {page-break-before:always;}
                .page9  {page-break-inside:avoid; page-break-after:auto}
                a,
                a:visited {
                    text-decoration: underline;
                }
                body{font-size: 14px}
                size:A4;
                a[href]:after {
                    content: " (" attr(href) ")";
                }

                abbr[title]:after {
                    content: " (" attr(title) ")";
                }


                a[href^="javascript:"]:after,
                a[href^="#"]:after {
                    content: "";
                }
                .uk-grid, to {display: inline !important}

            }
        </style>
        <script>
            $(document).ready(function () {
                // Wrap each tr and td's content within a div
                // (todo: add logic so we only do this when printing)
                $("table tbody th, table tbody td").wrapInner("<div></div>");
            })
        </script>
        <script language="javascript" type="text/javascript">
            function printDiv(divID) {
                //Get the HTML of div
                var divElements = document.getElementById(divID).innerHTML;
                //Get the HTML of whole page
                var oldPage = document.body.innerHTML;

                //Reset the page's HTML with div's HTML only
                document.body.innerHTML =
                        "<html><head><title></title></head><body>" +
                        divElements + "</body>";

                //Print Page
                window.print();

                //Restore orignal HTML
                document.body.innerHTML = oldPage;


            }
        </script>


    </head>
    <body>
        <div class="container">
            <div class="headerbox">
                <table>
                    <tr>
                        <td><div class="logoarea" style="margin-left:250px">
                                <i class="logo"><img src="media/img/logo.png" style="width:59px;height: auto;margin-left: 33px"/></i>
                    <div class="logotitle">Takoradi Technical University - Welcome <?php echo $session->get("name"); ?> </div>
                   
                       
                    
                            </div></td>
                        <td><div style="float:right;margin-left:21px;margin-top: 12px"><a href="logout.php">Logout</a></div></td>
                    </tr>
                </table>
            </div>
 
            <div  style="position:relative; min-height:100%">
                
                <div align="justify" class="letter">



                    <a  onclick="javascript:printDiv('print')" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave">Click to print form</a>
                    <div id='print'>

 
                        <table border='0'>
                            <tr>
                                <td> <img src='media/img/admissions.jpg' style="width:910px;height: auto"  class="image-responsive"/> 

                                  
                            </tr>
                        </table>

                        <?php if ($data->ADMISSIONTYPE == 'technical') { ?>

                            <div class="content" id="technical">  <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"><?php echo $data->NAME ?></span></p>

                                        <div style="margin-left: 0px;text-align: justify">
                                            <div id='page1'>
                                                <centerd><b><p class="">OFFER OF ADMISSION  - <?php echo strtoupper(@$info->FACULTY) ?>  -  ADMISSION N<u>O </u>:<?php echo $data->APPLICATIONNUMBER ?></p></b></center>
                                                    <hr>
                                                        <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of 
                                                            <b><?php echo @$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED); ?></b>. The duration of the programme is <?php echo $info->DURATION ?> Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                                        <p><b>Note A mandatory Preparatory course in Engineering Mathematics</b>  and <b> Engineering Science </b>will be organized for all applicants from Technical Institutions to build up their capacity in<b> Elective Mathematics required for HND programme. </b>The preparatory course starts from <b>Monday 24th July</b> and ends on <b>Friday18th August 2017</b>. You are therefore required to pay a <b>non-refundable special tuition fee of GH¢200</b> at any branch of <b>Capital Bank, into Accounts Number, 2220001961011</b>. There is an <b>option for accommodation on campus during the preparatory course at a fee of GH¢ 75 for interested individuals also to be paid into the same Bank Accounts above</b>.
                                                        </p>
                                                        <p>1. Your admission is for the <b> <?php echo $calenderInfo->YEAR ?></b> Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>

                                                        <p>2. The<b> <?php echo $calenderInfo->YEAR ?> academic year</b> is scheduled to begin on <b> Monday 28th August   <?php echo date('Y') ?></b>. You are expected to report for medical examination and registration from <b>Monday 28th August <?php echo date('Y') ?> to Friday 8th September <?php date('Y') ?></b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September <?php date('Y') ?></b>.</p>

                                                        <p>3. You are required to make full payment of <b>non-refundable fee </b> of <b>GHS <?php echo $data->ADMISSIONFEES ?></b> at any branch of
                                                            <?php if ($info->TYPE == "NON TERTIARY") { ?>
                                                                <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>
                                                            <?php } elseif (strpos(@$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED), "Evening") !== false) { ?>
                                                            <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } else { ?>
                                                            <b><?php echo strtoupper(@$bankInfo->NAME) ?> into Account Number <?php echo strtoupper(@$bankInfo->ACCOUNT_NUMBER) ?></b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } ?>
                                                        <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>

                                                        <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>

                                                        <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>

                                                        <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>

                                                        <p>8. You are to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is not feasible, lectures and examination would be held on other days.</p>


                                                        </div>
                                                        <div id='page2'>
                                                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
    <?php if ($data->RESIDENTIALSTATUS == 0) { ?>
                                                                <p>10. You are affiliated to <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall. </b></p>

                                                            <?php } else { ?>
                                                                <p>10. You have been given Hall Accommodation at <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall </b>. You will be required to make payment of  GHS  <?php echo $hallInfo->AMOUNT ?> into any branch of Zenith Bank Ghana with account number <?php echo $hallInfo->ACCOUNTNUMBER ?> . <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                                                                    NOTE: Hall fees paid is not refundable.
                                                                </p>
    <?php } ?>
                                                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                                                            <p>You are required to make full payment of all fees before or on Friday 30th June <?php echo date('Y') ?>. </p>

                                                            <p>Please, accept my congratulations on your admission to the University.</p>

                                                            <div>
                                                                <table>
                                                                    <tr>
                                                                        <td>
                                                                            <p>Yours faithfully</p>
                                                                            <p><img src='media/img/signature.png' style="width:90px;height:auto;" /></p>
                                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                                                                        </td>


                                                                    </tr>

                                                                </table>
                                                            </div>
                                                            <td> <img src='media/img/footer.jpg' style=""  class="image-responsive"/> 

                                                        </div>
                                        </div></div></div>

<?php
} elseif($data->ADMISSIONTYPE=="regular") {
    ?>
            <div class="content" id="regular">  <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"><?php echo $data->NAME ?></span></p>

                                        <div style="margin-left: 0px;text-align: justify">
                                            <div id='page1'>
                                                <centerd><b><p class="">OFFER OF ADMISSION  - <?php echo strtoupper(@$info->FACULTY) ?>  -  ADMISSION N<u>O </u>:<?php echo $data->APPLICATIONNUMBER ?></p></b></center>
                                                    <hr>
                                                        <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of 
                                                            <b><?php echo @$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED); ?></b>. The duration of the programme is <?php echo $info->DURATION ?> Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                                         
                                                        <p>1. Your admission is for the <b><?php echo $calenderInfo->YEAR ?></b> Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>

                                                        <p>2. The<b> <?php echo $calenderInfo->YEAR ?> academic year</b> is scheduled to begin on <b> Monday 28th August   <?php echo date('Y') ?></b>. You are expected to report for medical examination and registration from <b>Monday 28th August <?php echo date('Y') ?> to Friday 8th September <?php date('Y') ?></b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September <?php date('Y') ?></b>.</p>

                                                        <p>3. You are required to make full payment of <b>non-refundable fee </b> of <b>GHS <?php echo $data->ADMISSIONFEES ?></b> at any branch of
                                                            <?php if ($info->TYPE == "NON TERTIARY") { ?>
                                                                <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>
                                                            <?php } elseif (strpos(@$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED), "Evening") !== false) { ?>
                                                            <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } else { ?>
                                                            <b><?php echo strtoupper(@$bankInfo->NAME) ?> into Account Number <?php echo strtoupper(@$bankInfo->ACCOUNT_NUMBER) ?></b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } ?>
                                                        <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>

                                                        <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>

                                                        <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>

                                                        <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>

                                                        <p>8. You are to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is not feasible, lectures and examination would be held on other days.</p>


                                                        </div>
                                                        <div id='page2'>
                                                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
    <?php if ($data->RESIDENTIALSTATUS == 0) { ?>
                                                                <p>10. You are affiliated to <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall. </b></p>

                                                            <?php } else { ?>
                                                                <p>10. You have been given Hall Accommodation at <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall </b>. You will be required to make payment of  GHS  <?php echo $hallInfo->AMOUNT ?> into any branch of Zenith Bank Ghana with account number <?php echo $hallInfo->ACCOUNTNUMBER ?> . <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                                                                    NOTE: Hall fees paid is not refundable.
                                                                </p>
    <?php } ?>
                                                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                                                            <p>You are required to make full payment of all fees before or on Friday 30th June <?php echo date('Y') ?>. </p>

                                                            <p>Please, accept my congratulations on your admission to the University.</p>

                                                            <div>
                                                                <table>
                                                                    <tr>
                                                                        <td>
                                                                            <p>Yours faithfully</p>
                                                                            <p><img src='media/img/signature.png' style="width:90px;height:auto;" /></p>
                                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                                                                        </td>


                                                                    </tr>

                                                                </table>
                                                            </div>
                                                            <td> <img src='media/img/footer.jpg' style=""  class="image-responsive"/> 

                                                        </div>
                                        </div></div></div>         
                                
<?php
}elseif($data->ADMISSIONTYPE=="mature") {
?>
                
           <div class="content" id="mature">  <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"><?php echo $data->NAME ?></span></p>

                                        <div style="margin-left: 0px;text-align: justify">
                                            <div id='page1'>
                                                <centerd><b><p class="">OFFER OF ADMISSION  - <?php echo strtoupper(@$info->FACULTY) ?>  -  ADMISSION N<u>O </u>:<?php echo $data->APPLICATIONNUMBER ?></p></b></center>
                                                    <hr>
                                                        <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of 
                                                            <b><?php echo @$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED); ?></b>. The duration of the programme is <?php echo $info->DURATION ?> Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                                         
                                                        <p>1. Your admission is for the <b><?php echo $calenderInfo->YEAR ?></b> Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>

                                                        <p>2. The<b> <?php echo $calenderInfo->YEAR ?> academic year</b> is scheduled to begin on <b> Monday 28th August   <?php echo date('Y') ?></b>. You are expected to report for medical examination and registration from <b>Monday 28th August <?php echo date('Y') ?> to Friday 8th September <?php date('Y') ?></b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September <?php date('Y') ?></b>.</p>

                                                        <p>3. You are required to make full payment of <b>non-refundable fee </b> of <b>GHS <?php echo $data->ADMISSIONFEES ?></b> at any branch of
                                                            <?php if ($info->TYPE == "NON TERTIARY") { ?>
                                                                <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>
                                                            <?php } elseif (strpos(@$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED), "Evening") !== false) { ?>
                                                            <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } else { ?>
                                                            <b><?php echo strtoupper(@$bankInfo->NAME) ?> into Account Number <?php echo strtoupper(@$bankInfo->ACCOUNT_NUMBER) ?></b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } ?>
                                                        <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>

                                                        <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>

                                                        <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>

                                                        <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>

                                                        <p>8. You are to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is not feasible, lectures and examination would be held on other days.</p>


                                                        </div>
                                                        <div id='page2'>
                                                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
    <?php if ($data->RESIDENTIALSTATUS == 0) { ?>
                                                                <p>10. You are affiliated to <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall. </b></p>

                                                            <?php } else { ?>
                                                                <p>10. You have been given Hall Accommodation at <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall </b>. You will be required to make payment of  GHS  <?php echo $hallInfo->AMOUNT ?> into any branch of Zenith Bank Ghana with account number <?php echo $hallInfo->ACCOUNTNUMBER ?> . <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                                                                    NOTE: Hall fees paid is not refundable.
                                                                </p>
    <?php } ?>
                                                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                                                            <p>You are required to make full payment of all fees before or on Friday 30th June <?php echo date('Y') ?>. </p>

                                                            <p>Please, accept my congratulations on your admission to the University.</p>

                                                            <div>
                                                                <table>
                                                                    <tr>
                                                                        <td>
                                                                            <p>Yours faithfully</p>
                                                                            <p><img src='media/img/signature.png' style="width:90px;height:auto;" /></p>
                                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                                                                        </td>


                                                                    </tr>

                                                                </table>
                                                            </div>
                                                            <td> <img src='media/img/footer.jpg' style=""  class="image-responsive"/> 

                                                        </div>
                                        </div></div></div>            
                
<?php }elseif($data->ADMISSIONTYPE=="provisional") {?>
               
                <div class="content" id="provisional">  <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"><?php echo $data->NAME ?></span></p>

                                        <div style="margin-left: 0px;text-align: justify">
                                            <div id='page1'>
                                                <centerd><b><p class="">OFFER OF ADMISSION(<b>PROVISIONAL</b>)  - <?php echo strtoupper(@$info->FACULTY) ?>  -  ADMISSION N<u>O </u>:<?php echo $data->APPLICATIONNUMBER ?></p></b></center>
                                                    <hr>
                                                        <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of 
                                                            <b><?php echo @$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED); ?></b>. The duration of the programme is <?php echo $info->DURATION ?> Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                                           <p><b><i>Note: Your admission is <b>PROVISIONAL</b>, you are therefore, required to present your results to the university’s admissions office after it is published, to enable the office regularlised your admission.</b></i></p>
                              
                                                        <p>1. Your admission is for the <b><?php echo $calenderInfo->YEAR ?></b> Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>

                                                        <p>2. The<b> <?php echo $calenderInfo->YEAR ?> academic year</b> is scheduled to begin on <b> Monday 28th August   <?php echo date('Y') ?></b>. You are expected to report for medical examination and registration from <b>Monday 28th August <?php echo date('Y') ?> to Friday 8th September <?php date('Y') ?></b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September <?php date('Y') ?></b>.</p>

                                                        <p>3. You are required to make full payment of <b>non-refundable fee </b> of <b>GHS <?php echo $data->ADMISSIONFEES ?></b> at any branch of
                                                            <?php if ($info->TYPE == "NON TERTIARY") { ?>
                                                                <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>
                                                            <?php } elseif (strpos(@$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED), "Evening") !== false) { ?>
                                                            <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } else { ?>
                                                            <b><?php echo strtoupper(@$bankInfo->NAME) ?> into Account Number <?php echo strtoupper(@$bankInfo->ACCOUNT_NUMBER) ?></b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } ?>
                                                        <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>

                                                        <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>

                                                        <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>

                                                        <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>

                                                        <p>8. You are to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is not feasible, lectures and examination would be held on other days.</p>


                                                        </div>
                                                        <div id='page2'>
                                                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
    <?php if ($data->RESIDENTIALSTATUS == 0) { ?>
                                                                <p>10. You are affiliated to <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall. </b></p>

                                                            <?php } else { ?>
                                                                <p>10. You have been given Hall Accommodation at <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall </b>. You will be required to make payment of  GHS  <?php echo $hallInfo->AMOUNT ?> into any branch of Zenith Bank Ghana with account number <?php echo $hallInfo->ACCOUNTNUMBER ?> . <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                                                                    NOTE: Hall fees paid is not refundable.
                                                                </p>
    <?php } ?>
                                                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                                                            <p>You are required to make full payment of all fees before or on Friday 30th June <?php echo date('Y') ?>. </p>

                                                            <p>Please, accept my congratulations on your admission to the University.</p>

                                                            <div>
                                                                <table>
                                                                    <tr>
                                                                        <td>
                                                                            <p>Yours faithfully</p>
                                                                            <p><img src='media/img/signature.png' style="width:90px;height:auto;" /></p>
                                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                                                                        </td>


                                                                    </tr>

                                                                </table>
                                                            </div>
                                                            <td> <img src='media/img/footer.jpg' style=""  class="image-responsive"/> 

                                                        </div>
                                        </div></div></div>  
 <?php
}elseif($data->ADMISSIONTYPE=="conditional"){
 ?>
            <div class="content" id="conditional">  <div class="watermark">
                                    <div style="margin-left: 10px">
                                        <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"><?php echo $data->NAME ?></span></p>

                                        <div style="margin-left: 0px;text-align: justify">
                                            <div id='page1'>
                                                <centerd><b><p class="">OFFER OF ADMISSION(<b>CONDITIONAL</b>)  - <?php echo strtoupper(@$info->FACULTY) ?>  -  ADMISSION N<u>O </u>:<?php echo $data->APPLICATIONNUMBER ?></p></b></center>
                                                    <hr>
                                                        <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of 
                                                            <b><?php echo @$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED); ?></b>. The duration of the programme is <?php echo $info->DURATION ?> Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                                              <b><i> Note: Your admission is conditional. Per the new requirements you are supposed to have a minimum of D7 in six subjects with at least C6 in three relevant subjects in the area of specialization. You are therefore required to rewrite to make good the deficiencies within a period of one academic year. Your eligibility to continue with the HND programme would be based on the outcome of the SSCE/WASSCE result. You would be required to present your new results in writing to the DEPUTY REGISTRAR Academic affairs</i></b>

                                                        <p>1. Your admission is for the <b><?php echo $calenderInfo->YEAR ?></b> Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>

                                                        <p>2. The<b> <?php echo $calenderInfo->YEAR ?> academic year</b> is scheduled to begin on <b> Monday 28th August   <?php echo date('Y') ?></b>. You are expected to report for medical examination and registration from <b>Monday 28th August <?php echo date('Y') ?> to Friday 8th September <?php date('Y') ?></b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September <?php date('Y') ?></b>.</p>

                                                        <p>3. You are required to make full payment of <b>non-refundable fee </b> of <b>GHS <?php echo $data->ADMISSIONFEES ?></b> at any branch of
                                                            <?php if ($info->TYPE == "NON TERTIARY") { ?>
                                                                <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>
                                                            <?php } elseif (strpos(@$appClass->getAdmittedProgram($data->PROGRAMMEADMITTED), "Evening") !== false) { ?>
                                                            <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } else { ?>
                                                            <b><?php echo strtoupper(@$bankInfo->NAME) ?> into Account Number <?php echo strtoupper(@$bankInfo->ACCOUNT_NUMBER) ?></b>. If you do not indicate acceptance by paying the fees before <b> Friday 30th June,<?php echo date('Y') ?></b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make full payment of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year</b>.</p>

    <?php } ?>
                                                        <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>

                                                        <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>

                                                        <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>

                                                        <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>

                                                        <p>8. You are to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is not feasible, lectures and examination would be held on other days.</p>


                                                        </div>
                                                        <div id='page2'>
                                                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
    <?php if ($data->RESIDENTIALSTATUS == 0) { ?>
                                                                <p>10. You are affiliated to <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall. </b></p>

                                                            <?php } else { ?>
                                                                <p>10. You have been given Hall Accommodation at <b><?php echo strtoupper($data->HALLADMITTED) ?> Hall </b>. You will be required to make payment of  GHS  <?php echo $hallInfo->AMOUNT ?> into any branch of Zenith Bank Ghana with account number <?php echo $hallInfo->ACCOUNTNUMBER ?> . <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                                                                    NOTE: Hall fees paid is not refundable.
                                                                </p>
    <?php } ?>
                                                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                                                            <p>You are required to make full payment of all fees before or on Friday 30th June <?php echo date('Y') ?>. </p>

                                                            <p>Please, accept my congratulations on your admission to the University.</p>

                                                            <div>
                                                                <table>
                                                                    <tr>
                                                                        <td>
                                                                            <p>Yours faithfully</p>
                                                                            <p><img src='media/img/signature.png' style="width:90px;height:auto;" /></p>
                                                                            <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                                                                        </td>


                                                                    </tr>

                                                                </table>
                                                            </div>
                                                            <td> <img src='media/img/footer.jpg' style=""  class="image-responsive"/> 

                                                        </div>
                                        </div></div></div>           
                    
<?php }?>
</div>
 
</div>

 </div>  
</body></html>