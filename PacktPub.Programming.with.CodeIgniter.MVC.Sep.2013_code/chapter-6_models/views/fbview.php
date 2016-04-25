<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My facebook details</title>
</head>
<body>

<div id="my_details">
    <div id="picture"><img src="<?=$me[0]['pic_big'] ?>"></div>
    <div id="my_name"><?=$me[0]['name'] ?></div>
</div>

<table>
    <tr>
        <th>Name</th>
        <th>Link to friend</th>
    </tr>

	<?php foreach ($friends['data'] as $friend): ?>
    <tr>
        <td><?=$friend['name']?></td>
        <td><a href='http://www.facebook.com/<?=$friend["id"]?>'>To friend</a></td>
    </tr>
	<?php endforeach; ?>

</table>

</body>
</html>