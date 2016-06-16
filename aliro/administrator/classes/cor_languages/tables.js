
    function dump(variable) {
        if(!variable) return false;
        debugwin = window.open('', 'debugwin', 'left=0,top=0,width=300,height=700,scrollbars=yes,status=no,resizable=yes');
        debugwin.document.write("<html><head><title>Javascript Debug Window</title></head><body>");
        debugwin.document.write("Variable information for variable: "+typeof(variable)+" ("+variable.constructor+")<br />");
        for (var prop in variable) {
            debugwin.document.writeln('"'+prop+'" => ' + variable[prop] + '<br /> ');
        }
        debugwin.document.close();
    }
    function isset(variable){return(typeof(window.variable)!='undefined');}

    function Table(tblid)
    {
        var tbl = document.getElementById(tblid);
        var rows = tbl.tBodies[0].rows;
        var active_row = null;
        var handlers = new Array();
        tbl.init = function() {
            for (var i = 0; i < rows.length; i++) {
                rows[i].parity    = i % 2;
                rows[i].default_class = rows[i].className;
                rows[i].onmouseover =  function(){
                    if (active_row != this) {
                        this.style.backgroundColor = '#6699cc';
                        this.className = 'hover';
                    }
                };
                rows[i].onmouseout  =  function () {
                    if (active_row != this) {
                        this.style.backgroundColor = this.parity ? '#F5F5F5' : '#ffffff';
                        this.className = this.default_class;
                    }
                };
                rows[i].onmousedown =  function () {
                    if (active_row == this) {
                        active_row = null;
                        this.onmouseover();
                    }
                    else {
                        var last_active = active_row;
                        active_row = this;
                        if (last_active != null) {
                            last_active.onmouseout();
                        }
                        active_row.style.backgroundColor = '#4C7DAB';
                        active_row.className = 'active';
                    }
                };
                rows[i].ischecked  =  function() {
                    var checkbox = this.getElementsByTagName('input')[0];
                    if (checkbox.checked) {
                        checkbox.checked = false;
                        document.adminForm.boxchecked.value = 0;
                    } else {
                        checkbox.checked = true;
                        document.adminForm.boxchecked.value = 1;
                    }
                };
                rows[i].onmouseout();
            }
        }

        this.makeSortable = function(idxSort,sortProps){
            var arrMethods = sortProps.split(",");
            var arrHead = tbl.getElementsByTagName('thead')[0].getElementsByTagName('th');
            var arrow = document.createElement("span");
            for(var i=0;i<arrHead.length;i++){
                if(arrHead[i].className == 'input') {
                    continue;
                }
                arrHead[i].onclick =  function(){
                    intCol = this.cellIndex;
                    strMethod = arrMethods[intCol];
                    var arrRows = rows;
                    intDir = (arrHead[intCol].className=="asc")?-1:1;
                    arrHead[intCol].className = (arrHead[intCol].className=="asc")?"des":"asc";
                    txt = getInnerText(this);
                    arrow.innerHTML = (intDir > 0) ? '&nbsp;&nbsp;&uarr;' : '&nbsp;&nbsp;&darr;';
                    this.appendChild(arrow);
                    for(var i=0;i<arrHead.length;i++){
                    if(i!=intCol){arrHead[i].className="";}
                    }
                    var arrRowsSort = new Array();
                    for(var i=0;i<arrRows.length;i++){
                        arrRowsSort[i]=arrRows[i].cloneNode(true);
                        if(active_row == arrRows[i]) {
                            active_row = arrRowsSort[i];
                        }
                    }
                    arrRowsSort.sort(sort2dFnc);
                    for(var i=0;i<arrRows.length;i++){
                        arrRows[i].parentNode.replaceChild(arrRowsSort[i],arrRows[i]);
                    }
                    this.parentNode.parentNode.parentNode.init();
                }
            }
        }


        function getInnerText(el) {
        if (typeof el == "string") {return el};
        if (typeof el == "undefined") { return el };
            if (el.innerText) return el.innerText;
            var str = "";
            var cs = el.childNodes;
            for (var i = 0; i < cs.length; i++) {
                switch (cs[i].nodeType) {
                    case 1: //ELEMENT_NODE
                    str += getInnerText(cs[i]);
                    break;
                    case 3:    //TEXT_NODE
                    str += cs[i].nodeValue;
                    break;
                }
            }
            return str;
        }

        function sort2dFnc(a,b){
            var col = intCol;
            var dir = intDir;
            var aCell = getInnerText(a.getElementsByTagName("td")[col]);
            var bCell = getInnerText(b.getElementsByTagName("td")[col]);

            switch (strMethod){
                case "int":
                    aCell = parseInt(aCell);
                    bCell = parseInt(bCell);
                break;
                case "float":
                    aCell = parseFloat(aCell);
                    bCell = parseFloat(bCell);
                break;
                case "date":
                    aCell = new Date(aCell);
                    bCell = new Date(bCell);
                break;
            }
            return (aCell>bCell)?dir:(aCell<bCell)?-dir:0;
        }
        tbl.init();
    }
