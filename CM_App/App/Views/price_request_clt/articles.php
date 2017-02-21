	<section class="content-header">
		<span class="content-title"><i class="fa fa-home"></i>تدبير طلب أثمنة</span>
	</section>
	<div class="row doc-infos">
		<div class="col-sm-6 col-xs-12">
			<div class="box-infos-search">
				<section class="content-header box-infos-header">
					<span class="content-title"> طلب أثمنة</span>
				</section>
				<div class="box-infos">
					<h3>الرقم: <?= $pr_clt->num ?></h3>
					<p>التاريخ: <?= $pr_clt->dt ?></p>
					<p>الموضوع: <?= $pr_clt->subject ?></p>
					<p>ملاحظات: <?= $pr_clt->discr ?></p>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xs-12">
			<div class="box-infos-search">
				<section class="content-header box-infos-header">
					<span class="content-title"><i class="fa fa-home"></i> العميل</span>
				</section>
				<div class="box-infos">
					<h3 class="box-infos-name"><?= $pr_clt->client_name ?></h3>
					<p class="box-infos-city"><?= $pr_clt->city ?></p>
					<p class="box-infos-address"><?= $pr_clt->address ?></p>
				</div>
			</div>
		</div>
	</div>
	<section class="content-header">
		<span class="content-title"><i class="fa fa-home"></i>المنتجات</span>
		<ul class="header-btns">
			<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_sales): ?>
			<li>
				<a href="<?= App::$path ?>price_request_clt/addart/<?= $pr_clt->id ?>" class="btn btn-success">
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
				<a href="<?= App::$path ?>price_request_clt/printdetails/<?= $pr_clt->id ?>" target="_blank" class="btn btn-default">
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
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($pr_clt_arts as $pr_clt_art): ?>
				<tr>
					<td class="table-actions">
				<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_articles): ?>
					<a href="<?= App::$path ?>price_request_clt/editart/<?= $pr_clt_art->pr_clt_id ?>/<?= $pr_clt_art->id ?>" class="btn btn-warning btn-xs">تعديل</a>
						<a href="#" class="btn btn-danger btn-xs" pr_clt_art_id="<?= $pr_clt_art->id ?>" onclick="deleteElementArt(this, event);">حذف</a>

				<?php endif; ?>
					</td>
					<td><?= $pr_clt_art->art_id ?></td>
					<td><?= $pr_clt_art->ref ?></td>
					<td><?= $pr_clt_art->desig ?></td>
					<td><?= $pr_clt_art->qte ?></td>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div>

	</section>
