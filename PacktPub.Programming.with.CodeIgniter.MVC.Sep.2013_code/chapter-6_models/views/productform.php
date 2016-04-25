<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Form Example</title>
</head>

<body>

<?php if (validation_errors()) : ?>
	<?php echo validation_errors() ; ?>
<?php endif; ?>

<?php
	echo form_open($form['action'], $form['attributes']) ;
	echo form_hidden($form['hidden_fields']); ?>

<table>
    <tr>
        <td><?php echo form_label($form['product_name']['label']['text'], $form['product_name']['label']['for']); ?></td>
        <td><?php echo form_input($form['product_name']['field']); ?></td>
    </tr>

    <tr>
        <td><?php echo form_label($form['product_sku']['label']['text'], $form['product_sku']['label']['for']); ?></td>
        <td><?php echo form_input($form['product_sku']['field']); ?></td>
    </tr>

    <tr>
        <td><?php echo form_label($form['product_quantity']['label']['text'], $form['product_quantity']['label']['for']); ?></td>
        <td><?php echo form_input($form['product_quantity']['field']); ?></td>
    </tr>

    <tr>
        <td colspan="3"><?php echo form_submit('productsubmit', 'Send'); ?></td>
    </tr>

</table>

<?php echo form_close() ; ?>

</body>
</html>