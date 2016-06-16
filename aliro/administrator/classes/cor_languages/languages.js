

function update(value, is_plural, nplural)
{
    var row_id = document.getElementById('row_id');
    if(is_plural) {
        var trans = document.getElementById('msgstr_'+nplural+'_'+row_id.value);
        trans.value = value;
        if(nplural == 0) display = value;
    } else {
        var trans = document.getElementById('msgstr_'+row_id.value);
        var transspan = document.getElementById('msgstr_'+row_id.value+'_span');
        trans.value = value;
        display = value;
    }    
    transspan = document.getElementById('msgstr_'+row_id.value+'_span');
    transspan.innerHTML = display;
}
function translate(id, is_plural, nplurals){

    var singular   = document.getElementById('singular');
    var plural     = document.getElementById('plural');
    var row_id     = document.getElementById('row_id');
    row_id.value   = id;
    if(is_plural) {
        plural.style.display = 'block';
        singular.style.display = 'none';
        msgid      = document.getElementById('msgid_'+id);
        original   = document.getElementById('p_msgid');
        original.value = msgid.value;
        msgid_plural    = document.getElementById('msgid_plural_'+id);
        original_plural = document.getElementById('p_msgid_plural');
        original_plural.value = msgid_plural.value;
        for(a = 0; a < nplurals; a++) {
            msgstr = document.getElementById('msgstr_'+a+'_'+id);
            translation  = document.getElementById('p_msgstr_'+a);
            translation.value = msgstr.value
        }
    } else {
        plural.style.display = 'none';
        singular.style.display = 'block';
        msgid      = document.getElementById('msgid_'+id);
        original   = document.getElementById('s_msgid');
        original.value = msgid.value;
        msgstr      = document.getElementById('msgstr_'+id);
        translation = document.getElementById('s_msgstr');
        translation.value = msgstr.value;
    }
    
}

function togglefuzzy(img, checkbox) {
    if(checkbox.value == 'false') {
        checkbox.value = 'true';
        img.src = 'images/publish_x.png';
    } else if(checkbox.value == 'true') {        
        checkbox.value = 'false';
        img.src = 'images/tick.png';
    }
}
