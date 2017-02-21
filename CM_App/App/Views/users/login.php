<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> تسجيل الدخول</span>
</section>
<section class="content">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <?php if($errors): ?>
        <div class="form-error alert alert-danger">
            <span>عفوا.. اسم لمستخدم وكلمة المرور غير متناسقان.</span>
        </div>
        <?php endif; ?>
        <form method="post" name="form-user-login" id="form-user-login" enctype="multipart/form-data">

        <div class="panel panel-primary">
            <div class="panel-heading login-header">
                <h3>تسجيل الدخول</h3>
            </div>
            <div class="panel-body">
                <?=  $form->input('login', '', [
                    'type' => 'text',
                    'placeholder' => 'اسم المستخدم',
                    'data-validation' => 'custom',
                    'data-validation-regexp' => '^([a-zA-Z0-9]+)$',
                    'data-validation-length' => 'max100',
                    'data-validation-error-msg' => 'اسم المستخدم يجب أن يكون عبارة عن حروف لاتينية أو أرقام ففط.'
                ]); ?>
                <?=  $form->input('pass', '', [
                    'type' => 'password',
                    'placeholder' => 'كلمة المرور',
                    'data-validation' => 'length',
                    'data-validation-length' => '3-255',
                    'data-validation-error-msg' => 'كلمة المرور  يجب أن يتراوح ما بين 3 و 255 حرف.'
                ]); ?>
                <div class="col-lg-12 form-group text-center">
                    <?=  $form->submit('btn-user-login', 'سجل الدخول', [
                        'id' => 'btn-user-login',
                    ]);
                    ?>
                </div>
        </div>
        </div>
        </form>
    </div>
    <div class="col-sm-4"></div>

</section>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">الموردين</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table rtl_table data-table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>الإسم</th>
                            <th>المدينة</th>
                            <th>العنوان</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
