<?php include 'includes/header.php'?>
<?php
	
	//create DB Object
	$db = new Database();
	
	//create query
	$query = "SELECT * FROM posts";
	
	//run query
	$post = $db->select($query)->fetch_assoc();
	
	//create query
	$query = "SELECT * FROM categories";
	
	//run query
	$categories = $db->select($query);
	
	//onsubmit
	if(isset($_POST['submit'])){
		//assigne vars
		$title = mysqli_real_escape_string($db->link, $_POST['title']);
		$body = mysqli_real_escape_string($db->link, $_POST['body']);
		$category = mysqli_real_escape_string($db->link, $_POST['category']);
		$author = mysqli_real_escape_string($db->link, $_POST['author']);
		$tags = mysqli_real_escape_string($db->link, $_POST['tags']);
		//simple validation 
		if($title == '' || $body=='' || $category == '' || $author == '' || $tags == ''){
			//set error 
			$error = 'Please fill out all require fields';
		}else{
			$query = "INSERT INTO posts
				(title, body, category, author, tags)
				VALUES('$title','$body','$category','$author','$tags')";
			$insert_row = $db->insert($query);
		}
	}
?>
<form role="form" method="post" action="add_post.php">
  <div class="form">
    <label>Post Title</label>
    <input name="title" type="text" class="form-control" placeholder="Enter Title">
  </div>
  <div class="form">
    <label>Post Body</label>
	<textarea name="body" class="form-control" placeholder="Enter Post"></textarea>
  </div>
  <div class="form">
    <label>Category</label>
	<select name="category" class="form-control">
		<?php while($row = $categories->fetch_assoc()): ?>
			<option  value="<?php echo $row['id']; ?>" ><?php echo $row['name']; ?></option>
		<?php endwhile;?>
	</select>
  </div>
  <div class="form">
    <label>Author</label>
    <input name="author" type="text" class="form-control" placeholder="Enter Author Name">
  </div>
  <div class="form">
    <label>Tags</label>
    <input name="tags" type="text" class="form-control" placeholder="Enter Tags">
  </div>
  <br>
  <div method="post">
		<input name="submit" type="submit" class="btn btn-default" value="Submit"/>
		<a href="index.php" class="btn btn-default" >Cancel</a>
		
  </div>
  <br>
</form>

<?php include 'includes/footer.php'?>