<?php
    if (isset($article) && isset($_GET['id'])){
        $thumb_name = $article->thumb;
    }
    else{
        $thumb_name = 'no_thumb.jpg';
    }
?>
<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> إضافة منتج جديد</span>
</section>
<section class="content">
    <form method="post" name="form-article-add" id="form-article-add" enctype="multipart/form-data">
        <div class="row form-add-top">
            <div class="col-sm-8 col-xs-12">
                <div class="box-infos-search">
                    <section class="content-header box-infos-header">
                        <span class="content-title"><i class="fa fa-home"></i> المورد</span>
                        <a href="#" class="btn btn-default btn-search btn-infos-search">
                            <i class="fa fa-search"></i>
                        </a>
                    </section>
                    <div class="box-infos">
                        <?php if(isset($article)){ ?>
                        <input type="hidden" value="<?= $article->supplier_id ?>" name="box-infos-id" class="box-infos-id">
                        <h3 class="box-infos-name"><?= $article->name ?></h3>
                        <p class="box-infos-city"><?= $article->city ?></p>
                        <p class="box-infos-address"><?= $article->address ?></p>
                        <?php } else { ?>
                            <input type="hidden" name="box-infos-id" class="box-infos-id">
                            <h3 class="box-infos-name"></h3>
                            <p class="box-infos-city"></p>
                            <p class="box-infos-address"></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xs-12">
                <div class="box-infos-search">
                    <section class="content-header box-infos-header">
                    <span class="content-title"><i class="fa fa-image"></i> الصورة</span>
                        <a href="#" class="btn btn-default btn-search" onclick="triggerInputFile('thumb', event);">
                            <i class="fa fa-search"></i>
                        </a>

                    </section>
                    <div class="box-infos text-center">
                        <img class="thumb-preview" src="<?= App::$path ?>img/thumbs/articles/<?= $thumb_name ?>">
                        <a href="#" class="badge thumb-reset" onclick="resetThumb( event);">Reset</a>

                        <?=  $form->file('thumb', [
                            'type' => 'file',
                            'id' => 'thumb',
                            'class' => 'hidden-input-file',
                            'onchange' => 'readUrl(this);',
                            'data-validation' => 'required mime size',
                            'data-validation-allowing' => 'jpg',
                            'data-validation-error-msg' => 'الصورة ضرورية وبحجم لا يزيد عن 1M من نوع jpg'
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?=  $form->input('ref', 'رمز المنتج', [
                    'type' => 'text',
                    'placeholder' => 'رمز المنتج',
                    'data-validation' => 'length',
                    'data-validation-length' => 'max100',
                    'data-validation-error-msg' => 'رمز المنتج يجب ألا يتجاوز 100 حرف'
                ]); ?>
                <?=  $form->input('desig', 'اسم المنتج', [
                    'type' => 'text',
                    'placeholder' => 'اسم المنتج',
                    'data-validation' => 'length',
                    'data-validation-length' => '3-255',
                    'data-validation-error-msg' => 'اسم المنتج يجب أن يتراوح ما بين 3 و 255 حرف.'
                ]); ?>
                <?=  $form->select('category_id', 'الصنف', $categories); ?>
                <?=  $form->select('unit_id', 'الوحدة', $units); ?>
                <?=  $form->select('tva', 'TVA (%)', $tva); ?>
            </div>
            <div class="col-lg-12 form-group text-center">
                <hr>
                <button type="submit" id="btn-save-article" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </form>
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
