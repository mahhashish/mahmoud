	<section class="content-header">
		<span class="content-title"><i class="fa fa-home"></i> الوحدات</span>
		<ul class="header-btns">
			<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_articles): ?>
			<li>
				<a href="<?= App::$path ?>unit/add" class="btn btn-success">
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
				<a href="<?= App::$path ?>unit/printlist" target="_blank" class="btn btn-default">
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
					<form method="post" name="form-unit-search" id="form-unit-search">
				<div class="col-xs-12">
					<?=  $form->input('unit', 'اسم الوحدة', [
						'type' => 'text',
						'id' => 'unit',
						'placeholder' => 'اسم الوحدة',
						'data-validation' => 'length',
						'data-validation-optional' => 'true',
						'data-validation-length' => 'max20',
						'data-validation-error-msg' => 'اسم الوحدة يجب ألا يتجاوز 20 حرف.'
					]); ?>
				</div>
			
				<div class="col-lg-12 form-group text-center">
					<button type="submit" id="btn-search-unit" class="btn btn-primary">ابحث</button>
				</div>
			</form>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table main-table rtl_table data-table table-striped table-hover">
			<tbody>
			<?php
			foreach($units as $unit): ?>
				<tr>
					<td class="table-actions">
						<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_articles): ?>
						<a href="<?= App::$path ?>unit/edit/<?= $unit->id ?>" class="btn btn-warning btn-xs">تعديل</a>
						<?php if($unit->id > 0): ?>
						<a href="#" class="btn btn-danger btn-xs" unit_id="<?= $unit->id ?>" onclick="deleteElement(this, event);">حذف</a>
					<?php endif; ?>
					<?php endif; ?>

						</td>
					<td><?= $unit->unit ?></td>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div>

	</section>
