<?php
   // Start our session. This must be done before performing any output
   session_start();

   // We must check if "mode" or "action" were passed as parameters via input form
   if (isset ($_REQUEST['p_mode'])) $mode = $_REQUEST['p_mode']; else $mode = NULL;
   if (isset ($_REQUEST['p_action'])) $action = $_REQUEST['p_action']; else $action = NULL;
   if (isset ($_REQUEST['p_cat_id'])) $_SESSION['cat_id'] = $_REQUEST['p_cat_id'];
?>

<HTML>
<!-- We will hard-code unchangeable HTML parts of our script -->

<HEAD>
<TITLE>VISCAT - Sample PHP OnLine Catalogue</TITLE>
</HEAD>

<BODY>
<B>Welcome to VISCAT</B> - Simple OnLine PHP Catalogue!<BR>

<!-- In a table below, we will show our application workspace in two sections:
      1: Left side - non-changing access-at-will menu
      2: Right side - changeable content depending on our current action mode
-->
<TABLE border='1' cellpadding='3'>
  <TR>
    <TD valign='top'>
      <A href='viscat.php?p_mode=1&p_action=0'>Change Catalogue</A>&nbsp;&nbsp;
      <A href='viscat.php?p_mode=2&p_action=0'>Select Articles</A>&nbsp;&nbsp;
      <A href='viscat.php?p_mode=3&p_action=0'>View Basket</A>&nbsp;&nbsp;
    </TD>
  </TR>
  <TR>
    <TD valign='top'>

<!-- From here on, we will generate HTML code using PHP -->
<?php

   // Now we are about to make decision based on our "mode" and "action" parameters!
   // If we find "mode" variable to be NULL, we suppose that it has not yet been set
   //  and set it to 1 (catalogue selection)
   if ($mode == NULL) $mode = 1; // Because we want our shopper to select a catalogue first

   // The same should be done with "action" variable
   if ($action == NULL) $action = 0; // Action "0" means nothing or invokes default state

   // We can not select article without catalogue selected
   if (($mode > 1) && ($_SESSION['cat_id'] < 1)) {
      print "<FONT color='red'>Please select a catalogue first!</FONT><BR>";
      $mode = 1;
   }

   // Let's connect to our database - we will use $mycon_id variable;
   //  If a connection has already been established, $mycon_id will be other than NULL...
   if (!isset ($mycon_id))
   {
      $mycon_id = mysql_connect("localhost", "root", "");

      if ( (!$mycon_id) || (!mysql_select_db("davor", $mycon_id)) )
      {
         // Let's inform about the error and get out
         print "Could not connect to the database! <BR>";
         print "MySQL Error " . mysql_errno() . ": " . mysql_error() . "<BR>";
         print "Exiting...";
         // We leave...
         exit;
      }

   }
   
   // We can display the catalogue name if any is selected...
   if ($_SESSION['cat_id'] > 0)
   {
      $data_row = mysql_fetch_array(
                    mysql_query( "SELECT * FROM CATALOGUE WHERE CAT_ID='" . mysql_escape_string($_SESSION['cat_id']) . "'",
                                 $mycon_id)
                    );
      $image_name = $data_row["IMAGE_NAME"];
      print "<EM>Active catalogue is</EM>: <B>" . $data_row["DESCRIPTION"] . "</B><BR>\n";
   }
   
   // Now make a decision about content; we will split this part into several
   //  sub-routines, placed inside separated FUNCTIONS, to simplify reading of code

   switch ($mode)
   {
     case 1: // Catalogue Selection
       {
         print "<FONT color='blue'><B>Catalogue Selection:</B></FONT><HR>";
         // Now invoke procedures used to select catalogue etc.
         CatalogueProcedures();
         break;
       }
     case 2: // Selecting Articles
       {
         print "<FONT color='green'><B>Selecting Articles</B></FONT><BR>";
         // Call procedures handling article selection
         ArticleProcedures();
         break;
       }
     case 3: // Selecting Articles
       {
         print "<FONT color='purple'><B>Basket Preview</B></FONT><BR>";
         // Go to function which handles basket contents
         BasketProcedures();
         break;
       }
   }

   print "&nbsp;";
   
   // Catalogue selection procedures
   function CatalogueProcedures()
   {
      global $action; // We use global action variable
      global $mycon_id; // ... and global database connection variable

      switch ($action)
      {
         case 0: // Display catalogue list
         {
           $data = mysql_query("SELECT * FROM CATALOGUE", $mycon_id);
           if (!$data)
           {
              print "<BR>Sorry, no catalogue could be retrieved!<BR>\n";
              exit;
           }
           // Let's create links for catalogue selection
           while ($data_row = mysql_fetch_array($data))
           {
              print "\n<A href='viscat.php?p_mode=2&p_action=0&p_cat_id=".$data_row["CAT_ID"]."'>";
              print $data_row["DESCRIPTION"];
              print "</A><BR>";
           }
           break;
         }
      }
   
   }
   
   // Article selection procedures
   function ArticleProcedures()
   {
      global $image_name;
      global $action; // We use global action variable
      global $mycon_id; // ... and global database connection variable

      if (isset ($_REQUEST['p_article_id'])) $p_article_id = $_REQUEST['p_article_id']; else $p_article_id = NULL;
      if (isset ($_REQUEST['p_quantity'])) $p_quantity = $_REQUEST['p_quantity']; else $p_quantity = NULL;

      // Create image file name (images are located in subdir "images")
      $image_file_name = "./images/" . $image_name;
      
      // Now let us see which content to display for article selection (picture or article list)
      switch ($action)
      {
         case 1: // Add article to basket if any passed
         {
            $data_row = mysql_fetch_array(
                         mysql_query( "SELECT * FROM ARTICLE ".
                                      "WHERE ARTICLE_ID='" . mysql_escape_string ($p_article_id) . "'",
                                      $mycon_id )
                        );
	    if ($data_row)
	    {
            	$basket_row = array( $p_article_id,
                	                 $data_row["ARTICLE_NAME"],
                        	         (int) $p_quantity,
                                	 $data_row["PRICE"],
                                 	$p_quantity * $data_row["PRICE"] );
            	$_SESSION['basket'][] = $basket_row;

 	           print "<B>{$_REQUEST['p_quantity']} pc(s) of Article '{$_REQUEST['p_article_id']}' has been added to your basket.</B><BR>\n";
	    }
         }
         
         case 0: // Display image and load areas
         {
            // Display image from physical file
            print "<IMG src='$image_file_name' border='0' usemap='#imagemap'>\n";
            // Load and display areas
            $data = mysql_query( "SELECT * FROM CATAREA, ARTICLE ".
                                 "WHERE ARTICLE.ARTICLE_ID = CATAREA.ARTICLE_ID ".
                                 "      AND CAT_ID='" . mysql_escape_string ($_SESSION['cat_id']) . "'",
                                 $mycon_id);
            // Create a map
            print "<MAP name='imagemap'>\n";
            while ($data_row = mysql_fetch_array($data))
            {
               print "<AREA shape='POLY' ".
                     "onClick='ShowArticleInfo(\"<B>[".$data_row["ARTICLE_ID"]."] ".
                     $data_row["ARTICLE_NAME"]. "</B>\", ".
                     "\"" .$data_row["ARTICLE_ID"]. "\")' ".
                     "alt='" . $data_row["ARTICLE_NAME"]." \$".$data_row["PRICE"]."\n\r(" . $data_row["DESCRIPTION"] . ")'".
                     " coords='" . $data_row["POINTS"] . "'>\n";
            };
            print "</MAP>\n\n";
            // Next <DIV> is used to display article info and input form
            print "<DIV id='artinfo' style='border-style:solid; border-width:1px; broder-color:black;'></DIV>";
            // Following script is creating appropriate form depending on article selected
            print "<script language='JavaScript'>                                                   \n";
            print " function ShowArticleInfo(info, article_id)                                      \n";
            print " {                                                                               \n";
            print "  window.artinfo.innerHTML =                                                     \n";
            print "   '<FORM method=\"get\" name=\"article_form\" '+                                \n";
            print "   '      action=\"viscat.php\">'+                                               \n";
            print "   ' <INPUT type=\"button\" onClick=\"form.submit()\" value=\"Add\">'+           \n";
            print "   ' <INPUT type=\"text\" name=\"p_quantity\" value=\"1\" size=\"3\"> pcs'+      \n";
            print "   ' <INPUT type=\"hidden\" name=\"p_mode\" value=\"2\">'+                       \n";
            print "   ' <INPUT type=\"hidden\" name=\"p_action\" value=\"1\">'+                     \n";
            print "   ' <INPUT type=\"hidden\" name=\"p_article_id\" value=\"'+article_id+'\">'+    \n";
            print "   ' of &nbsp;'+info+', to your basket'+                                         \n";
            print "   '</FORM>';                                                                    \n";
            print "  document.forms[0].elements[1].focus();                                         \n";
            print "  document.forms[0].elements[1].select();                                        \n";
            print " }                                                                               \n";
            print "</script>\n                                                                      \n";
            break;
         }
         
      }
      
   };

   // Basket handling procedures
   function BasketProcedures()
   {
      global $action; // We use global action variable

      switch ($action)
      {
         case 0: // View basket contents
           {
              print "<TABLE border='1'>\n";
              print " <TR><TD><B>Article ID</B></TD>".
                    "  <TD><B>Article Name</B></TD>".
                    "  <TD align='right'><B>Quantity</B></TD>".
                    "  <TD align='right'><B>Price</B></TD>".
                    "  <TD align='right'><B>Amount</B></TD>".
                    " </TR>\n";

	      $amount = 0;

              for ($i = 0; $i < count($_SESSION['basket']); $i++)
              {
                print " <TR>";
                print "  <TD>".$_SESSION['basket'][$i][0]."</TD>";
                print "  <TD>".$_SESSION['basket'][$i][1]."</TD>";
                print "  <TD align='right'>".$_SESSION['basket'][$i][2]."</TD>";
                print "  <TD align='right'>".$_SESSION['basket'][$i][3]."</TD>";
                print "  <TD align='right'><B>".number_format($_SESSION['basket'][$i][4], 2, ".", ",")."</TD>";
                print " </TR>\n";
                $amount += $_SESSION['basket'][$i][4];
              };
              print " <TR><TD colspan='4' align='right'><B>Total:</TD>";
              print "   <TD align='right'><B>".number_format($amount, 2, ".", ",")."</TD></TR>\n";
              print "</TABLE>\n";
           }
      }
   }

?>

<!-- Finish-up physical HTML code -->

    </TD>
  </TR>
</TABLE>

</BODY>

<!-- Here we physically end our HTML code -->
</HTML>
