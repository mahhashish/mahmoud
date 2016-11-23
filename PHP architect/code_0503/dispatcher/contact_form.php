<form method=POST action="<?php echo$post_url?>">
<input type="hidden" name="cid" value="<?php echo $contact['cid']?>">
<table align=center border=0 cellpadding=0 cellspacing=0>
    <tr>
        <th>Name:</th>
		<td><input type="text" name="name" value="<?php echo $contact['name']?>">
    <tr>
    <tr>
        <th>Email:</th>
		<td><input type="text" name="email" value="<?php echo $contact['email']?>">
    <tr>
	<tr>
		<td align=center colspan=2>
		<input type="submit" name="submit" value="<?php echo $submit_value?>">
		<input type="submit" name="submit" value="Cancel">
		</td>
	</tr>
</table>
