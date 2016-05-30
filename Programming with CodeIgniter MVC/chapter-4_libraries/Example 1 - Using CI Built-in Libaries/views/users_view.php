<!DOCTYPE html">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<html>
<head>
<title>Showing Users Table Using CI Build-In table Library </title>
</head>
<body>
    <div id='results'>
    <?php echo $this->table->generate($users); ?>
    </div>
</body>
</html>
