<?php
include 'header.php'; 
 $conn=mysql_connect("localhost","root","123456");
$database="school";
mysql_select_db($database, $conn);
?>



<div class="content">
  <div>
    <div> <img src="images/calling.jpg" alt=""> </div>
    <div>
      <div id="sidebar">
        <h3>Our Education</h3>
        <ul>
          <li id="vision"> <span>Our Vision</span>
            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim venia.</p>
          </li>
          <li id="mission"> <span>Our Mission</span>
            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim venia.</p>
          </li>
          <li id="wecare"> <span>We care</span>
            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim venia.</p>
          </li>
        </ul>
      </div>
      <div id="contact">
        <h4 class="first">Customer Service</h4>
        
      <p>Call or e-mail us for help with any aspect of your purchase, online or offline.</p>
        <div style="padding-left: 100px;">
        <?php
       
         $sql_slct = mysql_query("select email,cellnum,fax,mailadd from contact");
         $fetch = mysql_fetch_assoc($sql_slct);
         
         echo "Email:         ".($fetch['email']).'<br>'; 
         echo "Toll-free No:  ".($fetch['cellnum']).'<br>';
         echo "Fax No:        ".($fetch['fax']);

       ?>
       </div>

        <h4>Mailing Addresses</h4>
        <div style="padding-left: 100px;">
        <?php
        echo($fetch['mailadd']);
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
include 'footer.php'; 
?>