<?php //trustedvaluesform.php ?>
<html>
<form action="trustedvaluesformhandler.php" method="get">
<input type="hidden" name="price" value="<?=$price?>">
Credit Card #:<input type="text" size="10" name="cc">
<br><br>
<input type="submit" value="Order Product">
</form>
</html>