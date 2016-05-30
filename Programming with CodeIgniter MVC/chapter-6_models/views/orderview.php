<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Products List</title>
</head>
<body>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>SKU</th>
        <th>Quantity</th>
	  <th>Actions</th>
    </tr>

	<?php foreach ($products as $product): ?>
    	<tr>
        <td><?php echo $product->product_id; ?></td>
        <td><?php echo $product->product_name;  ?></td>
        <td><?php echo $product->product_sku ; ?></td>
        <td><?php echo $product->product_quantity ; ?></td>
	  <td><a href="<?php echo site_url("order/product/" . $product->product_id); ?>">Order Product</a> </td>
    	</tr>
   	<?php endforeach; ?>
</table>

</body>
</html>