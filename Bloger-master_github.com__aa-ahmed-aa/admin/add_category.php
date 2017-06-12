<?php include 'includes/header.php'?>
<?php 
	
	//create DB Object
	$db = new Database();
	
	//create query
	$query = "SELECT * FROM categories";
	
	//on submit
	if(isset($_POST['submit'])){
		//assigne vars
		$name = mysqli_real_escape_string($db->link, $_POST['name']);
		//simple validation 
		if($name == ''){
			//set error 
			$error = 'Please fill out all require fields';
		}else{
			$query = "INSERT INTO categories (name) VALUES ('$name')";
			$insert_row = $db->insert($query);
		}
	}
?>
<form role="form" method="post" action="add_category.php">
  <div class="form-group">
    <label>Category Name</label>
    <input name="name" type="text" class="form-control" placeholder="Add Category">
  </div>
  <div>
	<input name="submit" type="submit" class="btn btn-default" value="Submit"/>
	<a href="index.php" class="btn btn-default" >Cancel</a>
  </div>
  <br>

</form>

<?php include 'includes/footer.php'?>