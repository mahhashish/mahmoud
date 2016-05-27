/**
* @version $Id: aliro_core_ui.js
* @package Aliro
* @copyright (C) 2007-2009 Aliro Software Limited, from code (c) The Mambo Foundation/Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Aliro is Free Software
*/

YUI.add('aliro-core-ui', function(Y) {
    
    /**
    * Aliro UI Class
    */

    function AliroCoreUI(config) {
        AliroCoreUI.superclass.constructor.apply(this, arguments);
    }

    //Used to identify instances of this class
    AliroCoreUI.NAME = "AliroCoreUI";

    //"Associative Array", used to define the set of attributes 
    //added by this class. The name of the attribute is the key,
    //and the object literal value acts as the configuration 
    //object passed to addAttrs
    AliroCoreUI.ATTRS = {
    };
    
    //Prototype methods
    Y.extend(AliroCoreUI, Y.Base, {
        initializer : function() {
            Y.log('AliroCoreUI has loaded!');
        },
        tooltip : {
            ttInstance : null,
            doTooltipContainerCheck : function() {
                var aliroTooltipContainer = Y.one('#aliro_tooltip_container') || 'undefined';
                //Add supporting tooltip container div to the DOM as needed
                if (aliroTooltipContainer === 'undefined') {
                    Y.one(document.body).append('<div id="aliro_tooltip_container"></div>');
                    Y.log("Tooltip container setup complete!  Should only happen once.", "info");
                }
            },
            //Used to setup system tooltips
            //Note: requires "overlay", normally added via a call to aliroHTML::toolTip
            getTooltip: function() {
                this.doTooltipContainerCheck();
                var tt = new Y.Overlay({
                            zIndex: 999999,
                            visible: false
                        });

                tt.render("#aliro_tooltip_container");
                
                this.ttInstance = tt;
                return this.ttInstance;
            },
            //Used to build a tooltip created via aliroHTML::toolTip
            buildTooltipFromMarkup: function(e) {
                var elementToAlignWith = e.target.get('id') || e.target,
                    tooltipNode        = e.target.get('parentNode').one(".tooltip") || 'undefined',
                    tooltipText,
                    WidgetPositionExt  = Y.WidgetPositionExt;

                if (tooltipNode !== 'undefined') {
                    tooltipText = tooltipNode.get('innerHTML');
                    YUI.ALIRO.COREUI.tooltip.displayTooltip.call(YUI.ALIRO.COREUI, elementToAlignWith, tooltipText, null, null, 'TL', 'TR');
                } else {
                    Y.log("Tooltip hover active, but unable to locate tooltip text!", "warn");
                }
            },
            //Used to build inline mouseover tooltips (e.g.) see aliroHTML::checkedOut
            //- Used somewhat like the older overlib function when called directly in a mousover
            buildInlineTooltip: function(tooltipText, headerText, footerText, overlayPosition, nodePosition, ttWidth) {
                //When triggered from a mouseover the scope of "this" should be that of the node that triggered the event
                var elementToAlignWith = Y.one(this) || 'undefined';
                
                YUI.ALIRO.CORE.assert(!Y.Lang.isUndefined(elementToAlignWith.get('tagName')), "YUI.ALIRO.COREUI.tooltipFromMouseover called with invalid scope.  Unable to locate DOM node for alignment!");
                YUI.ALIRO.COREUI.tooltip.displayTooltip.call(YUI.ALIRO.COREUI, elementToAlignWith, tooltipText, headerText, footerText, overlayPosition, nodePosition, ttWidth);
            },
            //Used to display a customized tooltip
            //- For all possible positions see - http://developer.yahoo.com/yui/3/api/WidgetPositionExt.html
            displayTooltip: function(elementToAlignWith, tooltipText, headerText, footerText, overlayPosition, nodePosition, ttWidth) {
                //If element id was provide we seek out the node reference
                if (Y.Lang.isString(elementToAlignWith)) {
                    elementToAlignWith = Y.one('#' + elementToAlignWith) || 'undefined';
                }
                
                //At this point we should have a valid DOM node reference for alignment
                YUI.ALIRO.CORE.assert(elementToAlignWith !== 'undefined' && !Y.Lang.isUndefined(elementToAlignWith.get('tagName')), "YUI.ALIRO.COREUI.displayTooltip: Unable to locate DOM node for alignment!");
                
                var alignmentId       = elementToAlignWith.get("id") || 'undefined',
                    tt                = this.tooltip.getTooltip(),
                    WidgetPositionExt = Y.WidgetPositionExt;

                if (alignmentId === 'undefined') {
    				alignmentId = Y.guid(); //this node lacks an id so generate one for use in alignment
    				elementToAlignWith.set("id", alignmentId);
    			}
                
                //Construct tooltip and alignment based on user provided attributes
                tt.set("align", {node: '#' + alignmentId, 
                                    points:[WidgetPositionExt[overlayPosition], WidgetPositionExt[nodePosition]]});

                //We must have tooltipText, others are optional
                YUI.ALIRO.CORE.assert(!Y.Lang.isUndefined(tooltipText), "Tooltip text is required to create a tooltip!");
                tt.set("bodyContent", tooltipText);

                if (!Y.Lang.isUndefined(headerText)) {
                    tt.set("headerContent", headerText);
                }
                if (!Y.Lang.isUndefined(footerText)) {
                    tt.set("footerContent", footerText);
                }
                if (!Y.Lang.isUndefined(ttWidth)) {
                    tt.set("width", ttWidth);
                }

                Y.on("mouseleave", function() {
                    this.tooltip.hideTooltip();
                    Y.log('Tooltip deactivated. You should not see the tooltip!');
                }, elementToAlignWith, this);

                tt.show();
                Y.log('Tooltip active. You should see now the tooltip!');
            },
            hideTooltip: function() {
                if (!Y.Lang.isNull(this.ttInstance)) {
                    this.ttInstance.hide();
                    this.ttInstance = null;
                }
            }
        }
        
    });
    
    Y.AliroCoreUI = AliroCoreUI; //inject new class into the sandbox

}, '1.0', {requires:["aliro-core"]} ); //does require others, but they should be loaded ad-hoc via PHP calls
