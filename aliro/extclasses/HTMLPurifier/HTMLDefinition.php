<?php

/**
 * Definition of the purified HTML that describes allowed children,
 * attributes, and many other things.
 * 
 * Conventions:
 * 
 * All member variables that are prefixed with info
 * (including the main $info array) are used by HTML Purifier internals
 * and should not be directly edited when customizing the HTMLDefinition.
 * They can usually be set via configuration directives or custom
 * modules.
 * 
 * On the other hand, member variables without the info prefix are used
 * internally by the HTMLDefinition and MUST NOT be used by other HTML
 * Purifier internals. Many of them, however, are public, and may be
 * edited by userspace code to tweak the behavior of HTMLDefinition.
 * 
 * @note This class is inspected by Printer_HTMLDefinition; please
 *       update that class if things here change.
 *
 * @warning Directives that change this object's structure must be in
 *          the HTML or Attr namespace!
 */
class HTMLPurifier_HTMLDefinition extends HTMLPurifier_Definition
{
    
    // FULLY-PUBLIC VARIABLES ---------------------------------------------
    
    /**
     * Associative array of element names to HTMLPurifier_ElementDef
     */
    public $info = array();
    
    /**
     * Associative array of global attribute name to attribute definition.
     */
    public $info_global_attr = array();
    
    /**
     * String name of parent element HTML will be going into.
     */
    public $info_parent = 'div';
    
    /**
     * Definition for parent element, allows parent element to be a
     * tag that's not allowed inside the HTML fragment.
     */
    public $info_parent_def;
    
    /**
     * String name of element used to wrap inline elements in block context
     * @note This is rarely used except for BLOCKQUOTEs in strict mode
     */
    public $info_block_wrapper = 'p';
    
    /**
     * Associative array of deprecated tag name to HTMLPurifier_TagTransform
     */
    public $info_tag_transform = array();
    
    /**
     * Indexed list of HTMLPurifier_AttrTransform to be performed before validation.
     */
    public $info_attr_transform_pre = array();
    
    /**
     * Indexed list of HTMLPurifier_AttrTransform to be performed after validation.
     */
    public $info_attr_transform_post = array();
    
    /**
     * Nested lookup array of content set name (Block, Inline) to
     * element name to whether or not it belongs in that content set.
     */
    public $info_content_sets = array();
    
    /**
     * Doctype object
     */
    public $doctype;
    
    
    
    // RAW CUSTOMIZATION STUFF --------------------------------------------
    
    /**
     * Adds a custom attribute to a pre-existing element
     * @note This is strictly convenience, and does not have a corresponding
     *       method in HTMLPurifier_HTMLModule
     * @param $element_name String element name to add attribute to
     * @param $attr_name String name of attribute
     * @param $def Attribute definition, can be string or object, see
     *             HTMLPurifier_AttrTypes for details
     */
    public function addAttribute($element_name, $attr_name, $def) {
        $module =& $this->getAnonymousModule();
        if (!isset($module->info[$element_name])) {
            $element =& $module->addBlankElement($element_name);
        } else {
            $element =& $module->info[$element_name];
        }
        $element->attr[$attr_name] = $def;
    }
    
    /**
     * Adds a custom element to your HTML definition
     * @note See HTMLPurifier_HTMLModule::addElement for detailed 
     *       parameter and return value descriptions.
     */
    public function &addElement($element_name, $type, $contents, $attr_collections, $attributes) {
        $module =& $this->getAnonymousModule();
        // assume that if the user is calling this, the element
        // is safe. This may not be a good idea
        $element =& $module->addElement($element_name, $type, $contents, $attr_collections, $attributes);
        return $element;
    }
    
    /**
     * Adds a blank element to your HTML definition, for overriding
     * existing behavior
     * @note See HTMLPurifier_HTMLModule::addBlankElement for detailed
     *       parameter and return value descriptions.
     */
    public function &addBlankElement($element_name) {
        $module  =& $this->getAnonymousModule();
        $element =& $module->addBlankElement($element_name);
        return $element;
    }
    
    /**
     * Retrieves a reference to the anonymous module, so you can
     * bust out advanced features without having to make your own
     * module.
     */
    public function &getAnonymousModule() {
        if (!$this->_anonModule) {
            $this->_anonModule = new HTMLPurifier_HTMLModule();
            $this->_anonModule->name = 'Anonymous';
        }
        return $this->_anonModule;
    }
    
    private $_anonModule;
    
    
    // PUBLIC BUT INTERNAL VARIABLES --------------------------------------
    
    public $type = 'HTML';
    public $manager; /**< Instance of HTMLPurifier_HTMLModuleManager */
    
    /**
     * Performs low-cost, preliminary initialization.
     */
    public function __construct() {
        $this->manager = new HTMLPurifier_HTMLModuleManager();
    }
    
    protected function doSetup($config) {
        $this->processModules($config);
        $this->setupConfigStuff($config);
        unset($this->manager);
        
        // cleanup some of the element definitions
        foreach ($this->info as $k => $v) {
            unset($this->info[$k]->content_model);
            unset($this->info[$k]->content_model_type);
        }
    }
    
    /**
     * Extract out the information from the manager
     */
    protected function processModules($config) {
        
        if ($this->_anonModule) {
            // for user specific changes
            // this is late-loaded so we don't have to deal with PHP4
            // reference wonky-ness
            $this->manager->addModule($this->_anonModule);
            unset($this->_anonModule);
        }
        
        $this->manager->setup($config);
        $this->doctype = $this->manager->doctype;
        
        foreach ($this->manager->modules as $module) {
            foreach($module->info_tag_transform         as $k => $v) {
                if ($v === false) unset($this->info_tag_transform[$k]);
                else $this->info_tag_transform[$k] = $v;
            }
            foreach($module->info_attr_transform_pre    as $k => $v) {
                if ($v === false) unset($this->info_attr_transform_pre[$k]);
                else $this->info_attr_transform_pre[$k] = $v;
            }
            foreach($module->info_attr_transform_post   as $k => $v) {
                if ($v === false) unset($this->info_attr_transform_post[$k]);
                else $this->info_attr_transform_post[$k] = $v;
            }
        }
        
        $this->info = $this->manager->getElements();
        $this->info_content_sets = $this->manager->contentSets->lookup;
        
    }
    
    /**
     * Sets up stuff based on config. We need a better way of doing this.
     */
    protected function setupConfigStuff($config) {
        
        $block_wrapper = $config->get('HTML', 'BlockWrapper');
        if (isset($this->info_content_sets['Block'][$block_wrapper])) {
            $this->info_block_wrapper = $block_wrapper;
        } else {
            trigger_error('Cannot use non-block element as block wrapper',
                E_USER_ERROR);
        }
        
        $parent = $config->get('HTML', 'Parent');
        $def = $this->manager->getElement($parent, true);
        if ($def) {
            $this->info_parent = $parent;
            $this->info_parent_def = $def;
        } else {
            trigger_error('Cannot use unrecognized element as parent',
                E_USER_ERROR);
            $this->info_parent_def = $this->manager->getElement($this->info_parent, true);
        }
        
        // support template text
        $support = "(for information on implementing this, see the ".
                   "support forums) ";
        
        // setup allowed elements
        
        $allowed_elements = $config->get('HTML', 'AllowedElements');
        $allowed_attributes = $config->get('HTML', 'AllowedAttributes');
        
        if (!is_array($allowed_elements) && !is_array($allowed_attributes)) {
            $allowed = $config->get('HTML', 'Allowed');
            if (is_string($allowed)) {
                list($allowed_elements, $allowed_attributes) = $this->parseTinyMCEAllowedList($allowed);
            }
        }
        
        if (is_array($allowed_elements)) {
            foreach ($this->info as $name => $d) {
                if(!isset($allowed_elements[$name])) unset($this->info[$name]);
                unset($allowed_elements[$name]);
            }
            // emit errors
            foreach ($allowed_elements as $element => $d) {
                // :TODO: Is this htmlspecialchars() call really necessary?
                $element = htmlspecialchars($element);
                trigger_error("Element '$element' is not supported $support", E_USER_WARNING);
            }
        }
        
        $allowed_attributes_mutable = $allowed_attributes; // by copy!
        if (is_array($allowed_attributes)) {
            foreach ($this->info_global_attr as $attr_key => $info) {
                if (!isset($allowed_attributes["*.$attr_key"])) {
                    unset($this->info_global_attr[$attr_key]);
                } elseif (isset($allowed_attributes_mutable["*.$attr_key"])) {
                    unset($allowed_attributes_mutable["*.$attr_key"]);
                }
            }
            foreach ($this->info as $tag => $info) {
                foreach ($info->attr as $attr => $attr_info) {
                    if (!isset($allowed_attributes["$tag.$attr"]) &&
                        !isset($allowed_attributes["*.$attr"])) {
                        unset($this->info[$tag]->attr[$attr]);
                    } else {
                        if (isset($allowed_attributes_mutable["$tag.$attr"])) {
                            unset($allowed_attributes_mutable["$tag.$attr"]);
                        } elseif (isset($allowed_attributes_mutable["*.$attr"])) {
                            unset($allowed_attributes_mutable["*.$attr"]);
                        }
                    }
                }
            }
            // emit errors
            foreach ($allowed_attributes_mutable as $elattr => $d) {
                list($element, $attribute) = explode('.', $elattr);
                // :TODO: Is this htmlspecialchars() call really necessary?
                $element = htmlspecialchars($element);
                $attribute = htmlspecialchars($attribute);
                if ($element == '*') {
                    trigger_error("Global attribute '$attribute' is not ".
                        "supported in any elements $support",
                        E_USER_WARNING);
                } else {
                    trigger_error("Attribute '$attribute' in element '$element' not supported $support",
                        E_USER_WARNING);
                }
            }
            
        }
        
        // setup forbidden elements
        $forbidden_elements = $config->get('HTML', 'ForbiddenElements');
        $forbidden_attributes = $config->get('HTML', 'ForbiddenAttributes');
        
        foreach ($this->info as $tag => $info) {
            if (isset($forbidden_elements[$tag])) {
                unset($this->info[$tag]);
                continue;
            }
            foreach ($info->attr as $name => $def) {
                if (isset($forbidden_attributes["$tag.$name"])) {
                    unset($this->info[$tag]->attr[$name]);
                    continue;
                }
            }
        }
        
    }
    
    /**
     * Parses a TinyMCE-flavored Allowed Elements and Attributes list into
     * separate lists for processing. Format is element[attr1|attr2],element2...
     * @warning Although it's largely drawn from TinyMCE's implementation,
     *      it is different, and you'll probably have to modify your lists
     * @param $list String list to parse
     * @param array($allowed_elements, $allowed_attributes)
     * @todo Give this its own class, probably static interface
     */
    public function parseTinyMCEAllowedList($list) {
        
        $elements = array();
        $attributes = array();
        
        $chunks = preg_split('/(,|[\n\r]+)/', $list);
        foreach ($chunks as $chunk) {
            if (empty($chunk)) continue;
            // remove TinyMCE element control characters
            if (!strpos($chunk, '[')) {
                $element = $chunk;
                $attr = false;
            } else {
                list($element, $attr) = explode('[', $chunk);
            }
            if ($element !== '*') $elements[$element] = true;
            if (!$attr) continue;
            $attr = substr($attr, 0, strlen($attr) - 1); // remove trailing ]
            $attr = explode('|', $attr);
            foreach ($attr as $key) {
                $attributes["$element.$key"] = true;
            }
        }
        
        return array($elements, $attributes);
        
    }
    
    
}

