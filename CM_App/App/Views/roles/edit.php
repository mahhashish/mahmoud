<?php
    if (isset($_GET['id'])){
        $title = 'تعديل حق الولوج';
    }
    else{
        $title = 'إضافة حق الولوج جديد';

    }
?>
<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> <?= $title ?></span>
</section>
<section class="content">
    <form method="post" name="form-role-add" id="form-role-add" enctype="multipart/form-data">
        <div class="col-sm-12">
            <?= $form->input('role_name', 'اسم حق الولوج',
                [
                    'type' => 'text',
                    'placeholder' => 'اسم حق الولوج',
                    'data-validation' => 'length',
                    'data-validation-length' => '1-100',
                    'data-validation-error-msg' => 'حق الولوج يجب أن يتراوح بين 1 و 100 حرف.'
                ]
            ); ?>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>الزبناء</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>العرض : </label>
                        <select name="show_clients" class="form-control"
                        data-validation="required"
                        data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->show_clients)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الإضافة، التعديل والحذف : </label>
                        <select name="aed_clients" class="form-control"
                        data-validation="required"
                        data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->aed_clients)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>الموردين</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>العرض : </label>
                        <select name="show_suppliers" class="form-control"
                        data-validation="required"
                        data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->show_suppliers)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الإضافة، التعديل والحذف : </label>
                        <select name="aed_suppliers" class="form-control"
                        data-validation="required"
                        data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->aed_suppliers)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>المبيعات</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>العرض : </label>
                        <select name="show_sales" class="form-control"
                        data-validation="required"
                        data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->show_sales)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الإضافة، التعديل والحذف : </label>
                        <select name="aed_sales" class="form-control"
                        data-validation="required"
                        data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->aed_sales)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>المشتريات</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>العرض : </label>
                        <select name="show_purchases" class="form-control"
                                data-validation="required"
                                data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->show_purchases)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الإضافة، التعديل والحذف : </label>
                        <select name="aed_purchases" class="form-control"
                                data-validation="required"
                                data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->aed_purchases)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>المنتجات</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>العرض : </label>
                        <select name="show_articles" class="form-control"
                                data-validation="required"
                                data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->show_articles)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الإضافة، التعديل والحذف : </label>
                        <select name="aed_articles" class="form-control"
                                data-validation="required"
                                data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->aed_articles)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>المخازن</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>العرض : </label>
                        <select name="show_stock" class="form-control"
                                data-validation="required"
                                data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->show_stock)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>المستخدمين وحقوق الولوج</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>العرض : </label>
                        <select name="show_users_roles" class="form-control"
                                data-validation="required"
                                data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->show_users_roles)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الإضافة، التعديل والحذف : </label>
                        <select name="aed_users_roles" class="form-control"
                                data-validation="required"
                                data-validation-error-msg="المرجو تحديد حق الولوج">
                            <?php if(isset($role) && ($role->aed_users_roles)) { ?>
                                <option value="0">لا</option>
                                <option value="1" selected>نعم</option>
                            <?php } else { ?>
                                <option value="0" selected>لا</option>
                                <option value="1">نعم</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 form-group text-center">
            <hr>
            <button id="btn-role-add" name="btn-role-add" class="btn btn-primary">حفظ</button>
        </div>
    </form>
</section>
