<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> إضافة وحدة جديدة</span>
</section>
<section class="content">
    <form method="post" name="form-unit-add" id="form-unit-add" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-12">
                <?=  $form->input('unit', 'اسم الوحدة', [
                    'type' => 'text',
                    'placeholder' => 'اسم الوحدة',
                    'data-validation' => 'length',
                    'data-validation-length' => '1-20',
                    'data-validation-error-msg' => 'اسم الوحدة يجب أن يتراوح ما بين 1 و 20 حرف.'
                ]); ?>

            </div>
            <div class="col-lg-12 form-group text-center">
                <hr>
                <button type="submit" id="btn-save-unit" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </form>
</section>
