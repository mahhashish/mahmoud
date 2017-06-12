<?php
include 'header.php'; 
 include 'sidebar.php';
 $conn=mysql_connect("localhost","root","123456");
$database="school";
mysql_select_db($database, $conn);
$msg = "";
 	 	 
	if(isset($_POST['submit']) && !empty($_POST['submit'] ))
	{
	$title = $_POST['title'];
	$content = $_POST['content'];
	$tag = $_POST['tag'];
	//echo "insert into article (title,content,tag) values ('$title','$content','$tag')";
	//die;
 	
$sql_insert = mysql_query("insert into article (title,content,tag) values ('$title','$content','$tag')");

    if($sql_insert)
    {
    	$msg = "Data Successfully Added";
    }
    else
    {
    	$msg = "Error to add Data";
    }
}
 if(isset($_POST['update']))
 {
      
      $id = $_GET['status'];
      $new_title = $_POST['title'];
      $new_content = $_POST['content'];    
      $new_tag = $_POST['tag'];
 
 
 	  $updt = mysql_query("update article set title = '$new_title', content = '$new_content', tag = '$new_tag' where id = '$id'  ");
 	  
 	  if ($updt)
 	  {
 	  
 	  header("location: dashboard.php");
 	  $msg = "Update Successfully";
      }
      else
      {

      $msg = "Problem to update data";
      }
 }

	if(isset($_GET['del_id']))
         {   
        	$id = $_GET['del_id'];
            $dlt = mysql_query("delete from article where id ='$id' ");
            $msg = "Article Deleted Successfully";
             
         } 
			



?>
<div id="mydiv" style="color: red; padding-left: 400px; padding-bottom: 10px; padding-top: 10px; font-size: 20px;"><?php echo $msg;?></div>
	
	
	<section id="main" class="column">
		
		
		
		<article class="module width_full">
			
		
		<article class="module width_3_quarter">
		<header><h3 class="tabs_involved">Article Listing</h3>
		
		</header>

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter" cellspacing="0"> 
			<thead> 
				<tr> 
   					<th></th> 
    				<th>Title</th> 
    				<th>Description</th> 
    				<th>Tags</th> 
    				<th>Created Date</th>
    				<th>Actions</th> 
				</tr> 
			</thead> 
			<?php 
                       
      $slct = mysql_query("select * from article"); 
     while($fetch = mysql_fetch_assoc($slct))
           {                 

			?>
			<tbody> 
				<tr> 
				    <td><?php echo $fetch['id']; ?></td> 
   					<td><?php echo $fetch['title']; ?></td> 
    				<td><?php echo substr($fetch['content'], 0,30); ?></td> 
    				<td><?php echo substr($fetch['tag'], 0,10); ?></td> 
    				<td><?php echo $fetch['created_date']; ?></td> 
    				<td><a href="dashboard.php?status= <?php echo $fetch['id']; ?>"><input type="image" src="images/icn_edit.png" title="Edit"></a><a href="?del_id= <?php echo $fetch['id']; ?>"><input type="image" src="images/icn_trash.png" title="Trash" onclick="return confirm('Are you sure for delete this Article?')"></a></td> 
    				
				</tr> 
				<?php } ?>
				
				


				
			</div><!-- end of #tab1 --> 
			
			<div id="tab2" class="tab_content">
			<table class="tablesorter" cellspacing="0"> 
			
			<tbody> 
				
			</tbody> 
			</table>

			</div><!-- end of #tab2 -->
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article -->

		<?php 
       
        if(isset($_GET['status']))                             
          {
          	$id = $_GET['status'];

$slct = mysql_query("select * from article where id = '$id'");

     $fetch = mysql_fetch_assoc($slct)    
		?>

<form name="article" method="post" id="post">

<div class="clear"></div>
		
		<article class="module width_full">
			<header><h3>Post New Article</h3></header>
				<div class="module_content">
						<fieldset>
							<label>Post Title</label>
							<input type="text" id="title" name="title" value="<?php echo $fetch['title']; ?>" >
						</fieldset>
						<fieldset>
							<label>Content</label>
					<textarea rows="18" id="content" name="content"  ><?php echo $fetch['content']; ?></textarea>
						</fieldset>
						<fieldset style="width:48%; float:left;"> <!-- to make two field float next to one another, adjust values accordingly -->
							<label>Tags</label>
							<input type="text" name="tag" id="tag" style="width:92%;" value="<?php echo $fetch['tag']; ?>">
						</fieldset><div class="clear"></div>
				    	</div>
   		   					<input type="submit" value="Update" id="update" name="update" class="alt_btn">
   		   					
							<input type="submit" value="Reset" id="reset" name="reset" class="alt_btn">
			    </div>
		</article><!-- end of post new article -->

</form>
		<?php } 

         else 
         {
		?>





<form name="article" method="post" id="post1">

<div class="clear"></div>
		
		<article class="module width_full">
			<header><h3>Post New Article</h3></header>
				<div class="module_content">
						<fieldset>
							<label>Post Title</label>
							<input type="text" id="title" name="title" >
						</fieldset>
						<fieldset>
							<label>Content</label>
							<textarea rows="18" id="content" name="content"></textarea>
						</fieldset>
						<fieldset style="width:48%; float:left;"> <!-- to make two field float next to one another, adjust values accordingly -->
							<label>Tags</label>
							<input type="text" name="tag" id="tag" style="width:92%;">
						</fieldset><div class="clear"></div>
				    	</div>
   		   					<input type="submit" value="Publish" id="submit" name="submit" class="alt_btn">
   		   					
							<input type="submit" value="Reset" id="reset" name="reset" class="alt_btn">
			    </div>
		</article><!-- end of post new article -->

</form>
		<?php } ?>
			
		
			
		<div class="spacer"></div>
	</section>


</body>

</html>