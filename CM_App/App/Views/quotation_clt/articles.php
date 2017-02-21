	<section class="content-header">
		<span class="content-title"><i class="fa fa-home"></i>تدبير طلب عرض أثمنة</span>
	</section>
	<div class="row doc-infos">
		<div class="col-sm-6 col-xs-12">
			<div class="box-infos-search">
				<section class="content-header box-infos-header">
					<span class="content-title"> عرض أثمنة</span>
				</section>
				<div class="box-infos">
					<h3>الرقم: <?= $quotation_clt->num ?></h3>
					<p>التاريخ: <?= $quotation_clt->dt ?></p>
					<p>الموضوع: <?= $quotation_clt->subject ?></p>
					<p>ملاحظات: <?= $quotation_clt->discr ?></p>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xs-12">
			<div class="box-infos-search">
				<section class="content-header box-infos-header">
					<span class="content-title"><i class="fa fa-home"></i> العميل</span>
				</section>
				<div class="box-infos">
					<h3 class="box-infos-name"><?= $quotation_clt->client_name ?></h3>
					<p class="box-infos-city"><?= $quotation_clt->city ?></p>
					<p class="box-infos-address"><?= $quotation_clt->address ?></p>
				</div>
			</div>
		</div>
	</div>
	<section class="content-header">
		<span class="content-title"><i class="fa fa-home"></i>المنتجات</span>
		<ul class="header-btns">
			<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_sales): ?>
			<li>
				<a href="<?= App::$path ?>quotation_clt/addart/<?= $quotation_clt->id ?>" class="btn btn-success">
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
				<a href="<?= App::$path ?>quotation_clt/printdetails/<?= $quotation_clt->id ?>" target="_blank" class="btn btn-default">
					<i class="fa fa-print"></i>
					<span class="hidden-xs hidden-sm"> طباعة</span>
				</a>
			</li>
		</ul>
	</section>
	<section class="content">
		<div class="table-responsive">
			<table class="table main-table rtl_table data-table table-striped table-hover">
			<thead>
			<tr>
				<th>&nbsp;</th>
				<th>رقم المنتج</th>
				<th>الكود</th>
				<th>الإسم</th>
				<th>الكمية</th>
				<th>الثمن</th>
				<th>المجموع</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($quotation_clt_arts as $quotation_clt_art): ?>
				<tr>
					<td class="table-actions">
				<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_articles): ?>
					<a href="<?= App::$path ?>quotation_clt/editart/<?= $quotation_clt_art->quotation_clt_id ?>/<?= $quotation_clt_art->id ?>" class="btn btn-warning btn-xs">تعديل</a>
						<a href="#" class="btn btn-danger btn-xs" quotation_art_id="<?= $quotation_clt_art->id ?>" onclick="deleteElementArt(this, event);">حذف</a>

				<?php endif; ?>
					</td>
					<td><?= $quotation_clt_art->art_id ?></td>
					<td><?= $quotation_clt_art->ref ?></td>
					<td><?= $quotation_clt_art->desig ?></td>
					<td><?= $quotation_clt_art->qte ?></td>
					<td><?= $quotation_clt_art->price ?></td>
					<td><?= $quotation_clt_art->total ?></td>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div>

	</section>
