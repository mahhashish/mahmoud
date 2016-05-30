<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Products List</title>
</head>
<body>

<table>
    <tr>
        <td>ID</td>
        <td>Name</td>
        <td>SKU</td>
        <td>Quantity</td>
	  <td>Actions</td>
    </tr>

	<?php foreach ($products as $product): ?>
    <tr>
        <td><?php echo $product->product_id; ?></td>
        <td><?php echo $product->product_name;  ?></td>
        <td><?php echo $product->product_sku ; ?></td>
        <td><?php echo $product->product_quantity ; ?></td>
	  <td><a href="<?php echo site_url("product/edit/" . $product->product_id); ?>">Edit Product</a> </td>
    </tr>
	<?php endforeach; ?>
</table>

<p>
	<a href="<?php echo site_url('product/add'); ?>">Add Product</a>
</p>

</body>
</html>