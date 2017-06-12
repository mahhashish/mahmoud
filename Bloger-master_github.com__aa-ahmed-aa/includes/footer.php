        </div><!-- /.blog-main -->

        <div class="col-sm-3 col-sm-offset-1 blog-sidebar">
          <div class="sidebar-module sidebar-module-inset">
            <h4>About</h4>
            <p><?php echo $site_description; ?></p>
          </div>
          <div class="sidebar-module">
            <h4>Categories</h4>
			<?php if($categories):?>
            <ol class="list-unstyled">
				<?php while($row = $categories->fetch_assoc()):?>
					<li><a href="posts.php?caterorie=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></li>
				<?php endwhile; ?>
            </ol>
			<?php else:?>
				<p>There is no Categories</p>
			<?php endif;?>
          </div>
        </div><!-- /.blog-sidebar -->

      </div><!-- /.row -->

    </div><!-- /.container -->

    <footer class="blog-footer">
      <p>PHPLoversBlog &copy; 2015</p>
      <p>
        <a href="#">Back to top</a>
      </p>
    </footer>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
  </body>
</html>