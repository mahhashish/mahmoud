<section class="content-header">
    <span class="content-title"><i class="fa fa-edit"></i> إضافة طلب أثمنة</span>
</section>
<section class="content">
    <form method="post" name="form-pr_clt-add" id="form-pr_clt-add" enctype="multipart/form-data">
        <div class="row form-add-top">
            <div class="col-xs-12">
                <div class="box-infos-search">
                    <section class="content-header box-infos-header">
                        <span class="content-title"><i class="fa fa-home"></i> العميل</span>
                        <a href="#" class="btn btn-default btn-search btn-infos-search" onclick="LoadClients(event);">
                            <i class="fa fa-search"></i>
                        </a>
                    </section>
                    <div class="box-infos">
                        <?php if(isset($pr_clt)){ ?>
                        <input type="hidden" value="<?= $pr_clt->client_id ?>" name="box-infos-id" class="box-infos-id">
                        <h3 class="box-infos-name"><?= $pr_clt->client_name ?></h3>
                        <p class="box-infos-city"><?= $pr_clt->city ?></p>
                        <p class="box-infos-address"><?= $pr_clt->address ?></p>
                        <?php } else { ?>
                            <input type="hidden" name="box-infos-id" class="box-infos-id">
                            <h3 class="box-infos-name"></h3>
                            <p class="box-infos-city"></p>
                            <p class="box-infos-address"></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?=  $form->input('num', 'الرقم', [
                    'type' => 'text',
                    'placeholder' => 'الرقم',
                    'data-validation' => 'length',
                    'data-validation-length' => '1-100',
                    'data-validation-error-msg' => 'الرقم يجب ألا يتجاوز 100 حرف.'
                ]); ?>
                <?=  $form->input('dt', 'التاريخ', [
                    'type' => 'text',
                    'value' => date('d-m-Y'),
                    'placeholder' => 'التاريخ',
                    'data-validation' => 'date',
                    'data-validation-format' => 'dd-mm-yyyy',
                    'data-validation-error-msg' => 'المرجو تحديد التاريخ بضيغة صحيحة: dd-mm-yyy'
                ]); ?>
                <?=  $form->input('subject', 'الموضوع', [
                    'type' => 'text',
                    'placeholder' => 'الموضوع',
                    'data-validation' => 'length',
                    'data-validation-length' => 'max255',
                    'data-validation-optional' => 'true',
                    'data-validation-error-msg' => 'الموضوع يجب ألا يتجاوز 255 حرف.'
                ]); ?>
                <?=  $form->textarea('discr', 'ملاحظات', [
                    'type' => 'text',
                    'placeholder' => 'ملاحظات',
                    'data-validation' => 'length',
                    'data-validation-length' => 'max255',
                    'data-validation-optional' => 'true',
                    'data-validation-error-msg' => 'الملاحظات يجب ألا يتجاوز 255 حرف.'
                ]); ?>

            </div>
            <div class="col-lg-12 form-group text-center">
                <hr>
                <button type="submit" id="btn-save-pr_clt" class="btn btn-primary">حفظ</button>
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
                <h4 class="modal-title" id="myModalLabel">العملاء</h4>
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
