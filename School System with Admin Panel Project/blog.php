<?php
include 'header.php'; 
 $conn=mysql_connect("localhost","root","123456");
$database="school";
mysql_select_db($database, $conn);
?>
<div class="content">
  <div id="blog">
    <div class="sidebar">
      <h2>Archives</h2>
      <h3 class="first"><a href="#">2011 <span>(60)</span></a></h3>
      <div>
        <p><a href="#">December <span>(1)</span></a></p>
        <p><a href="#">November <span>(11)</span></a></p>
        <p><a href="#">October <span>(3)</span></a></p>
        <p><a href="#">September <span>(8)</span></a></p>
        <p><a href="#">August <span>(2)</span></a></p>
        <p><a href="#">July <span>(3)</span></a></p>
        <p><a href="#">June </a></p>
        <p><a href="#">May <span>(8)</span></a></p>
        <p><a href="#">April <span>(7)</span></a></p>
        <p><a href="#">March <span>(7)</span></a></p>
        <p><a href="#">February <span>(10)</span></a></p>
        <p><a href="#">January <span>(1)</span></a></p>
      </div>
      <h3><a href="#">2010</a></h3>
      <h3><a href="#">2009</a></h3>
    </div>
    <div class="article">
      <ul>
        <li class="first">
          <div class="section">   </div>
          <div>
          <?php


$Per_Page = 4;   

if (!isset($_GET['page'])) {
    $Page = 1;
} else {
    $Page = $_GET['page'];
}
$Page_Start = (($Per_Page * $Page) - $Per_Page);
         $sql_slct = mysql_query("select * from article order by created_date desc limit $Page_Start,$Per_Page"); 

        // $fetch = mysql_fetch_array($sql_slct);
        while ( $raw= mysql_fetch_assoc($sql_slct) ) { ?>
         
          <h1><a href="#"><?php echo $raw['title'];?></a></h1>
            <p><?php echo $raw['content'];?></p>
            <h1><a href="#"><?php echo $raw['tag'];?></a></h1><hr style="border: 0 none;  border-top: 2px dashed #E4E8E8;  background: none; height:0;"><br>
        <?php }


              ?>

          </div>
        </li>
        
      </ul>
      <div id="paging"><?php 
      $slct = mysql_query("select * from article"); 
      $total_rows = mysql_num_rows($slct);
      if ($total_rows <= $Per_Page) {
        $Num_Pages = 1;
      } elseif (($total_rows % $Per_Page) == 0) {
          $Num_Pages = ($total_rows / $Per_Page) ;
      } else {
          $Num_Pages = ($total_rows / $Per_Page) + 1;
          $Num_Pages = (int) $Num_Pages;
      }




      ?>

        <ul>
        <?php  for ($i=1; $i <= $Num_Pages; $i++) { ?>
          <li class="selected"><a href="?page=<?php echo $i?>"><?php echo $i ?></a></li>
          <?php } ?>
        </ul>
    </div>
  </div>
</div>



<?php
include 'footer.php'; 
?>