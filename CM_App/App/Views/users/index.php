	<section class="content-header">
		<span class="content-title"><i class="fa fa-home"></i> المستخدمين</span>
		<ul class="header-btns">
			<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_users_roles): ?>
			<li>
				<a href="<?= App::$path ?>user/add" class="btn btn-success">
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
				<a href="<?= App::$path ?>user/printlist" target="_blank" class="btn btn-default">
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
					<form method="post" name="form-user-search" id="form-user-search">
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
					<button type="submit" id="btn-search-user" class="btn btn-primary">ابحث</button>
				</div>
			</form>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table main-table rtl_table data-table table-striped table-hover">
			<tbody>
			<?php
			foreach($users as $user): ?>
				<tr>
					<td class="table-actions">
						<a href="<?= $user->url ?>" class="btn btn-success btn-xs">عرض</a>
				<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_users_roles): ?>
					<a href="<?= App::$path ?>user/edit/<?= $user->id ?>" class="btn btn-warning btn-xs">تعديل</a>
						<?php if($user->id > 1){ ?>
						<a href="#" class="btn btn-danger btn-xs" user_id="<?= $user->id ?>" onclick="deleteElement(this, event);">حذف</a>
					<?php } else { ?>
							<span class="badge">أدمن</span>
					<?php }  ?>
					<?php endif; ?>
						</td>
					<td>
						<a href="<?= $user->url ?>">
							<img class="avatar" src="<?= App::$path ?>img/avatar/<?= $user->avatar ?>">
						</a>
					</td>
					<td>
						<h4><a href="<?= App::$path ?>user/profile/<?= $user->id ?>"><?= $user->fname.' '. $user->lname ?></a></h4>
						<p>اسم المستخدم : <?= $user->login ?></p>
					</td>
					<td>
						<p>الوظيفة : <?= $user->function ?></p>
						<p>حق الولوج : <?= $user->role_name ?></p>
					</td>
					<td>
						<p><?= $user->phone ?></p>
						<p><?= $user->email ?></p>
					</td>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div>

	</section>
