<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> إضافة صنف جديد</span>
</section>
<section class="content">
    <form method="post" name="form-category-add" id="form-category-add" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-12">
                <?=  $form->input('category', 'اسم الصنف', [
                    'type' => 'text',
                    'placeholder' => 'اسم الصنف',
                    'data-validation' => 'length',
                    'data-validation-length' => '3-255',
                    'data-validation-error-msg' => 'اسم الصنف يجب أن يتراوح ما بين 3 و 255 حرف.'
                ]); ?>

            </div>
            <div class="col-lg-12 form-group text-center">
                <hr>
                <button type="submit" id="btn-save-category" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </form>
</section>
