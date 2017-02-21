
<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> تعديل البروفايل</span>
</section>
<section class="content">
    <?php if($errors): ?>
        <div class="form-error alert alert-danger">
            <span>عفوا.. اسم لمستخدم وكلمة المرور غير متناسقان.</span>
        </div>
    <?php endif; ?>

    <form method="post" name="form-profile-edit" id="form-profile-edit" enctype="multipart/form-data">

        <div class="col-sm-9">
            <?= $form->input('login', 'اسم المستخدم',
                [
                    'type' => 'text',
                    'id' => 'login',
                    'autofocus' => 'autofocus',
                    'placeholder' => 'اسم المستخدم',
                    'data-validation' => 'custom',
                    'data-validation-regexp' => '^([a-zA-Z0-9]+)$',
                    'data-validation-length' => '2-50',
                    'data-validation-error-msg' => 'يجب أن يتكون اسم المستخدم من حروف لاتينية و أرقام فقط.'
                ]
            ); ?>
            <?= $form->input('email', 'الإيميل',
                [
                    'type' => 'text',
                    'placeholder' => 'الإيميل',
                    'data-validation' => 'email',
                    'data-validation-optional' => 'true',
                    'data-validation-error-msg' => 'قد أدخلت الايميل بصيغة خاطئة'
                ]
            ); ?>
            <?= $form->input('fname', 'الإسم الأول',
                [
                    'type' => 'text',
                    'placeholder' => 'الإسم الأول',
                    'data-validation' => 'length',
                    'data-validation-length' => '2-50',
                    'data-validation-error-msg' => 'الإسم الأول يجب أن يتكون من 2 إلى 50 حرف فقك.'
                ]
            ); ?>
            <?= $form->input('lname', 'الإسم الثاني',
                [
                    'type' => 'text',
                    'placeholder' => 'الإسم الأول',
                    'data-validation' => 'length',
                    'data-validation-length' => '2-50',
                    'data-validation-error-msg' => 'الإسم الثاني يجب أن يتكون من 2 إلى 50 حرف فقك.'
                ]
            ); ?>
            <?= $form->input('phone', 'الهاتف',
                [
                    'type' => 'text',
                    'placeholder' => 'الهاتف',
                    'data-validation' => 'length',
                    'data-validation-length' => '2-50',
                    'data-validation-optional' => 'true',
                    'data-validation-error-msg' => 'صيغة الهاتف غير صحيحة.'
                ]
            ); ?>
            <hr>
            <?= $form->input('current_pass', 'كلمة المرور الحالية',
                [
                    'type' => 'password',
                    'id' => 'current_pass',
                    'placeholder' => 'كلمة المرور الحالية',
                    'data-validation' => 'length',
                    'data-validation-length' => '3-100',
                    'data-validation-error-msg' => 'كلمة المرور الحالية يحب ألا تقل على 3 حروف ولا تتجاوز 100 حرف'
                ]
            ); ?>
            <?= $form->input('new_pass_confirmation', 'كلمة المرور',
                [
                    'type' => 'password',
                    'id' => 'new_pass_confirmation',
                    'placeholder' => 'كلمة المرور',
                    'data-validation' => 'length',
                    'data-validation-length' => '3-100',
                    'data-validation-error-msg' => 'كلمة المرور يحب ألا تقل على 3 حروف ولا تتجاوز 100 حرف'
                ]
            ); ?>
            <?= $form->input('new_pass', 'تأكيد كلمة المرور',
                [
                    'type' => 'password',
                    'id' => 'new_pass',
                    'placeholder' => 'تأكيد كلمة المرور',
                    'data-validation' => 'confirmation',                                                      'data-validation-error-msg' => 'كلمة المرور غير متطابقة'
                ]
            ); ?>

        </div>
        <div class="col-sm-3">
            <div class="box-infos-search">
                <section class="content-header box-infos-header">
                    <span class="content-title"><i class="fa fa-image"></i> الصورة</span>
                    <a href="#" class="btn btn-default btn-search" onclick="triggerInputFile('avatar', event);">
                        <i class="fa fa-search"></i>
                    </a>

                </section>
                <div class="box-infos text-center">
                    <img class="thumb-preview" src="img/avatar/<?= isset($user) ? $user->avatar : '0.jpg' ?>">
                    <a href="#" class="badge thumb-reset" <?php
                    if(isset($user) && ($user->avatar != '0.jpg')) {
                        echo 'style="display: inline-block;"';
                    }
                    ?> onclick="resetAvatar(this,  event);">Reset</a>

                    <?=  $form->file('avatar', [
                        'type' => 'file',
                        'id' => 'avatar',
                        'class' => 'hidden-input-file',
                        'onchange' => 'readUrl(this);',
                        'data-validation' => 'required mime size',
                        'data-validation-allowing' => 'jpg',
                        'data-validation-error-msg' => 'الصورة ضرورية وبحجم لا يزيد عن 1M من نوع jpg'
                    ]); ?>
                </div>
            </div>

        </div>
        <div class="col-lg-12 form-group text-center">
            <hr>
            <button id="btn-profile-edit" name="btn-profile-edit" class="btn btn-primary">حفظ</button>
        </div>
    </form>
</section>
