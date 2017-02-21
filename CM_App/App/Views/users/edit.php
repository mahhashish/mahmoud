<?php
    if (isset($_GET['id'])){
        $title = 'تعديل مستخدم';
    }
    else{
        $title = 'إضافة مستخدم جديد';

    }
?>
<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> <?= $title ?></span>
</section>
<section class="content">
    <form method="post" name="form-user-add" id="form-user-add" enctype="multipart/form-data">
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
            <p>كلمة المرور الإفتراضية: </p>
            <p id="default-pwd" class="form-control"></p>
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
            <?= $form->input('function', 'الوظيفة',
                [
                    'type' => 'text',
                    'placeholder' => 'الوظيفة',
                    'data-validation' => 'length',
                    'data-validation-length' => '1-100',
                    'data-validation-optional' => 'true',
                    'data-validation-error-msg' => 'الحقل الوظيفة يجب أن يتراوح من 1 الى 100 حرف.'
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
            <?= $form->select('role_id', 'حق الولوج', $roles,
                [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'المرجو اختيار حق الولوج.'
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
                    <img class="thumb-preview" src="<?= App::$path ?>img/avatar/<?= isset($user) ? $user->avatar : '0.jpg' ?>">
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
        <div class="col-lg-12 form-group forum-cmds text-center">
            <hr>
            <button id="btn-user-add" name="btn-user-add" class="btn btn-primary">حفظ</button>
        </div>
    </form>
</section>
