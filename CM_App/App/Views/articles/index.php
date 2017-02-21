	<section class="content-header">
		<span class="content-title"><i class="fa fa-home"></i> السلع</span>
		<ul class="header-btns">
			<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_articles): ?>
			<li>
				<a href="<?= App::$path ?>article/add" class="btn btn-success">
					<i class="fa fa-plus-circle"></i>
					<span class="hidden-xs hidden-sm"> إضافة</span>
				</a>
			</li>
			<?php endif; ?>
			<li>
				<a href="#" class="btn btn-primary" onclick="searchToogle('form-search-wrap', event);">
					<i class="fa fa-search"></i>
					<span class="hidden-xs hidden-sm"> بحث</span>
				</a>
			</li>
			<li>
				<a href="<?= App::$path ?>article/printlist" target="_blank" class="btn btn-default">
					<i class="fa fa-print"></i>
					<span class="hidden-xs hidden-sm"> طباعة</span>
				</a>
			</li>
		</ul>
	</section>
	<section class="content">
		<div class="row form-search-wrap">
			<div class="box-infos-search">
				<section class="content-header box-infos-header">
					<span class="content-title"><i class="fa fa-home"></i> بحث</span>
				</section>
				<div class="box-infos">
					<form method="post" name="form-article-search" id="form-article-search">
				<div class="col-md-4 col-sm-6 col-xs-12">
					<?=  $form->input('ref', 'رمز المنتج', [
						'type' => 'text',
						'id' => 'ref',
						'placeholder' => 'رمز المنتج',
						'data-validation' => 'length',
						'data-validation-optional' => 'true',
						'data-validation-length' => 'max100',
						'data-validation-error-msg' => 'رمز المنتج يجب ألا يتجاوز 100 حرف'
					]); ?>
				</div>
				<div class="col-md-4 col-sm-6 col-xs-12">
					<?=  $form->input('desig', 'اسم المنتج', [
						'type' => 'text',
						'id' => 'desig',
						'placeholder' => 'اسم المنتج',
						'data-validation' => 'length',
						'data-validation-optional' => 'true',
						'data-validation-length' => '1-255',
						'data-validation-error-msg' => 'اسم المنتج يجب أن يتراوح ما بين 1 و 255 حرف.'
					]); ?>
				</div>
				<div class="col-md-4 col-sm-6 col-xs-12">
					<?=  $form->select('supplier_id', 'المورد', $suppliers, ['id' => 'supplier_id'], true); ?>
				</div>
				<div class="col-md-4 col-sm-6 col-xs-12">
					<?=  $form->select('category_id', 'الصنف', $categories, ['id' => 'category_id'], true); ?>
				</div>
				<div class="col-md-4 col-sm-6 col-xs-6">
					<?=  $form->select('unit_id', 'الوحدة', $units, ['id' => 'unit_id'], true); ?>
				</div>
				<div class="col-md-4 col-sm-6 col-xs-6">
					<?=  $form->select('tva', 'TVA (%)', $tva, ['id' => 'tva'], true); ?>
				</div>
				<div class="col-lg-12 form-group text-center">
					<button type="submit" id="btn-search-article" class="btn btn-primary">ابحث</button>
				</div>
			</form>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table main-table rtl_table data-table table-striped table-hover">
			<thead>
			<tr>
				<th>&nbsp;</th>
				<th>الرقم</th>
				<th>الكود</th>
				<th>الإسم</th>
				<th>الوحدة</th>
				<th>الصنف</th>
				<th>TVA</th>
				<th>المورد</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($articles as $art): ?>
				<tr>
					<td class="table-actions">
						<a href="<?= App::$path ?>article/show/<?= $art->id ?>" class="btn btn-success btn-xs">عرض</a>
				<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_articles): ?>
					<a href="<?= App::$path ?>article/edit/<?= $art->id ?>" class="btn btn-warning btn-xs">تعديل</a>
						<a href="#" class="btn btn-danger btn-xs" art_id="<?= $art->id ?>" onclick="deleteArt(this, event);">حذف</a>
					<?php endif; ?>
					</td>
					<td><?= $art->id ?></td>
					<td><?= $art->ref ?></td>
					<td><?= $art->desig ?></td>
					<td><?= $art->unit ?></td>
					<td><?= $art->category ?></td>
					<td><?= $art->tva ?></td>
					<td><?= $art->supplier_name ?></td>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div>

	</section>
