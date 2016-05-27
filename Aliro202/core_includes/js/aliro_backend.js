/**
* @version $Id: aliro_backend.js
* @package Aliro
* @copyright (C) 2007-2009 Aliro Software Limited, from code (c) The Mambo Foundation/Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Aliro is Free Software
*/

YUI.add('aliro-backend', function(Y) {
    
    /**
    * Aliro Backend Foundation Class
    */

    function AliroBackend(config) {
        AliroBackend.superclass.constructor.apply(this, arguments);
    }

    //Used to identify instances of this class
    AliroBackend.NAME = "AliroBackend";

    //"Associative Array", used to define the set of attributes 
    //added by this class. The name of the attribute is the key,
    //and the object literal value acts as the configuration 
    //object passed to addAttrs
    AliroBackend.ATTRS = {};
    
    //Prototype methods
    Y.extend(AliroBackend, Y.Base, {
        initializer : function() {
            Y.log('AliroBackend has loaded!');
        },
        /** 
        * This function locates a standard adminlist in the DOM and creates a click handler for it.  Using
        * event delegation we identify clicks we need to react to.  Right now the list of important actions
        * includes; orderUpIcon, orderDownIcon, AccessProcessing, PublishedProcessing, CheckedOutProcessing.
        * We then only need a single listener for all important events improving performance overall.
        */
        setupAdminList :  function() {
            Y.delegate("click", function(e) {
                e.preventDefault();
                Y.log("An adminlist was clicked");
                    
                var targetToActOn,
	                eltargetId, 
	                eltargetIdSplit,
                    eltargetCbId,
                    task;
                    
                //The actual click might have come from a wrapped image so handle accordingly
                if (e.target.get('tagName').toLowerCase() === 'img') {
                    targetToActOn = e.target.get('parentNode');
                } else {
                    targetToActOn = e.target;
                }
                
                eltargetId = targetToActOn.get('id');
                YUI.ALIRO.CORE.assert((eltargetId.indexOf("__") !== -1), "The row ID does not conform to the new adminlist structure.  Component update required!"); 
                
                eltargetIdSplit = eltargetId.split("__");
                eltargetCbId = eltargetIdSplit[1];
                task = eltargetIdSplit[0];

    	        YUI.ALIRO.CORE.listItemTask(eltargetCbId, task);
        	}, ".adminlist", "a.list-item-task");
    	
    	    //Handle idbox clicks
        	Y.delegate("click", function(e) {
        	    Y.log("An idbox was clicked");

                YUI.ALIRO.CORE.isChecked(e.target.get("checked"));
        	}, ".adminlist", ".idbox");
    	
        	this.fire('adminlist:setup');
        }
    });
    
    Y.AliroBackend = AliroBackend; //inject new class into the sandbox

}, '1.0', {requires:["yui", "base", "event", "event-custom", "aliro-core"]} );
