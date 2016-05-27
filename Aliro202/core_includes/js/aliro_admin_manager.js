/**
* @version $Id: aliro_admin_manager.js
* @package Aliro
* @copyright (C) 2007-2009 Aliro Software Limited, from code (c) The Mambo Foundation/Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Aliro is Free Software
*/

YUI().use("*", function(Y) {
    
    //Just about every YUI component will require a special body class so add as soon as possible
    Y.on("available", function() {
        Y.Node.get("body").addClass("yui-skin-sam");
    }, "body");
    
    YUI.namespace("ALIRO");
    YUI.ALIRO.CORE    = new Y.AliroCore();
    YUI.ALIRO.BACKEND = new Y.AliroBackend();
    YUI.ALIRO.COREUI  = new Y.AliroCoreUI();
    
    Y.on("domready", function() {
        YUI.ALIRO.CORE.loadDebugConsole();
        YUI.ALIRO.BACKEND.setupAdminList();
        
        /*****************************
        * CORE EVENT HANDLERS 
        ******************************/
        //Handles publish link clicks
        Y.delegate("click", function(e) {
            e.preventDefault();

            var parsedLink = this.get("id").split("__"),
                id         = parsedLink[0],
                task       = parsedLink[1];

        	YUI.ALIRO.CORE.listItemTask(id, task);
    	}, "#AliroAdminMainbox", "a.publish-processing-link");
        
        /*****************************
        * TOOLTIP EVENT HANDLERS 
        ******************************/
        var presenceOfTooltip = Y.one(".tooltip-container") || 'undefined'; //could be more that 1, but we just need to know if the page has any
        
        if (presenceOfTooltip !== 'undefined') {
            //Handles tooltips interactions
            Y.delegate("mouseenter", function(e) {
                YUI.ALIRO.COREUI.tooltip.buildTooltipFromMarkup.call(YUI.ALIRO.COREUI, e);
            }, document.body, "a.tooltip-container");

            Y.delegate("click", function(e) {
                e.preventDefault();
            }, document.body, "a.tooltip-container", Y);
        }
    });
    
});

