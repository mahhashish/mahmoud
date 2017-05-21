<?php

include "form.php";

$form = new CForm('index.php');

$element = new CTextElement('username', 'Username', $form);
$element -> required = true;
$form -> AddElement($element);

$element = new CPasswordElement('password', 'Password', $form);
$element -> required = true;
$form -> AddElement($element);

$items = array( array('IT', 1), array('Finance', 2), array('Executive Management', 3));
$element = new CDropdownElement('department', 'Department', $form, $items);
$element -> required = true;
$form -> AddElement($element);

$element = new CSubmitElement('submit', '', $form, 'submit', 'Go');
$form -> AddElement($element);

$form -> Load();

if ($form -> submitted) {
	if ($form -> elements['username'] -> value !== 'marco' || $form -> elements['password'] -> value !== 'tabini')
		$form -> elements['username'] -> error .= 'Invalid username and/or password';
	else
		die("Login successful");
}
?>
<html>
	<body>
		Please enter your username and password:
		<p>
			<?php $form -> Render(); ?>
		</p>
	</body>
</html>