	<section class="content-header">
		<span class="content-title"><i class="fa fa-home"></i> حقوق الولوج</span>
		<ul class="header-btns">
			<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_users_roles): ?>
			<li>
				<a href="<?= App::$path ?>role/add" class="btn btn-success">
					<i class="fa fa-plus-circle"></i>
					<span class="hidden-xs hidden-sm"> إضافة</span>
				</a>
			</li>
			<?php endif; ?>
		</ul>
	</section>
	<section class="content">
		<div class="table-responsive">
			<table class="table main-table rtl_table data-table table-striped table-hover">
			<tbody>
			<?php
			foreach($roles as $role): ?>
				<tr>
					<td class="table-actions">
				<?php if(isset($_SESSION['user']) && $_SESSION['user']->aed_users_roles): ?>
					<a href="<?= App::$path ?>role/edit/<?= $role->id ?>" class="btn btn-warning btn-xs">تعديل</a>
						<?php if($role->id > 1){ ?>
						<a href="#" class="btn btn-danger btn-xs" role_id="<?= $role->id ?>" onclick="deleteElement(this, event);">حذف</a>
					<?php } else { ?>
							<span class="badge">أدمن</span>
					<?php }  ?>
					<?php endif; ?>
					</td>
					<td>
						<h4><?= $role->role_name ?></h4>
					</td>
					<td class="td-roles">
						<p>
							<span>الزبناء : </span>
							<?php if($role->aed_clients){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-plus-circle" title="حق الإضافة"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-plus-circle"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } ?>

							<?php if($role->show_clients){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-eye"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-eye"></i>
							</span>
							<?php } ?>
						</p>
						<p>
							<span>الموردين : </span>
							<?php if($role->aed_suppliers){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-plus-circle" title="حق الإضافة"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-plus-circle"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } ?>

							<?php if($role->show_suppliers){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-eye"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-eye"></i>
							</span>
							<?php } ?>
						</p>
						<p>
							<span>المبيعات : </span>
							<?php if($role->aed_sales){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-plus-circle" title="حق الإضافة"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-plus-circle"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } ?>

							<?php if($role->show_sales){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-eye"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-eye"></i>
							</span>
							<?php } ?>
						</p>
						<p>
							<span>المشتريات : </span>
							<?php if($role->aed_purchases){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-plus-circle" title="حق الإضافة"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-plus-circle"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } ?>

							<?php if($role->show_purchases){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-eye"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-eye"></i>
							</span>
							<?php } ?>
						</p>
						<p>
							<span>المنتجات : </span>
							<?php if($role->aed_articles){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-plus-circle"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-plus-circle"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } ?>

							<?php if($role->show_articles){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-eye"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-eye"></i>
							</span>
							<?php } ?>
						</p>
						<p>
							<span>النخازن : </span>
							<?php if($role->show_stock){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-eye"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-eye"></i>
							</span>
							<?php } ?>
						</p>
						<p>
							<span>المستخدمين وحقوق الولوج : </span>
							<?php if($role->aed_users_roles){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-plus-circle" title="حق الإضافة"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-plus-circle"></i>
								<i class="fa fa-edit"></i>
								<i class="fa fa-remove"></i>
							</span>
							<?php } ?>

							<?php if($role->show_users_roles){ ?>
								<span class="fa-roles-true">
								<i class="fa fa-eye"></i>
							</span>
							<?php } else { ?>
								<span class="fa-roles-false">
								<i class="fa fa-eye"></i>
							</span>
							<?php } ?>
						</p>
					</td>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div>

	</section>
