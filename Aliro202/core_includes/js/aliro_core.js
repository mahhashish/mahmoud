/**
* @version $Id: aliro_core.js
* @package Aliro
* @copyright (C) 2007-2009 Aliro Software Limited, from code (c) The Mambo Foundation/Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Aliro is Free Software
*/

YUI.add('aliro-core', function(Y) {

    /**
    * Aliro Core Class:
    * Many of these functions have been relocated, and some cases rewritten, from alirojavascript.  Developers should now 
    * be calling these function through the an instance of AliroCore.  If backwards compatability is enabled then 
    * alirojavascript.js is included which will translate older functions to the new namespaced ones in most cases.  
    * However, some alirojavascript functions are not being migrated and thus will eventually go away (ex) mosDHTML, 
    * Calendar, etc.  
    */
    
    function AliroCore(config) {
        AliroCore.superclass.constructor.apply(this, arguments);
    }
    
    //Used to identify instances of this class
    AliroCore.NAME = "aliroCore";
    
    /**
    * Return lowercase name of running editor.  Supports MOStlyCE, Byte, and no editor.
    **/
    function editorCheck() {
        var editor = 'unknown';
        
        //Override if a common RTE is detected
        if (typeof tinyMCE !== "undefined") {
            editor = 'tinymce';
        } else if (typeof YUI.ALIRO['byte'] !== "undefined") { //Note: dot notation causes reserved word issues
            editor = 'byte';
        }

        return editor;
    }

    //"Associative Array", used to define the set of attributes 
    //added by this class. The name of the attribute is the key,
    //and the object literal value acts as the configuration 
    //object passed to addAttrs
    AliroCore.ATTRS = {
        editorInUse  : null,
        yui3version  : null,
        yui2version  : null
    };
    
    //Prototype methods
    Y.extend(AliroCore, Y.Base, {
        initializer : function() {
            this.set("yui2version", "2.8.0r4");
            this.set("yui3version", "3.0.0");
            this.set("editorInUse", editorCheck());

            Y.log("AliroCore has loaded!", "info");
        },
        /**
        * Load the ALIRO debug console (when in debug mode)
        */
        loadDebugConsole : function() {
            if (aliroDebugMode === '1') {
                var aliroDebugConsole = new Y.Console({
                    plugins: [ Y.Plugin.Drag ],
                    visible: true
                }).render();

                //Keep the console below the toolbar for less dragging
                Y.one(".yui-console").setStyle("top", "65px");
                
                Y.log("Aliro debug console has loaded!", "info");
            }
        },
        /**
        * Used to throw custom errors
        * @param condition A test condition that should be true
        * @param message The message to use if condition is false
        */
        assert: function(condition, message) {
            if (!condition) {
                if (aliroDebugMode === '1') {
                    Y.log(message, "error");
                }
                throw new Error(message);
            }
        },
        /**
        * Writes a dynamically generated list
        * @param string The parameters to insert into the <select> tag
        * @param array A javascript array of list options in the form [key,value,text]
        * @param string The key to display for the initial state of the list
        * @param string The original key that was selected
        * @param string The original item value that was selected
        */
        writeDynaList: function(selectParams, source, key, orig_key, orig_val) {
        	var x, 
        	    i    = 0,
        	    html = '\n	<select ' + selectParams + '>';

        	for (x in source) {
        	    if (source.hasOwnProperty(x)) {
        	        if (source[x][0] === key) {
            			var selected = '';
            			if ((orig_key === key && orig_val === source[x][1]) || (i === 0 && orig_key !== key)) {
            				selected = 'selected="selected"';
            			}
            			html += '\n		<option value="'+source[x][1]+'" '+selected+'>'+source[x][2]+'</option>';
            		}
            		i++;
        	    }
        	}
        	html += '\n	</select>';

        	document.writeln( html );
        },
        /**
        * Changes a dynamically generated list
        * @param string The name of the list to change
        * @param array A javascript array of list options in the form [key,value,text]
        * @param string The key to display
        * @param string The original key that was selected
        * @param string The original item value that was selected
        */
        changeDynaList: function(listname, source, key, orig_key, orig_val) {
        	var i, 
        	    x,
        	    list = document.adminForm[listname],
        	    listLen = list.options.length;

        	// empty the list
        	for (i = 0; i < listLen; i++ ) {
        		list.options[i] = null;
        	}

        	i = 0;
        	for (x in source) {
        		if (source[x][0] === key) {
        			var opt = new Option();
        			opt.value = source[x][1];
        			opt.text = source[x][2];

        			if ((orig_key === key && orig_val === opt.value) || i === 0) {
        				opt.selected = true;
        			}
        			list.options[i++] = opt;
        		}
        	}
        	list.length = i;
        },
        /**
        * Adds a select item(s) from one list to another
        */
        addSelectedToList: function(frmName, srcListName, tgtListName) {
            var i,
                form    = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
                srcList = Y.one('#' + srcListName) || form[srcListName],
                tgtList = Y.one('#' + tgtListName) || form[tgtListName],
                srcLen  = srcList.length,
                tgtLen  = tgtList.length,
                tgt     = "x";

        	//build array of target items
        	for (i = tgtLen - 1; i > -1; i--) {
        		tgt += "," + tgtList.options[i].value + ",";
        	}

        	//Pull selected resources and add them to list
        	for (i = srcLen - 1; i > -1; i--) {
        		if (srcList.options[i].selected && tgt.indexOf( "," + srcList.options[i].value + "," ) === -1) {
        			var opt = new Option( srcList.options[i].text, srcList.options[i].value );
        			tgtList.options[tgtList.length] = opt;
        		}
        	}
        },
        /**
        * Removes a select item(s) from one list
        */
        delSelectedFromList: function(frmName, srcListName) {
        	var form    = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
        	    srcList = Y.one('#' + srcListName) || form[srcListName],
        	    srcLen  = srcList.length;

        	for (var i=srcLen-1; i > -1; i--) {
        		if (srcList.options[i].selected) {
        			srcList.options[i] = null;
        		}
        	}
        },
        /**
        * Moves an item within a select list up/down
        */
        moveInList: function(frmName, srcListName, index, to) {
            var i,
                form    = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
                srcList = Y.one('#' + srcListName) || form[srcListName],
                total   = srcList.options.length-1,
                items   = [],
            	values  = [];

        	if (index === -1) {
        		return false;
        	}
        	if (to === +1 && index === total) {
        		return false;
        	}
        	if (to === -1 && index === 0) {
        		return false;
        	}

        	for (i=total; i >= 0; i--) {
        		items[i] = srcList.options[i].text;
        		values[i] = srcList.options[i].value;
        	}
        	for (i = total; i >= 0; i--) {
        		if (index === i) {
        			srcList.options[i + to] = new Option(items[i],values[i], 0, 1);
        			srcList.options[i] = new Option(items[i+to], values[i+to]);
        			i--;
        		} else {
        			srcList.options[i] = new Option(items[i], values[i]);
        	   }
        	}
        	srcList.focus();
        },
        /**
        * Identify which options within a select list was chosen
        */
        getSelectedOption: function(frmName, srcListName) {
        	var i,
        	    form    = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
        	    srcList = Y.one('#' + srcListName) || form[srcListName];

        	i = srcList.selectedIndex;
        	if (i !== null && i > -1) {
        		return srcList.options[i];
        	} else {
        		return null;
        	}
        },
        /**
        * Used to select item(s) within a select list
        */
        setSelectedValue: function(frmName, srcListName, value) {
        	var form    = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
        	    srcList = Y.one('#' + srcListName) || form[srcListName],
        	    srcLen  = srcList.length;

        	for (var i=0; i < srcLen; i++) {
        		srcList.options[i].selected = false;
        		if (srcList.options[i].value === value) {
        			srcList.options[i].selected = true;
        		}
        	}
        },
        /**
        * Identify which radio button was chosen
        */
        getSelectedRadio: function(frmName, srcGroupName) {
            var form     = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
                srcGroup = Y.one('#' + srcGroupName) || form[srcGroupName];

        	if (srcGroup[0]) {
        		for (var i=0, n=srcGroup.length; i < n; i++) {
        			if (srcGroup[i].checked) {
        				return srcGroup[i].value;
        			}
        		}
        	} else {
        		if (srcGroup.checked) {
        			return srcGroup.value;
        		} // if the one button is checked, return zero
        	}
           // if we get to this point, no radio button is selected
           return null;
        },
        /**
        * Identify which value was selected within a select list
        */
        getSelectedValue: function(frmName, srcListName) {
        	var form          = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
        	    srcList       = Y.one('#' + srcListName) || form.one('[name="' + srcListName + '"]');
        	    
        	this.assert(!Y.Lang.isNull(srcList), "Unable to locate the requested select list: " + srcListName);
        	    
        	return srcList.get("options").item(srcList.get("selectedIndex")).get("value");
        },
        /**
        * Identify text value of selected option within a select list
        */
        getSelectedText: function(frmName, srcListName) {
        	var form          = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
        	    srcList       = Y.one('#' + srcListName) || form.one('[name="' + srcListName + '"]');
        	    
        	this.assert(!Y.Lang.isNull(srcList), "Unable to locate the requested select list: " + srcListName);
        	    
        	return Y.Lang.trim(srcList.get("options").item(srcList.get("selectedIndex")).get("text"));
        },
        chgSelectedValue: function(frmName, srcListName, value) {
        	var form          = Y.one('#' + frmName) || Y.one('form[name="' + frmName + '"]'),
        	    srcList       = Y.one('#' + srcListName) || form.one('[name="' + srcListName + '"]');
        	    
            this.assert(!Y.Lang.isNull(srcList), "Unable to locate the requested select list: " + srcListName);
        	this.assert(!Y.Lang.isNull(srcList.get("selectedIndex")), "Unable to update the requested select list: " + srcListName + ". Option not selected!");
        	
        	srcList.get("options").item(srcList.get("selectedIndex")).set("value", value);
        },
        /**
        * Used to show existing attributes/properties for a selected image in image list
        */
        showImageProps: function(base_path) {
        	var srcImage,
        	    form  = Y.one('#adminForm') || Y.one('form[name="adminForm"]'),
        	    value = this.getSelectedValue( 'adminForm', 'imagelist' ),
        	    parts = value.split( '|' );

        	form._source.value = parts[0];

        	this.setSelectedValue( 'adminForm', '_align', parts[1] || '' );
        	form._alt.value = parts[2] || '';
        	form._border.value = parts[3] || '0';
        	form._caption.value = parts[4] || '';
        	this.setSelectedValue( 'adminForm', '_caption_position', parts[5] || '' );
        	this.setSelectedValue( 'adminForm', '_caption_align', parts[6] || '' );
        	form._width.value = parts[7] || '';

        	//previewImage( 'imagelist', 'view_imagelist', base_path );
        	srcImage = Y.one('#view_imagelist') || document.view_imagelist;
        	srcImage.src = base_path + parts[0];
        },
        /**
        * Used to change attributes/properties for a selected image in image list
        */
        applyImageProps: function() {
        	var form = Y.one('#adminForm') || Y.one('form[name="adminForm"]');
        	
        	if (!this.getSelectedValue( 'adminForm', 'imagelist' )) {
        		alert( "Select and image from the list" );
        		return;
        	}
        	var value = form._source.value + '|' +
        	            this.getSelectedValue( 'adminForm', '_align' ) + '|' +
                    	form._alt.value + '|' +
                    	parseInt( form._border.value, 10 ) + '|' +
                    	form._caption.value + '|' +
                    	this.getSelectedValue('adminForm', '_caption_position' ) + '|' +
                    	this.getSelectedValue( 'adminForm', '_caption_align' ) + '|' +
                    	form._width.value;
        	this.chgSelectedValue('adminForm', 'imagelist', value);
        },
        /**
        * Used to show a preview of selected image in image list
        */
        previewImage: function(list, image, base_path) {
        	var srcList   = Y.one('#' + list) || document[list],
        	    srcImage  = Y.one('#' + image) || document[image],
        	    fileName  = srcList.options[srcList.selectedIndex].text,
        	    fileName2 = srcList.options[srcList.selectedIndex].value;
        	    
        	if (fileName.length === 0 || fileName2.length === 0) {
        		srcImage.setAttribute("src", 'images/blank.gif');
        	} else {
        		srcImage.setAttribute("src", base_path + fileName2);
        	}
        },
        /**
        * Toggles the check state of a group of boxes
        *
        * Checkboxes must have an id attribute in the form cb0, cb1...
        * @param The number of box to 'check'
        * @param An alternative field name
        */
        checkAll: function(n, fldName) {        	
        	var i,
        	    toggle     = Y.one('#toggle') || Y.one('input[name="toggle"]'),
        	    c          = toggle.get("checked") === true ? true : '',
        	    boxchecked = Y.one('#boxchecked') || Y.one('input[name="boxchecked"]'),
        	    fName      = !Y.Lang.isUndefined(fldName) ? fldName : 'cb';

        	for (i=0; i < n; i++) {
        	    //Non-YUI selector since we can't use setAttribute in this case
        		var cb = document.getElementById(fName + '' + i);
        		if (!Y.Lang.isUndefined(cb)) {
        			cb.checked = c;
        		}
        	}

        	if (c === true) {
        		boxchecked.setAttribute("value", 1);
        	} else {
        		boxchecked.setAttribute("value", 0);
        	}
        },
        listItemTask: function(id, task) {
            var i,
                cbx,
                cb         = document.getElementById(id),
                boxchecked = Y.one('#boxchecked') || Y.one('input[name="boxchecked"]');
                
            if (cb) {
                for (i = 0; true; i++) {
                    cbx = document.getElementById('cb' + i);
                    if (!cbx) {
                        break;
                    }
                    cbx.checked = false;
                }
                cb.checked = true;
                boxchecked.setAttribute("value", 1);
                this.submitbutton(task);
            }
            
            return false;
        },
        /**
        * Used to hide the main admin menu
        */
        hideMainMenu: function() {
            var hidemainmenu = Y.one('#hidemainmenu') || Y.one('input[name="hidemainmenu"]');
            
            this.assert(!Y.Lang.isNull(hidemainmenu), "Unable to locate hidemainmenu!");
        	hidemainmenu.setAttribute("value", 1);
        },
        isChecked: function(isitchecked) {
            var hdnBoxchecked = Y.one('#boxchecked') || Y.one('input[name="boxchecked"]');
            this.assert(!Y.Lang.isNull(hdnBoxchecked), "Unable to update the boxchecked form field!");
            
        	if (isitchecked === true){
        		hdnBoxchecked.setAttribute("value", 1);
        	} else {
        		hdnBoxchecked.setAttribute("value", 0);
        	}
        },
        /**
        * Acts as default function for submit buttons.  Usually would be overriden by the component.
        */
        submitbutton: function(pressbutton) {
this.submitform(pressbutton);
            if (typeof window.submitbutton !== 'undefined') {
               window.submitbutton(pressbutton);
            } else {
                this.submitform(pressbutton);
            }
        },
        /**
        * Used to submit forms
        */
        submitform: function(pressbutton) {
            var form = Y.one('#adminForm') || Y.one('form[name="adminForm"]'),
                task = Y.one('#task') || Y.one('input[name="task"]');

            this.assert(!Y.Lang.isNull(form), "Unable to locate the admin form for submission!");

            if (!Y.Lang.isNull(task)) {
                task.setAttribute("value", pressbutton);
            }
            
            form.submit();
        },
        submitcpform: function(sectionid, id) {
            var form = Y.one('#adminForm') || Y.one('form[name="adminForm"]');
            
            this.assert(!Y.Lang.isNull(form), "Unable to locate the admin form for submission!");
        	
        	form.get(sectionid).setAttribute("value", sectionid);
        	form.id.value = id;
        	this.submitbutton("edit");
        },
        /**
        * Getting radio button that is selected.
        */
        getSelected: function(allbuttons) {
            var i;
        	for (i=0;i<allbuttons.length;i++) {
        		if (allbuttons[i].checked) {
        			return allbuttons[i].value;
        		}
        	}
        },
        /**
        * Pops up a new window in the middle of the screen
        */
        popupWindow: function(mypage, myname, w, h, scroll) {
        	var winl     = (screen.width - w) / 2,
        	    wint     = (screen.height - h) / 2,
        	    winprops = 'height=' + h + ',width=' + w + ',top=' + wint + ',left=' + winl + ',scrollbars=' + scroll + ',resizable',
        	    win      = window.open(mypage, myname, winprops);

        	if (parseInt(navigator.appVersion, 10) >= 4) { 
        	    win.window.focus(); 
        	}
        },
        /**
        * LTrim(string) : Returns a copy of a string without leading spaces.
        */
        ltrim: function(str) {
            var s          = str,
                whitespace = " \t\n\r";

            if (whitespace.indexOf(s.charAt(0)) !== -1) {
               var j  = 0, 
                    i = s.length;
              
                while (j < i && whitespace.indexOf(s.charAt(j)) !== -1) {
                    j++;
                }
              
                s = s.substring(j, i);
            } 
            
            return s;
        },
        /**
        * RTrim(string) : Returns a copy of a string without trailing spaces.
        */
        rtrim: function(str) {
            var s          = str,
                whitespace = " \t\n\r";
                
           if (whitespace.indexOf(s.charAt(s.length-1)) !== -1) {
              var i = s.length - 1;
              
              while (i >= 0 && whitespace.indexOf(s.charAt(i)) !== -1) {
                 i--; 
              }
                
              s = s.substring(0, i+1);
           }
           
           return s;
        },
        /**
        * trim(string) : Returns a copy of a string without leading and/or trailing spaces.
        */
        trim: function(str) {
          return Y.Lang.trim(str);
        },
        MM_findObj: function(n, d) {
        	var p, i, x;

        	if(!d) {
        	    d = document;
        	}

        	if ((p=n.indexOf("?"))>0 && parent.frames.length) {
        		d = parent.frames[n.substring(p+1)].document;
        		n = n.substring(0,p);
        	}

        	if (!(x=d[n]) && d.all) {
        	    x = d.all[n];
        	}

        	for (i=0; !x && i<d.forms.length; i++) {
        	    x = d.forms[i][n];
        	}

        	for (i=0; !x && d.layers && i<d.layers.length; i++) {
        	    x = MM_findObj(n,d.layers[i].document);
        	}

        	if (!x && d.getElementById) {
        	    x = d.getElementById(n);
        	}

        	return x;
        },
        /**
        * When the user rolls the mouse over an image with a behavior attached, the event 
        * triggers the function, which swaps the image source with another image.
        */
        MM_swapImage: function() {
        	var i,
        	    x,
        	    j = 0,
        	    a = this.MM_swapImage.arguments;
        	    
        	document.MM_sr = [];
        	
        	for(i=0; i<(a.length-2); i+=3) {
        	    if ((x=this.MM_findObj(a[i])) !== null) {
            	    document.MM_sr[j++] = x;
            	    
            	    if (!x.oSrc) {
            	        x.oSrc = x.src; 
            	    }
            	    
            	    x.src = a[i+2];
            	} 
        	}
        },
        /**
        * Reverse the effects of MM_swapImage
        */
        MM_swapImgRestore: function() {
        	var i,
        	    x,
        	    a = document.MM_sr;
        	
        	for(i=0; a && i<a.length && (x=a[i]) && x.oSrc; i++) {
        	    x.src = x.oSrc;
        	}
        },
        MM_preloadImages: function() {
        	var d = document;
        	if (d.images) {
        	    if (!d.MM_p) {
        	        d.MM_p = [];
        	    }

            	var i,
            	    j = d.MM_p.length,
            	    a = this.MM_preloadImages.arguments;

            	for(i=0; i<a.length; i++) {
            	    if (a[i].indexOf("#") !== 0){ 
                	    d.MM_p[j] = new Image();
                	    d.MM_p[j++].src = a[i];
                	}
            	}
        	}
        },
        /**
        * Save order of list.  Used in conjuction with the reorder feature. 
        */
        saveorder: function(n) {
        	this.checkAll_button(n);
        	this.submitform('saveorder');
        },
        /**
        * Used to select all items within an item list
        */
        checkAll_button: function(n) {
        	for ( var j = 0; j <= n; j++ ) {
        		var id  = 'cb' + j,
        		    box = Y.one('#' + id) || false; 

        		if ( box !== false && box.getAtrribute("checked") === false ) {
        			box.setAtrribute("checked", true);
        		}
        	}
        },
        /**
        * @param object A form element
        * @param string The name of the element to find
        */
        getElementByName: function(f, name) {
        	if (f.elements) {
        		for (var i=0, n=f.elements.length; i<n; i++) {
        			if (f.elements[i].name == name) {
        				return f.elements[i];
        			}
        		}
        	}
        	return null;
        }
        
    });
    
    Y.AliroCore = AliroCore; //inject new class into the sandbox

}, '1.0', {requires:["yui", "base", "event", "event-custom"]} );
