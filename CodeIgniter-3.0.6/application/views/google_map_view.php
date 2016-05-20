<!DOCTYPE html">
<meta http-equiv="Content-type"
      content="text/html; charset=utf-8" />
<html>
    <head>
        <script src = "assets/js/jquery-2.2.3.min.js" ></script>
        <!--Render all the map JS provided by rendering controller-->
        <?php echo $map['js']; ?>
    </head>
    <body>
        <H3>CodeIgniter Powered CI Google Maps Library : <H3>
                <HR/><ul>
                    <!—Let the User Always Get Back to the default Zoom out -->
                    <li><?php echo anchor("gmaps", '<B>See All Locations</B>');
        ?>
                    </li>
                    <?PHP
                    $i = 0;
                    foreach ($locations as $location) {// Show to the user all the possible Zoom Ins defined places by
//the controller, so that user may zoom in by issuing the
// anchor.
                        $controller = $controllers["$i"];
                        $i++;
                        ?>
                        <li>
                            <?php echo anchor
                                    ("gmaps/$controller", "Zoom-In to " . $location);
                            ?>
                        </li>
<?PHP } ?>
                    
                </ul>
                <HR>

<?php echo $map['html']; ?>
                </body>
                </html>