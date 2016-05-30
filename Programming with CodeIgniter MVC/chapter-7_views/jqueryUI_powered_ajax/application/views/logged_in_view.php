<!DOCTYPE html">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<html>
<head>
<script src="http://code.jquery.com/jquery-latest.js" type='text/javascript'></script>
<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="<?=base_url(); ?>/css/my_style.css" media="screen" />	

<script type='text/javascript'>

// The user to call the Ajax handler controller 
var save_user_feedback_submitter 	= '<?=site_url() ?>' + 'index.php/ajax_handler/save_user_feedback';
var get_user_feedbacks 				= '<?=site_url() ?>' + 'index.php/ajax_handler/get_user_feedback_log';

function ajax_save_user_feedback ( feedback ) {
	
	$.ajax({
	        type	:  "POST",
	        url		:  save_user_feedback_submitter,
	        data 	:  { feedback : feedback },
	        dataType: "json",
	        success: function(data) {
				alert (  'Your feedback Updated - Thanks!' ); 
				 
			   }
	   });
	   
}

function ajax_get_user_feedback_log () {
	
	$.ajax({
	        type	:  "POST",
	        url		:  get_user_feedbacks,
	        dataType: "json",
	        success: function(data) {
				
				$('#feedback_log_view').show(); 
				$('#feedback_log_view').html( data.result ); 
				
			   }
	   });
	   
}


$(document).ready(function () { 

$( "#ideas-form" ).dialog({
            autoOpen	: false,
            height  	: 270,
            width		: 700,
            modal		: true,
			resizable	: false,
			effect		: 'drop', 
			direction	: "up", 
			show 		: "slide",
            buttons: {
                "Send Us Your Feedback": function() {
				
					var user_feedback = $('#user_feedback').val();
	 
				    ajax_save_user_feedback ( user_feedback ); 
					// clean feedback enttry for next one.. 
					$('#user_feedback').val('');
					// Show user all its feedback so far 
					ajax_get_user_feedback_log();
					 
					
					$( this ).dialog( "close" );
                },
				"Cancel": function() {
                    $( this ).dialog( "close" );
                }
            }
        });


// When user click for a feedback popup feedback window :

$( '#user_ideas' )
            .button()
            .click(function() {
                $( "#ideas-form" ).dialog( "open" );
            });
 		
$( '#feedback_log' )
            .button()
            .click(function() {
             ajax_get_user_feedback_log();
            });
});

</script >
	
</head>
<body>
<H1>Welcome <?=$user_name; ?>! </H1>
<H1>You are looged in! </H1>
<HR></HR>
<H3>Your User ID is: 	<?=$uid; ?></H3>
<H3>Your System Role is :<?=$role; ?></H3>
<H3>Your Menu options  :<?=$menu; ?></H3>

<DIV >
	<button id='user_ideas'    style="cursor:pointer;position:relative;top:0px" title='Share your feedback/ideas' > Add A New Feedback </button><BR/> 
	<button id="feedback_log"  style="cursor:pointer;position:relative;top:0px" title="Your feedback log"         > See Your Feedback Log </button>	
</DIV>

<div id='feedback_log_view'  style="display:none;width:800px;border-style:solid;border-color:black;overflow-x:auto;height:200px;overflow-y:auto;">

</div>


<H2>		
<?php echo anchor ('index.php/auth/logout', 'Logout' ) ?>
</H2>
		
<div id="ideas-form"   title="Your Feedback To Improve" >


    <form>
    <fieldset>
        <span id="user_name" class="text ui-widget-content ui-corner-all" >Thanks <?=$user_name;?>, Please share your feedback with us</span>
        <textarea name="idea_desc"    id="user_feedback"  rows="10" cols="83" placeholder='Your idea/Improvement Suggestion...' ></textarea>
	 </fieldset>
    </form>
</div>

		
</body>
</html>


