// JavaScript Document
$(function(){
	 $("#page_goto").keydown(function(event) {
		 // Allow: backspace, delete, tab, escape, and enter
		 if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13) {
                 // let it happen, don't do anything
                 return;
        }else{
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            } 
		}
	 });
	 

	$('#form2').submit(function(){
		
		var intval = $('#page_goto').val();
		var lastpage = parseInt($('.lastpage').text());
		if(intval !=""){
			if(intval > lastpage || intval < 1 ){
				alert('There is no page numbered "'+intval+'" in this document.');
				$('#page_goto').val('1');
				}else{
			window.report.Goto(intval);
				}
			}
			return false;
		});
		
		
		//preloader
$("#overlay").load(function(e) {
	$('.lastpage').text('0');
	$('.lastpage').text($("#overlay").contents().find("#totalcount").val());
    $(".preloader-floating").fadeOut('slow');
});	

//save
if($("#save")){
$("#save").click(function(){
	$('#genform').attr('action','public/report/view/payslipreport.php');
	$('#genform').submit();
	$('#genform').attr('action','public/report/view/pdtviewer.php');
	});
}

//zoom
if($('#zoom')){
	$('#zoom').change(function(){
		var size = $(this).val();
		if(size != ''){
			$("#overlay").contents().find(".holdtable").attr('class','holdtable');
			$("#overlay").contents().find(".holdtable").each(function(index, element) {
                $(this).addClass('zoom_'+size);
            });
			}
		});
	}
		
	});
	
