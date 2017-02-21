$(document).ready(function(){
    var myLanguage = {
        errorTitle: 'عفوا، هناك أخطاء!'
    };

    $.validate({
        form : '#form-pr_clt-add',
        language : myLanguage,
        modules : 'file',
        validateOnBlur : false,
        errorMessagePosition : 'top',
        onError : function() {
            //alert('alert !');
        },
        onSuccess : function() {
            //alert('success!');
        }
    });
    $.validate({
        form : '#form-pr-article-add',
        language : myLanguage,
        modules : 'file',
        validateOnBlur : false,
        errorMessagePosition : 'top',
        onError : function() {
            //alert('alert !');
        },
        onSuccess : function() {
            //alert('success!');
        }
    });

    $.validate({
        form : '#form-ppr_clt-search',
        language : myLanguage,
        validateOnBlur : false,
        errorMessagePosition : 'top',
        onError : function() {
            //alert('alert !');
        },
        onSuccess : function() {
            //alert('success!');
            searchArticles();
        }
    });

});

    function deleteElement(btn, e) {
        e.preventDefault();
        if (confirm("هل تريد فعلا حذف هذا العنصر؟")) {
            var obj = {
                ajax_action: 'price_request_clt.delete',
                pr_clt_id: $(btn).attr('pr_clt_id')
            };
            $.post(
                '/CM_App/public/index.php',
                obj,
                function (data) {

                    if (data == 1) {
                        window.location.reload();
                    } else {
                        alert("واجهتا مشاكل، المرجو المحاولة.");
                    }
                },
                'html'
            );
        }
    }
    function deleteElementArt(btn, e) {
        e.preventDefault();
        if (confirm("هل تريد فعلا حذف هذا العنصر؟")) {
            var obj = {
                ajax_action: 'price_request_clt.deleteArt',
                pr_clt_art_id: $(btn).attr('pr_clt_art_id')
            };
            $.post(
                '/CM_App/public/index.php',
                obj,
                function (data) {

                    if (data == 1) {
                        window.location.reload();
                    } else {
                        alert("واجهتا مشاكل، المرجو المحاولة.");
                    }
                },
                'html'
            );
        }
    }

    function LoadClients(e){
        e.preventDefault();
        var obj = {
            ajax_action : 'client.modal'
        };
        $.post(
            '/CM_App/public/index.php',
            obj,
            function(data){
                $('#myModal').modal('show');
                $('.modal-content .modal-body .table-responsive table tbody').html(data);
            },
            'html'
        );
    }
    function loadArticles(e){
        e.preventDefault();
        var obj = {
            ajax_action : 'article.modal'
        };
        $.post(
            '/CM_App/public/index.php',
            obj,
            function(data){
                $('#myModal').modal('show');
                $('.modal-content .modal-body .table-responsive table tbody').html(data);
            },
            'html'
        );
    }

function selectArticle(btn, e){
    e.preventDefault();
    var tr = $(btn).parent().parent();
    $('.box-infos-id').val($(btn).attr('article_id'));
    $('.box-infos-desig').text($(tr).children('.article_desig').text());
    $('.box-infos-ref').text($(tr).children('.article_ref').text());
    $('#myModal').modal('hide');

}
function selectClient(btn, e){
    e.preventDefault();
    var tr = $(btn).parent().parent();
    $('.box-infos-id').val($(btn).attr('client_id'));
    $('.box-infos-name').text($(tr).children('.client_name').text());
    $('.box-infos-city').text($(tr).children('.client_city').text());
    $('.box-infos-address').text($(tr).children('.client_address').text());
    $('#myModal').modal('hide');

}