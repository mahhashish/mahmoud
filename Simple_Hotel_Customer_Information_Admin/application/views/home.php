<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Cuizon' Hotel Admin</title>
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.4.js"></script>
	<link rel="stylesheet" href="<?php echo base_url();?>css/mycss.css">
	<script type="text/javascript">
	$(document).ready(function(){
		$("#view").click(function(){
				var search = $("#search").val();	
				$.ajax({
						type: "POST",				
						data: {search: search},
						url: "<?php echo site_url('HotelReservationController/view_all');?>",
						success: function(data){
								$("#result").html(data);	
						}
					});
				return false;
			});

		$("#search_lname").click(function() { 
			var search = $("#searchlname").val();   
			if(search == "")
			{
				$("#err").show();
			}
			else
			{
				$("#err").hide();
				$.ajax({
					type: "POST",
					url: "<?php echo site_url('HotelReservationController/search');?>",
					//data: {name: $(this).val()},
					data: {search: search},
					//data:'name='+$("#name").val(),
					success: function(html){
						$("#searchresult").show();
						$(".search").hide();
						$("#searchresult").html(html);							
					}
				});
			}
			$("#searchlname").val("");
			return false;
			});
		
		$("#register").click(function(){
				var fn = $("#fn").val();
				var mi = $("#mi").val();
				var ln = $("#ln").val();	
				var em = $("#em").val();	
				var con = $("#con").val();	

				if(fn == "" || ln== "" || em == "" || con == "")
				{
					alert("There are/is empty field/s.");
				}
				else
				{
					$.ajax({
						type: "POST",				
						data: {fn: fn, mi: mi, ln: ln, em: em, con: con},
						url: "<?php echo site_url('HotelReservationController/registration');?>",
						success: function(data){
							$("#view").click();	
							$("#result").html(data);					
						}
					});
					alert("Record is successfully added");
				}
				$("#fn").val("");
				$("#mi").val("");
				$("#ln").val("");	
				$("#em").val("");	
				$("#con").val("");					
				return false;
			});	

		$("#update").click(function(){
			//document.getElementById('#entry').hide();
				var cid = $("#cid").val();
				var fname = $("#fname").val();
				var min = $("#min").val();
				var lname = $("#lname").val();	
				var email = $("#email").val();	
				var contact = $("#contact").val();	
				if(cid == "" || fname == "" || lname == "" || email == "" || contact == "")
				{
					alert("Opps! Check out empty field/s.");
				}
				else
				{
					$.ajax({
						type: "POST",				
						data: {cid: cid, fname: fname, min: min, lname: lname, email: email, contact: contact},
						url: "<?php echo site_url('HotelReservationController/update');?>",
						success: function(data){
								$("#view").click();	
								$("#result").html(data);													
						}
					});
					alert("Record is successfully updated");

				}
				
				$("#cid").val("");
				$("#fname").val("");
				$("#min").val("");
				$("#lname").val("");	
				$("#email").val("");	
				$("#contact").val("");				
				return false;
			});

			
			$("#add").click(function(){
				$("#div-entry").show();
				$(".editdiv").hide();
				$("#result").hide();
				$("#welcome").hide();
				$("#searchresult").hide();
				$(".search").hide();
				$("#err").hide();
			})
			
			$("#edit").click(function(){
				$(".editdiv").show();
				$("#div-entry").hide();
				$("#result").hide();
				$("#welcome").hide();
				$("#searchresult").hide();
				$(".search").hide();
				$("#err").hide();
			})

			$("#view").click(function(){
				$(".editdiv").hide();
				$("#div-entry").hide();
				$("#result").show();
				$("#welcome").hide();
				$(".search").hide();
				$("#searchresult").hide();
				("#add").slideDown("slow");
				$("#err").hide();
			})
			$("#search").click(function(){
				$(".search").show();
				$(".editdiv").hide();
				$("#div-entry").hide();
				$("#result").hide();
				$("#welcome").hide();
				$("#searchresult").hide();
				$("#err").hide();
			})	
	});
</script>
<script>
	function ChangeColor(tableRow,highLight)
		    {
			    if (highLight)
			    {
			      tableRow.style.backgroundColor = '#dcfac9';
			    }
			    else
			    {
			      tableRow.style.backgroundColor = 'white';
			    }
  			}
</script>
	<style type="text/css">
		

	</style>
</head>
<body>
<div id="container">
	<h1>Simple Customer Information Admin </h1>
	
	<div id="body">
		<div id="welcome">
			<center><p style="font-size: 15pt;"><b>Simple Customer Information Admin Using CodeIgniter</b></p></center>	<hr/>

			<p style="font-size: 14pt; padding: 25px 25px 25px 25px;font-weight: bolder;text-align: justify;margin-left: 50px;margin-right:50px;line-height: 2.0;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				This is where transaction of data is being done. Adding of new customer is 
				also done here, simply click the add new customer button and you will be redirected
				to	the customer registration form. If you want to update the customer information,	
				simply click the update customer button and the form will be displayed. On the other 
				hand, if you want to view the details of the customer, simply click the view customer 
				button to do so. If you want to search for a specific customer, just click the search 
				customer button and a search button	will be displayed where you will search the 
				customer by lastname.
			</p>
		</div>
		<div id="action">
			<center><p style="font-size: 15pt;"><b>Menu</b></p>

			<hr/>
			
			<form>

				<button id="add"><img src="<?php echo base_url();?>images/add.png" alt="register"/><br>Add New Customer</button><br><br> 
				
				<!-- Diri ang edit section -->
				<button id="edit"><img src="<?php echo base_url();?>images/edit.png" alt="edit"/><br>Update Customer</button><br><br>
					
					<!-- mao ni ang sulod sa edit div section inig click sa edit button -->
					<div class="editdiv" style="display:none;"><!--  -->
						<center><p style="font-size: 15pt;"><b>Customer Update Form</b></p><hr/>
						<form >
							<table>
								<tr>
									<td>
										<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Customer ID</b></p> <input type="text" id="cid" placeholder="Customer ID" required/></center>
									</td>
								</tr>
								<tr>
									<td>
										<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>First Name</b></p> <input type="text" id="fname" placeholder="First Name" required/></center>
									</td>
								</tr>
								<tr>
									<td>
										<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Middle Initial</b></p> <input type="text" id="min" placeholder="Middle Initial (optional)" /></center>
									</td>
								</tr>
								<tr>																			
									<td colspan="2">
										<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Last Name</b></p><input type="text" id="lname" placeholder="Last Name" required/></center>
									</td>
								</tr>
								<tr>
									<td>
										<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Email Address </b></p><input type="email" id="email" placeholder="Valid Email Address" required/></center>
									</td>
								</tr>
								<tr>
									<td>
										<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Contact Number</b></p> <input type="text" id="contact" placeholder="Contact Number" required/></center>
									</td>
								</tr>
								<tr>
									<td>
										<br><br><center><input type="button" id="update" value="Update" style="width: 80px; border-radius: 5px;" /></center>
									</td><br>
								</tr>
							</table>
						</form>
						<br><br>					
					</div>
				<button id="search"><img src="<?php echo base_url();?>images/search.png" alt="search"/><br>Search Customer</button><br><br> 
					
					<div class="search" style="display:none;"><!--  -->
						<center><p style="font-size: 15pt;"><b>Search Customer</b></p><hr/><br>
							<form >
								<table>
									<tr>
										<td>
											<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b> Last Name </b></p> <input type="text" id="searchlname" placeholder="Search by last name..." required/>
											<input type="button" id="search_lname" value="Search" style="width: 60px;margin-top: -54px;margin-left: 377px;"></center>
											<p id="err" style="display:none;font-size: 12pt;"><b>Input field must not be empty.</b></p>
										</td>
									</tr>
								</table>
							</form>
						</center>
					</div>	
					<div id="searchresult">
					</div>		
	
				<button id="view"><img src="<?php echo base_url();?>images/view.png" alt="view"/><br>View Customer</button><br><br>
			</form>
		
			</center>
		</div>

		<center>
		<div id="div-entry" style="display:none;" ><!-- -->
			<center><p style="font-size: 15pt;"><b>Customer Registration Form</b></p><hr/>
				<form >
					<table>
						<tr>
							<td><!--<p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>First Name</b></p> -->
								<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>First Name</b></p><input type="text" id="fn" placeholder="First Name"></center>
							</td>
						</tr>
						<tr>
							<td>
								 <center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Middle Initial</b></p><input type="text" id="mi" placeholder="Middle Initial (optional)" /></center>
							</td>
						</tr>
						<tr>																			
							<td>
								<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Last Name</b></p><input type="text" id="ln" placeholder="Last Name"></center>
							</td>
						</tr>
						<tr>
							<td>
								<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Email Address</b></p><input type="email" id="em" placeholder="Valid Email Address"/></center>
							</td>
						</tr>
						<tr>
							<td>
								<center><p style="margin-left: 150px;margin-top: 10px;position:absolute;"><b>Contact Number</b></p><input type="text" id="con" placeholder="Contact Number"/></center>
							</td>
						</tr>
						<tr>
							<td>
								<br><br><center><input type="button" id="register" value="Register" style="width: 80px; border-radius: 5px;" /></center>
							</td><br>
						</tr>
					</table>
				</form>
				<br><br>		
			</div><!-- end of entry div -->
			</center>
		
		<div id="result"><tr onmouseover='ChangeColor(this, true);' onmouseout='ChangeColor(this, false);'></div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>
</body>
</html>