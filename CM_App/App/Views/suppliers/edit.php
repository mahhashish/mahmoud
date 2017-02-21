
<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> إضافة - تعديل مورد</span>
</section>
<section class="content">
    <form method="post" name="form-supplier-add" id="form-supplier-add" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-12">
                <?=  $form->input('name', 'اسم المورد', [
                    'type' => 'text',
                    'placeholder' => 'سم المورد',
                    'data-validation' => 'length',
                    'data-validation-length' => '1-255',
                    'data-validation-error-msg' => 'المرجو تحديد اسم المورد'
                ]); ?>
                <?=  $form->input('tel', 'الهاتف', [
                    'type' => 'text',
                    'placeholder' => 'الهاتف',
                    'data-validation' => 'length',
                    'data-validation-length' => '1-255',
                    'data-validation-error-msg' => 'المرجو تحديد الهاتف'
                ]); ?>
                <?=  $form->input('email', 'الإيميل', [
                    'type' => 'text',
                    'placeholder' => 'الإيميل',
                    'data-validation' => 'email',
                    'data-validation-error-msg' => 'المرجو كتابة الإيميل بصيغة صحيحة'
                ]); ?>
                <?=  $form->input('zip_code', 'رمز المدينة', [
                    'type' => 'text',
                    'placeholder' => 'رمز المدينة',
                    'data-validation' => 'number',
                    'data-validation-optional' => 'true',
                    'data-validation-error-msg' => 'المرجو كتابة رمز المدينة على شكل أرقام'
                ]); ?>
                <?=  $form->input('city', 'اسم المدينة', [
                    'type' => 'text',
                    'placeholder' => 'اسم المدينة',
                    'data-validation' => 'length',
                    'data-validation-length' => '1-255',
                    'data-validation-error-msg' => 'المرجو تحديد اسم المدينة'
                ]); ?>
                <?=  $form->input('address', 'العنوان', [
                    'type' => 'text',
                    'placeholder' => 'العنوان',
                    'data-validation' => 'length',
                    'data-validation-length' => '1-255',
                    'data-validation-error-msg' => 'المرجو تحديد العنوان'
                ]); ?>
            </div>
            <div class="col-lg-12 form-group text-center">
                <hr>
                <button type="submit" id="btn-save-supplier" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </form>
</section>
