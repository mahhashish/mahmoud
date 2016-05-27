<?php
if(str_replace( '\\', '/',$_SERVER['DOCUMENT_ROOT']) == SERVER_ROOT) { define('ABS_PATH', '');}  
    else { define('ABS_PATH', SERVER_ROOT); }
require_once($_SERVER['DOCUMENT_ROOT'].'/'. ABS_PATH .'/phpGrid.php');  

if(!session_id()){ session_start();}  
class C_DataGrid{            
    // grid columns
    private $sql;                                                                      
    private $sql_table;                                                                    
    private $sql_key;                                                                  
	private $callbackstring = '';	// Information for restoring environment in callbacks
    private $sql_fkey;          // foreign key (used by when grid is a subgrid);
    private $col_dbnames;       // original database field names
    private $col_hiddens;       // columns that are hidden  
    private $col_titles;        // descriptive titles                                                               
    private $col_readonly;      // columns read only      
    private $col_required;      // required when editing
    private $col_links;         // hyplinks (formatter:link)
    private $col_dynalinks;     // dynamic hyplinks (formmatter:showLink)
    private $col_edittypes;     // editype -> HTML control used in edit    
    private $col_datatypes;     // data type used in editrule
    private $col_imgs;          // image columns
    private $col_custom;          // custom formatted columns
//    private $col_custom_css;          // custom formatted columns
    
	private $col_widths; //  columns width 
    private $col_aligns; //  columns alignment 

	private $filter_sql; //  set filter
	private	$jq_summary_col_name;
	private	$jq_summary_type ;
	private $jq_showSummaryOnHide;

	
    // jqgrid
    private $jq_gridName;    
    private $jq_url;  
    private $jq_datatype;
    private $jq_mtype;
    private $jq_colNames;
    private $jq_colModel;
    private $jq_pagerName;
    private $jq_rowNum;
    private $jq_rowList;
    private $jq_sortname;
    private $jq_sortorder;
    private $jq_viewrecords;    // display recornds count in pager
    private $jq_multiselect;    // display checkbox for each row
    private $jq_autowidth;      // when true the width is set to 100% 
    private $jq_width;
    private $jq_height;
    
/* START all the variables for the group*/
	private $jq_grouping;
	private $jq_group_name;
	private $jq_is_group_fild_hidden;
	private $jq_direction;
	private $jq_groupcollapse;
/* END all the variables for the group*/

    private $jq_caption;    
    private $jq_cellEdit;       // cell edit when true
    private $jq_altRows;        // can have alternative row, or zebra, color
    private $jq_scrollOffset;   // horizontal scroll bar offset
    private $jq_editurl;        // inline edit url
    private $jq_rownumbers;     // row index
    private $jq_forceFit;       // maintain overall grid width when resizing a column   
    private $jq_loadtext;       // load promote text
    private $jq_scroll;         // use vertical scrollbar to load data. pager is disabled automately if true. height MUST NOT be 100% if true.

    private $jq_hiddengrid;     // hide grid initially
    private $jq_gridview;       // load all the data at once result in faster rendering. However, if set to true No Subgrid, treeGrid, afterInsertRow    
    
    // jquery ui
    private $jqu_resize;         // resize grid                                                          
    
    // others
    private $_num_rows;                                                                
    private $_num_fields;                                                              
    private $_file_path;                                                               
    private $_ver_num;          
    private $edit_mode;         // CELL, INLINE, FORM, or NONE
    private $edit_options;      // CRUD options
    private $has_tbarsearch;    // integrated toolbar 
    private $advanced_search;
    private $sys_msg;           // system message, e.g. error, alert
    private $alt_colors;        // row color class: ui-priority-secondary, ui-state-highlight, ui-state-hover
    private $theme_name;        // jQuery UI theme name
    private $locale;
    
    public $export_type;       // Export to EXCEL, HTML, PDF
    public $export_url;        
    public $debug;
    public $db;    
    public $db_connection = array();
    public $ud_params;           // user defined json properites
    public $obj_subgrid;        // subjgrid object                      
    public $obj_md = array();             // master detail object   

    //conditional formatting
    private $jq_rowConditions;
    private $jq_cellConditions;
    
    // Desc: our constructor
    // *** Note *** 
    // key and table are not technically required for ready-only grid
    // Next version, the sql_key, table, and foriegn are array to support composite keys 
    // and CRUD over mutiple tables   
    // 03.09.2011 - added $db_connection optional parameter for multiple databases
    
    public function __construct($sql, $sql_key='id', $sql_table='', $db_connection= array()){                
        
        //set the default database
        if(empty($db_connection)) {
            $this->db = new C_DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_TYPE,DB_CHARSET);        
        }
        else {
            $this->db = new C_DataBase($db_connection["hostname"],
									   $db_connection["username"], 
									   $db_connection["password"], 
									   $db_connection["dbname"], 
									   $db_connection["dbtype"],
									   $db_connection["dbcharset"]);        
            $this->db_connection = $db_connection;
        }
        
        $this->sql          = $sql .' WHERE 1=1';        
        $this->sql_key      = $sql_key;  
        $this->sql_fkey     = null;
        $this->sql_table    = $sql_table; 

        $this->_num_rows    = 0;//$this->db->num_rows($this->db->db_query($sql));
        $results            = $this->db->select_limit($sql,1, 1);
        $this->_num_fields  = $this->db->num_fields($results);
                
        // grid columns properties
        $this->col_hiddens          = array();      
        $this->col_titles           = array();      
        $this->col_readonly         = array();      
        $this->col_required         = array();      
        $this->col_links            = array();
        $this->col_dynalinks        = array();          
        $this->col_dbnames          = array();
        $this->col_edittypes        = array();
        $this->col_formats          = array();
		$this->col_widths           = array();     
        $this->col_aligns           = array();     

		$this->jq_summary_col_name=array();
//        $this->col_datatypes        = array();
        $this->col_imgs             = array();
        
        // jqgrid
        $this->jq_gridName  = ($sql_table == '')?'list1':$sql_table;    
        $this->jq_url       = '"'. ABS_PATH .'/data.ajax.php?gn='. $this->jq_gridName .'"';  // Notice double quote
        $this->jq_datatype  = 'json';
        $this->jq_mtype     = 'GET';
        $this->jq_colNames  = array();
        $this->jq_colModel  = array();
        $this->jq_pagerName = '"#'. $this->jq_gridName .'_pager1"';  // Notice the double quote
        $this->jq_rowNum    = 20;
        $this->jq_rowList   = array(10, 20, 30);
        $this->jq_sortname  = '';
        $this->jq_sortorder = 'asc';                                                                                               
        $this->jq_viewrecords = true;     
        $this->jq_multiselect = false;
        $this->jq_autowidth = false;
        $this->jq_width     = 800;
        $this->jq_height    = '100%';         
        $this->jq_caption   = $sql_table .'&nbsp;';
        $this->jq_altRows   = true;   
        $this->jq_scrollOffset = 22;    
        $this->jq_cellEdit  = false;
        $this->jq_editurl   = '';
        $this->jq_rownumbers = false;
        $this->jq_forceFit  = true;  
        $this->jq_scroll    = false; 
        $this->jq_hiddengrid= false;
        $this->jq_loadtext  = 'Loading phpGrid ...';   
        $this->jq_gridview  = true;
		$this->jq_grouping  = false;
		$this->jq_is_group_fild_hidden=false;
		$this->jq_direction='ltr';
		$this->jq_groupcollapse='false';
		//$this->jq_summary_col_name='';
		$this->jq_summary_type ='';
		$this->jq_showSummaryOnHide=false;
		$this->jq_is_group_summary=false;
        
        // jquery ui (currently in beta in jqgrid 3.6.4)
        $this->jqu_resize           = array('is_resizable'=>false,'min_width'=>300,'min_height'=>100);
        
        $this->_num_rows            = 0;            // values are updated in display()
        $this->_num_fields          = 0;            // values are updated in display()
        $this->_ver_num             = 'phpGrid(v4.3) jgGrid(v3.8.2) jQuery(v1.4.2) jQuery UI(1.7.3)';   
        $this->sys_msg              = null;
        $this->alt_colors           = array();   
        $this->theme_name           = 'start';
        $this->locale               = 'en';
        $this->export_type  = null;
        $this->export_url   = ABS_PATH .'/export.ajax.php?gn='.$this->jq_gridName;
        $this->edit_mode    = 'NONE';
        $this->edit_options = null;
        $this->has_tbarsearch = false;
        $this->advanced_search = false;
        $this->debug        = false;
        $this->ud_params = '';  
        $this->obj_subgrid = null;
        $this->obj_md      = null;
        
        $this->jq_rowConditions = array();
        $this->jq_cellConditions = array();                
    }  
    
	public function setCallbackString ($string) {
		$this->callbackstring = '&cbstr='.strtr(rtrim(base64_encode($string), '='), '+/', '-_');
		$this->jq_url = substr($this->jq_url,0,-1).$this->callbackstring.'"';
		$this->export_url .= $this->callbackstring;
		
	}
    // Desc: Intializing all necessary properties
    // Must call this method before display
    public function prepare_grid(){
        $this_db            = $this->db;
        $this->_num_rows    = $this_db->num_rows($this_db->db_query($this->sql));
        $results            = $this_db->select_limit($this->sql,1, 1);
        $this->_num_fields  = $this_db->num_fields($results);
        $this->col_dbnames  = $this->get_col_dbnames($results);        
        $this->set_colNames($results);             
        $this->set_colModel($results);                
    }
    
    // Desc: get original database field names
    public function get_col_dbnames($results){
        $this_db = $this->db;
        $col_dbnames = array();
        for($i = 0; $i < $this->_num_fields; $i++) {
            $col_dbname = $this_db->field_name($results, $i);             
            $col_dbnames[] = $col_dbname;        
        }          
        
        return $col_dbnames;
    }         
    
    // Desc: return table header in html with user definied descriptive names
    public function set_colNames($results){  
        $this_db = $this->db;
        $col_names = array();
        for($i = 0; $i < $this->_num_fields; $i++) {
            $col_name = $this_db->field_name($results, $i);             
            // check descriptive titles            
            if(isset($this->col_titles[$col_name]))
                $col_names[] = $this->col_titles[$col_name];
            else
                $col_names[] = $col_name;        
        }          
        $this->jq_colNames = $col_names;                                        
        
        return $col_names;
    }
    
    public function get_colNames(){
        return $this->jq_colNames;
    }
        
    public function set_colModel($results){
        $this_db = $this->db;
        $colModel = array();
        for($i=0;$i<$this->_num_fields;$i++){
            $col_name = $this_db->field_name($results, $i); 
            $col_type = $this_db->field_metatype($results, $i);            

            $cols = array();
            $cols['name'] = $col_name;   
            $cols['index'] = $col_name;
            $cols['hidden'] = isset($this->col_hiddens[$col_name]);

			// set width of coulmns
			if(isset($this->col_widths[$col_name])){
					$cols['width'] = $this->col_widths[$col_name]['width'];
			}
            
            // set column alignments
            if(isset($this->col_aligns[$col_name])) {
                $cols['align'] = $this->col_aligns[$col_name]['align'];
            }
            
            //Summry defind here..
			if(isset($this->jq_summary_col_name[$col_name])){
				$cols['summaryType'] = $this->jq_summary_col_name[$col_name]['summaryType'];
			
			}
            // edittype
            if(isset($this->col_edittypes[$col_name])){
                $cols['edittype'] = $this->col_edittypes[$col_name]['type'];                
            }else{
                $cols['edittype'] = ($col_type=='X')?'textarea':'text';           
            }
                 
            // *** Note *** 
			// For INLINE edit, set editable to whatever the value is in colModal.
            // For FORM edit, set all elements editable because not editable -> hidden in Form, and hidden fields are not editable by default. 
            // Instead readonly is set in beforeShowForm method. See (http://stackoverflow.com/questions/1987881/how-to-have-different-edit-options-for-add-edit-forms-in-jqgrid)
            switch($this->edit_mode)    {
                case 'CELL':
                case 'INLINE':
					$cols['editable'] = !in_array($col_name, $this->col_readonly);
					break;
                case 'FORM':
                    $cols['editable'] = true; 
                    break;
                default:
                    $cols['editable'] = false;
            }
            
            // editoptions
            // *** Note *** readonly is now set in beforeShowForm method
            // *** Note *** Datepicker requires jQuery UI 1.7.x. 
            // ### is the placeholder used later to remove leading and trailing quote,
            //     wrongly added by json_encode(), that surrounds the jquery event function  
            $editoptions = array();
            if(($col_type=='D'||$col_type=='T') && 
                !in_array($col_name, $this->col_readonly)){   // do not display datepicker if readonly
                $editoptions['dataInit'] = '###function(el){$(el).datepicker({dateFormat:\'yy-mm-dd\'});}###';
            }elseif(isset($this->col_edittypes[$col_name])){     
                if($this->col_edittypes[$col_name]['value']!=null){
                    $editoptions['value'] = $this->col_edittypes[$col_name]['value'];                    
                }                
                // for select editoptions only
                $editoptions['multiple'] = $this->col_edittypes[$col_name]['multiple']; 
                if($this->col_edittypes[$col_name]['dataUrl']!=null){
                    $editoptions['dataUrl']  = $this->col_edittypes[$col_name]['dataUrl'];             
                }
            }
            
                                                    
            // editrules
            $editrules = array();
            $editrules['edithidden'] = (isset($this->col_hiddens[$col_name]['edithidden']) && $this->col_hiddens[$col_name]['edithidden']==true)?true:false;
            $editrules['required']   =  in_array($col_name, $this->col_required);
            if(isset($this->col_datatypes[$col_name])){
                $editrules[$this->col_datatypes[$col_name]] = true;
            }else{
                switch($col_type){
                    case 'N':
                    case 'I':
                    case 'R':
                        $editrules['number'] = true;
                        break;
                    case 'D':
                        $editrules['date'] = true;                
                        break;
                }                
            }
            
            // formatter & formatoptions 
            // we try to make formatting automated as much as possible by using pre-defined formatter
            // based on ADOdb metatype and user settings
            // (formatter - http://www.trirand.com/jqgridwiki/doku.php?id=wiki:predefined_formatter)
            // (metatype - http://phplens.com/lens/adodb/docs-adodb.htm#metatype)
            if(isset($this->col_formats[$col_name])){
                if(isset($this->col_formats[$col_name]['link'])){
                    $cols['formatter'] = 'link';
                    $formatoptions = array();
                    $formatoptions['target'] = $this->col_formats[$col_name]['link']['target'];
                    $cols['formatoptions'] = $formatoptions;                                    
                }elseif(isset($this->col_formats[$col_name]['showlink'])){
                    $cols['formatter'] = 'showlink';
                    $formatoptions = array();
                    $formatoptions['baseLinkUrl']   = $this->col_formats[$col_name]['showlink']['baseLinkUrl'];
                    $formatoptions['idName']        = $this->col_formats[$col_name]['showlink']['idName'];
                    $formatoptions['addParam']      = $this->col_formats[$col_name]['showlink']['addParam'];
                    $formatoptions['target']        = $this->col_formats[$col_name]['showlink']['target'];
                    $cols['formatoptions'] = $formatoptions; 
                }elseif(isset($this->col_formats[$col_name]['image'])){    // custom formmater for displaying images 
                    $cols['formatter'] = '###imageFormatter###'; 
                    $cols['unformat']  = '###imageUnformatter###';
                }elseif(isset($this->col_formats[$col_name]['email'])){
                    $cols['formatter'] = 'email';
                }elseif(isset($this->col_formats[$col_name]['integer'])){
                    $cols['formatter'] = 'integer';
                    $formatoptions = array();      
                    $formatoptions['thousandsSeparator'] = $this->col_formats[$col_name]['integer']['thousandsSeparator']; 
                    $formatoptions['defaultValue']       = $this->col_formats[$col_name]['integer']['defaultValue'];
                    $cols['formatoptions'] = $formatoptions;                      
                }elseif(isset($this->col_formats[$col_name]['number'])){
                    $cols['formatter'] = 'number';
                    $formatoptions = array();      
                    $formatoptions['thousandsSeparator'] =$this->col_formats[$col_name]['number']['thousandsSeparator'];
                    $formatoptions['decimalSeparator']  = $this->col_formats[$col_name]['number']['decimalSeparator'];
                    $formatoptions['decimalPlaces']     = $this->col_formats[$col_name]['number']['decimalPlaces']; 
                    $formatoptions['defaultValue']      = $this->col_formats[$col_name]['number']['defaultValue'];
                    $cols['formatoptions'] = $formatoptions;                      
                }elseif(isset($this->col_formats[$col_name]['currency'])){
                    $cols['formatter'] = 'currency';
                    $formatoptions = array();      
                    $formatoptions['prefix']            = $this->col_formats[$col_name]['currency']['prefix']; 
                    $formatoptions['suffix']            = $this->col_formats[$col_name]['currency']['suffix'];                     
                    $formatoptions['thousandsSeparator'] =$this->col_formats[$col_name]['currency']['thousandsSeparator'];
                    $formatoptions['decimalSeparator']  = $this->col_formats[$col_name]['currency']['decimalSeparator'];
                    $formatoptions['decimalPlaces']     = $this->col_formats[$col_name]['currency']['decimalPlaces']; 
                    $formatoptions['defaultValue']      = $this->col_formats[$col_name]['currency']['defaultValue'];  
                    $cols['formatoptions'] = $formatoptions;                      
                }elseif(isset($this->col_formats[$col_name]['boolean'])){
					$formatoptions = array();                        
					$cols['formatter'] = '###booleanFormatter###'; 
                    $cols['unformat']  = '###booleanUnformatter###';
					$formatoptions['Yes']  = $this->col_formats[$col_name]['boolean']['Yes'];
                    $formatoptions['No']     = $this->col_formats[$col_name]['boolean']['No']; 
					//$cols['formatoptions'] = $this->col_formats[$col_name];
					$cols['formatoptions'] = $formatoptions;                
                }elseif(isset($this->col_formats[$col_name]['custom'])){    // custom formmater for css 
                    $cols['formatter'] = '###'.$col_name. '_customFormatter###'; 
                    $cols['unformat']  = '###'.$col_name. '_customUnformatter###';                                        
                }
            // special case for Select 
            }elseif(isset($this->col_edittypes[$col_name]) && ($this->col_edittypes[$col_name]['type']=='select')){
                $cols['formatter'] = 'select';
            }
            // set to default value based on ADOdb metatype
            // COMMENTED OUT!! Integer PK will be formatted.
            /*else{
                if($col_type == 'I'){
                    $cols['formatter'] = 'integer'; 
                    $formatoptions = array();      
                    $formatoptions['thousandsSeparator'] = ',';
                    $formatoptions['defaultValue'] = '0'; 
                    $cols['formatoptions'] = $formatoptions;                                
                }elseif($col_type == 'N'){
                    $cols['formatter'] = 'number';
                    $formatoptions = array();      
                    $formatoptions['thousandsSeparator'] = ',';
                    $formatoptions['decimalSeparator'] = '.';
                    $formatoptions['decimalPlaces'] = '2';
                    $formatoptions['defaultValue'] = '0.00'; 
                    $cols['formatoptions'] = $formatoptions;                                
                }elseif($col_type == 'D'){
                    $cols['formatter'] = 'date';
                }
            }
            */
                                    
            $cols['editoptions'] = $editoptions;                
            $cols['editrules'] = $editrules;

            $colModel[]   = $cols;        
        }

        $this->jq_colModel = $colModel;
    }     
    
    public function get_colModel(){
	
        return $this->jq_colModel;
    }        
    
    private function display_style(){
        if(!empty($this->alt_colors)){
            echo '<style type="text/css">' ."\n";
            echo '#'. $this->jq_gridName .' .ui-state-hover{background:'. $this->alt_colors['hover'] .';color:black}' ."\n";
            if($this->alt_colors['highlight']!=null)
                echo '#'. $this->jq_gridName .' .ui-state-highlight{background:'. $this->alt_colors['highlight'] .';}' ."\n";
            if($this->alt_colors['altrow']!=null)
                echo '#'. $this->jq_gridName .' .ui-priority-secondary{background:'. $this->alt_colors['altrow'] .';opacity: .7; filter:Alpha(Opacity=70); font-weight: normal; }' ."\n";
            echo '</style>' ."\n";

        }        
        
/*
        //02.21.2011 yuuki
        if(!empty($this->col_custom_css)){
            echo '<style type="text/css">' ."\n";
            echo  
            '._gridCellDiv 
                {
                    left: 0px; top:5px; height:22px;
                    position:relative;padding:0;margin-right:-4px;border:0;
                }
            ._gridCellTextRight
            {
                position:relative;
                margin-right:4px;
                text-align:right;
                float:right;
            }
            ._gridGradient{
                filter: progid:DXImageTransform.Microsoft.Gradient(StartColorStr="'.$this->col_custom_css.'", EndColorStr="white", GradientType=1);
                -ms-filter: "progid:DXImageTransform.Microsoft.Gradient(StartColorStr="'.$this->col_custom_css.'", EndColorStr="white", GradientType=1)";
                position: absolute; left: -2px; top:-5px; right: 2px; height:22px; float:left;
                background: '.$this->col_custom_css .';
                background: -webkit-gradient(linear, left top, right top, from('.$this->col_custom_css.'), to(white));
                background: -moz-linear-gradient(left, '.$this->col_custom_css.', white);
            }';
            echo '</style>' ."\n";               
        }
*/
		
    }
        
    // Desc: only include the scripts once. foriegn key indicates a detail grid. Dont' include script again
    private function display_script_includeonce(){
        if($this->sql_fkey==null){
            echo '<link rel="stylesheet" type="text/css" media="screen" href="'. ABS_PATH .'/css/'. $this->theme_name .'/jquery-ui-1.7.3.custom.css" />' ."\n";
            echo '<link rel="stylesheet" type="text/css" media="screen" href="'. ABS_PATH .'/css/ui.jqgrid.css" />' ."\n"; 
            echo '<script src="'. ABS_PATH .'/js/jquery-1.4.2.min.js" type="text/javascript"></script>' ."\n"; 
            echo '<script src="'. ABS_PATH .'/js/jquery-ui-1.7.3.custom.min.js" type="text/javascript"></script>'. "\n";
            echo '<script src="'. ABS_PATH . sprintf('/js/i18n/grid.locale-%s.js',$this->locale).'" type="text/javascript"></script>' ."\n";
            echo '<script src="'. ABS_PATH .'/js/jquery.jqGrid.min.js" type="text/javascript"></script>' ."\n";                    
            echo '<script src="'. ABS_PATH .'/js/grid.import.fix.js" type="text/javascript"></script>' ."\n";                    
        }
    }
    
    private function display_script_begin(){
        echo '<script type="text/javascript">' ."\n";
        echo 'var lastSel;';        // jqgrid variable used by inline edit OnSelect function
        echo 'jQuery(document).ready(function(){ ' ."\n";                                                                               
    }
                                             
    private function display_properties_begin(){
        echo 'var grid_'. $this->jq_gridName .' = jQuery("#'. $this->jq_gridName .'").jqGrid({'."\n";        
    }

    public function display_properties_main(){     
        echo    'url:'. $this->jq_url .",\n";
        echo    'datatype:"'. $this->jq_datatype ."\",\n";
        echo    'mtype:"'. $this->jq_mtype ."\",\n";
        echo    'colNames:'. json_encode($this->jq_colNames) .",\n";   
        echo    'colModel:'. C_Utility::indent_json(str_replace('###"', '', str_replace('"###', '', json_encode($this->jq_colModel)))) .",\n";  
        echo    'pager: '. $this->jq_pagerName .",\n";
        echo    'rowNum:'. $this->jq_rowNum .",\n";
        echo    'rowList:'. json_encode($this->jq_rowList) .",\n";
        echo    'sortname:"'. $this->jq_sortname ."\",\n";
        echo    'sortorder:"'. $this->jq_sortorder ."\",\n";
        echo    'viewrecords:'. C_Utility::literalBool($this->jq_viewrecords) .",\n";
        echo    'multiselect:'. C_Utility::literalBool($this->jq_multiselect) .",\n"; 
        echo    'caption:"'. $this->jq_caption ."\",\n";
        echo    'altRows:'. C_Utility::literalBool($this->jq_altRows) .",\n"; 
        echo    'scrollOffset:'. $this->jq_scrollOffset .",\n";   
        echo    'rownumbers:'. C_Utility::literalBool($this->jq_rownumbers) .",\n";
        echo    'forceFit:'. C_Utility::literalBool($this->jq_forceFit) .",\n";
        echo    'autowidth:'. C_Utility::literalBool($this->jq_autowidth) .",\n";
        echo    'hiddengrid:'. C_Utility::literalBool($this->jq_hiddengrid) .",\n";
        echo    'scroll:'. C_Utility::literalBool($this->jq_scroll) .",\n";           
        echo    'height:"'. $this->jq_height ."\",\n";            
        echo    'width:"'. $this->jq_width ."\",\n"; 
		echo	"sortable:true,\n"; 
		
		/*START Grouping*/		
        if($this->jq_grouping) {
		    echo    'direction:"'. $this->jq_direction ."\",\n"; //Right To Left Languages are supported.
            echo    'grouping:'. C_Utility::literalBool($this->jq_grouping) .",\n"; // This is code for grouping of row according filed
		    echo    'groupingView:{    groupField :["'.$this->jq_group_name."\" ],
								   groupSummary : [".C_Utility::literalBool($this->jq_is_group_summary)."], 
								   showSummaryOnHide : ".C_Utility::literalBool($this->jq_showSummaryOnHide).", 
								   groupColumnShow : [".C_Utility::literalBool($this->jq_is_group_fild_hidden)."],
								   groupCollapse  : ".C_Utility::literalBool($this->jq_groupcollapse) .",
								   groupText : ['<b>{0} - {1} Item(s)</b>']

								   },\n";
        }
		/*End Grouping*/
		
        echo    'gridview:'. C_Utility::literalBool($this->jq_gridview) .",\n";

        switch($this->edit_mode){
            case 'CELL':
                echo "cellEdit:true,\n"; 
                break;
            case 'INLINE':
                echo 'onSelectRow: function(id){
                        if(id && id!==lastSel){                    
                            jQuery("#'. $this->jq_gridName .'").restoreRow(lastSel);   
                            lastSel=id; 
                        }        
                        jQuery("#'. $this->jq_gridName .'").editRow(id, true,"","","","",aftersavefunc);        
                     },
                     editurl:"'. $this->jq_editurl .'"' .",\n";
                break;
            case 'FORM':
                echo 'editurl:"'. $this->jq_editurl .'"' .",\n";
                break;
            default:
                // NONE
        }        
        echo $this->ud_params;      
        
        //conditional formatting
        if(count($this->jq_cellConditions)>0) {
            $cellStr = "";
            $rowStr = "";
            $result = $this->db->select_limit($this->sql,1, 1);                    
            
            //check cell formatting
            for ($i=0;$i<count($this->jq_cellConditions);$i++){
                $cellCondition = $this->jq_cellConditions[$i];
                $colIndex = $this->db->field_index($result,$cellCondition["col"]);   
                $options = $cellCondition["options"];
                                      
                $cellStr.= "if (item.cell['$colIndex'] != null) {".$this->generate_condition($colIndex, $options["condition"],$options["value"]) ;
                
                if(!empty($cellStr)){
                    foreach ($options["css"] as $key=>$value){
                            $cellStr.=  '$("#'.$this->jq_gridName.'").setCell(item.id,'.$colIndex.',"",{"'.$key.'":"'.$value.'"});'."\n";
                    }  
                    $cellStr.= "\n".'} }'; 
                }                          
            }                                        
            
            //check row formatting
            for ($i=0;$i<count($this->jq_rowConditions);$i++){
                $rowCondition = $this->jq_rowConditions[$i];
                $colIndex = $this->db->field_index($result,$rowCondition["col"]);   
                $options = $rowCondition["options"];
                                      
                $rowStr.= "if (item.cell['$colIndex'] != null) {".$this->generate_condition($colIndex, $options["condition"],$options["value"]) ;
                
                if(!empty($cellStr)){
                    foreach ($options["css"] as $key=>$value){
                            $pos = strpos($key,"background");
                            if($pos !== false) {
                                $rowStr.= '$("#" + item.id).removeClass("ui-widget-content");';    
                            }
                            $rowStr.= '$("#" + item.id).css("'.$key.'","'.$value.'");'."\n";
                    }  
                    $rowStr.= "\n".'} }'; 
                }                          
            } 
            
            //Generate load complete event 
            if(!empty($cellStr) || !empty($rowStr)){
                echo 'loadComplete: function(data){                            
                        $.each(data.rows,function(i,item){'.$rowStr.$cellStr.' });
                },';                 
            }                            
        }                  
    }
    
    private function generate_condition($colIndex,$condition,$value)
    {
        $ret ="";
        switch ($condition){
            case "eq":   // Equals
                $ret = "\n".'if (item.cell['.$colIndex.'] == "'.$value.'") {'."\n";
                break;
            case "ne":  // Not Equals
                $ret = "\n".'if (item.cell['.$colIndex.'] != "'.$value.'") {'."\n";
                break;
            case "lt":  // Less than
                $ret = "\n".'if (item.cell['.$colIndex.'] < "'.$value.'") {'."\n";
                break;
            case "le": // Less than or Equal
                $ret = "\n".'if (item.cell['.$colIndex.'] <= "'.$value.'") {'."\n";
                break;    
            case "gt":  // Greater than
                $ret = "\n".'if (item.cell['.$colIndex.'] > "'.$value.'") {'."\n";
                break;
            case "ge":  // Greater than or Equal
                $ret = "\n".'if (item.cell['.$colIndex.'] >= "'.$value.'") {'."\n";
                break;    
            case "cn":  // Contains
                $ret = "\n".'if (item.cell['.$colIndex.'].indexOf("'.$value.'")!=-1) {'."\n";                    
                break;
            case "nc":  // Does not Contain
                $ret = "\n".'if (item.cell['.$colIndex.'].indexOf("'.$value.'")==-1) {'."\n";                    
                break;
            case "bw":  // Begins With                    
                $ret = "\n".'if (item.cell['.$colIndex.'].indexOf("'.$value.'")==0) {'."\n";                    
                break;
            case "bn":  // Not Begins With
                $ret = "\n".'if (item.cell['.$colIndex.'].indexOf("'.$value.'")!=0) {'."\n";                    
                break;
            case "ew":  // Ends With             
                $ret = "\n".'if (item.cell['.$colIndex.'].substr(-1)==="'.$value.'") {'."\n";                    
                break;
            case "en":  // Not Ends With
                $ret = "\n".'if (item.cell['.$colIndex.'].substr(-1)!=="'.$value.'") {'."\n";                                   
                break;
        }                
        return  $ret;
    }
    
    private function display_subgrid(){
        if($this->obj_subgrid != null){
            echo 'subGrid: true,'. "\n";                                                        
            echo 'subGridRowExpanded: function(subgrid_id, row_id) {
                    var subgrid_table_id, pager_id;
                    subgrid_table_id = subgrid_id+"_t";
                    pager_id = "p_"+subgrid_table_id;' ."\n";
            // echo 'alert(subgrid_id);alert(row_id)';
            echo '  $("#"+subgrid_id).html("<table id=\'"+subgrid_table_id+"\' class=\'scroll\'></table><div id=\'"+pager_id+"\' class=\'scroll\'></div>");' ."\n";
            echo '  jQuery("#"+subgrid_table_id).jqGrid({ ' ."\n";
            
            $this->obj_subgrid->set_jq_url($this->obj_subgrid->get_jq_url().'+row_id', false);      
            $this->obj_subgrid->set_jq_pagerName('pager_id', false);
            $this->obj_subgrid->set_multiselect(false);
            $this->obj_subgrid->set_sortname($this->obj_subgrid->get_sql_key());
            $this->obj_subgrid->set_dimension($this->jq_width-100);
            
            $this->obj_subgrid->display_properties_main();

            echo '      });' ."\n";
            echo $this->obj_subgrid->col_custom . "\n";
            echo '  jQuery("#"+subgrid_table_id).jqGrid("navGrid","#"+pager_id,{edit:false,add:false,del:false})'. "\n";
            echo '},' ."\n";

            echo 'subGridRowColapsed: function(subgrid_id, row_id) {},';
        }
    }
       
    // Desc: display master detail     
    // Modification: 01.26.2011 yuuki
    // added for loop for each detail grid
    private function display_masterdetail(){        
        if($this->obj_md != null){          
            echo 'onSelectRow: function(ids) {            
                    if(ids == null) {                        
                        ids=0;';
                    for($i=0;$i<count($this->obj_md);$i++){                        
                        echo
                            "\n".'if(jQuery("#'. $this->obj_md[$i]->get_jq_gridName().'").jqGrid("getGridParam","records") >0 )
                            {                             
                                jQuery("#'. $this->obj_md[$i]->get_jq_gridName() .'").jqGrid("setGridParam",{url:"'. ABS_PATH .'/masterdetail.ajax.php?gn='.$this->obj_md[$i]->get_jq_gridName().$this->callbackstring.'&id="+ids,page:1}).trigger("reloadGrid");
                            }
                            else {                             
                                jQuery("#'. $this->obj_md[$i]->get_jq_gridName() .'").jqGrid("setGridParam",{url:"'. ABS_PATH .'/masterdetail.ajax.php?gn='.$this->obj_md[$i]->get_jq_gridName().$this->callbackstring.'&id="+ids,page:1}).trigger("reloadGrid");            
                            }';
                    }
                    echo ' } else {'; 
                    
                    for($i=0;$i<count($this->obj_md);$i++){                        
                        echo
                            "\n".'if(jQuery("#'. $this->obj_md[$i]->get_jq_gridName().'").jqGrid("getGridParam","records") >0 )
                            {                                
                                jQuery("#'. $this->obj_md[$i]->get_jq_gridName() .'").jqGrid("setGridParam",{url:"'. ABS_PATH .'/masterdetail.ajax.php?gn='.$this->obj_md[$i]->get_jq_gridName().$this->callbackstring.'&id="+ids,page:1}).trigger("reloadGrid");
                            }
                            else {                                
                                jQuery("#'. $this->obj_md[$i]->get_jq_gridName() .'").jqGrid("setGridParam",{url:"'. ABS_PATH .'/masterdetail.ajax.php?gn='.$this->obj_md[$i]->get_jq_gridName().$this->callbackstring.'&id="+ids,page:1}).trigger("reloadGrid");            
                            }';
                    }              
                    echo '} },'."\n"; 
        }else{
            // TBD
        }
    }
    // Desc: end of main jqGrid (before toolbar)
    private function display_properties_end(){
        echo    'loadtext:"'. $this->jq_loadtext ."\"\n";  // last properties - no ending comma.                                                                                                                                             
        echo    '});' ."\n";         
    }
    
    private function display_toolbar(){
        switch($this->edit_mode){       
            case 'FORM':
            case 'INLINE':
                echo    'jQuery("#'. $this->jq_gridName .'").jqGrid("navGrid", '. $this->jq_pagerName .",\n";
                
                echo       '{edit:'. ((strrpos($this->edit_options,"U")!==false && $this->edit_mode!='INLINE')?'true':'false') 
                          .',add:'.  ((strrpos($this->edit_options,"C")!==false)?'true':'false')
                          .',del:'.  ((strrpos($this->edit_options,"D")!==false)?'true':'false') 
                          .',view:'. ((strrpos($this->edit_options,"R")!==false && $this->edit_mode!='INLINE')?'true':'false') 
                          .',search:false' 
                          .',excel:'. (($this->export_type!=null)?'true':'false').'}, ';
                
                echo       '{   // edit options  
                                top: ($(window).height()- 300) / 2+$(window).scrollTop(),
                                left:($(window).width() - 300) / 2+$(window).scrollLeft(),
                                jqModal:true,
                                checkOnUpdate:false,
                                savekey: [true,13], 
                                navkeys: [false,38,40], 
                                checkOnSubmit : false, 
                                reloadAfterSubmit:false, 
                                closeOnEscape:true, 
                                closeAfterEdit:true,';
                                if($this->debug){ echo 'afterSubmit:function(d,a){$("#ajaxresponse").html("<pre>"+d.responseText+"</pre>");},';}
                echo           'bottominfo:"* required",
                                viewPagerButtons:true,
                                beforeShowForm: function(frm) {'; 
                                
                                    foreach($this->col_readonly as $key => $value){
                                        echo '$("#'. $value .'").attr("readonly","readonly");';                                     
                                    }
                                
                echo     '      }
                          }, 
                            {   // add options 
                                top: ($(window).height()- 300) / 2+$(window).scrollTop(),
                                left:($(window).width() - 300) / 2+$(window).scrollLeft(),
                                jqModal:true,
                                checkOnUpdate:false,
                                savekey: [true,13], 
                                navkeys: [false,38,40], 
                                checkOnSubmit : false, 
                                reloadAfterSubmit:false, 
                                closeOnEscape:true, 
                                closeAfterEdit:true,';
                                if($this->debug){ echo 'afterSubmit:function(d,a){$("#ajaxresponse").html("<pre>"+d.responseText+"</pre>");},';}
                echo           'bottominfo:"* required",                                
                                viewPagerButtons:true,
                                beforeShowForm: function(frm) {';
                                
                                foreach($this->col_readonly as $key => $value){
                                    echo '$("#'. $value .'").removeAttr("readonly");';                                     
                                }

                echo     '      }
                            }, 
                            {   // del options    
                                top: ($(window).height()- 300) / 2+$(window).scrollTop(),
                                left:($(window).width() - 300) / 2+$(window).scrollLeft(),                            
                                reloadAfterSubmit:false,
                                jqModal:false,';
                                if($this->debug){ echo 'afterSubmit:function(d,a){$("#ajaxresponse").html("<pre>"+d.responseText+"</pre>");},';}
                echo           'bottominfo:"* required", 
                                closeOnEscape:true
                            }, 
                            {
                                // view options        
                                top: ($(window).height()- 300) / 2+$(window).scrollTop(),
                                left:($(window).width() - 300) / 2+$(window).scrollLeft(),
                                navkeys: [false,38,40], height:250,jqModal:false,closeOnEscape:true
                            }, 
                            {closeOnEscape:true} // search options 
                         );' ."\n";
            
                break;
            case 'NONE':
                echo    'jQuery("#'. $this->jq_gridName .'").jqGrid("navGrid", '. $this->jq_pagerName .",\n";                
                echo   '{edit:false,add:false,del:false,view:false'. 
                        ',search:false' .
                        ',excel:'. (($this->export_type!=null)?'true':'false').'}, {})' ."\n"; 
                break; 
        } // switch  
                                   
        // resizable grid (beta - jQuery UI)           
        if($this->jqu_resize['is_resizable']){
            echo 'jQuery("#'. $this->jq_gridName .'").jqGrid("gridResize",{minWidth:'. $this->jqu_resize['min_width'] .',minHeight:'. $this->jqu_resize['min_height'] .'});' ."\n";
        }          
        
        // inline search
        if($this->has_tbarsearch){
            echo 'jQuery("#'. $this->jq_gridName .'").jqGrid("navButtonAdd",'. $this->jq_pagerName .',{caption:"Search",title:"Toggle inline search", buttonicon :"ui-icon-pin-s",
                        onClickButton:function(){
                            grid_'. $this->jq_gridName .'[0].toggleToolbar();
                        } 
                    });'."\n";
            echo 'jQuery("#'. $this->jq_gridName .'").jqGrid("navButtonAdd",'. $this->jq_pagerName .',{caption:"Clear",title:"Clear Search",buttonicon :"ui-icon-refresh",
                        onClickButton:function(){
                            grid_'. $this->jq_gridName .'[0].clearToolbar();
                        } 
                    });'."\n";                
            echo 'jQuery("#'. $this->jq_gridName .'").jqGrid("filterToolbar");'."\n";
            echo 'grid_'. $this->jq_gridName .'[0].toggleToolbar();'."\n";   // hide inline search by default
        } 
        
        //advanced search
        if($this->advanced_search){
            echo 'jQuery("#'. $this->jq_gridName.'")
                .navGrid('.$this->jq_pagerName.',{edit:false,add:false,del:false,search:false,refresh:false})
                .navButtonAdd('.$this->jq_pagerName.',{
                    caption:"", 
                    buttonicon:"ui-icon-search", 
                    onClickButton: function(){ 
                        jQuery("#'.$this->jq_gridName.'").jqGrid("searchGrid", {multipleSearch:true});         
                }, 
                position:"first"          
            });'."\n";                              
        }
                    
        // Excel Export is not documented well. See JS source:
        // http://www.trirand.com/blog/phpjqgrid/examples/functionality/excel/default.php
        if($this->export_type!=null){
            echo 'jQuery("#'. $this->jq_gridName .'").jqGrid("navButtonAdd",'. $this->jq_pagerName .',{caption:"",title:"Export to '. $this->export_type .'",
                        onClickButton:function(e){
                            try{                                    
                                grid_'. $this->jq_gridName .'.jqGrid("excelExport",{url:"'. $this->export_url .'"});
                            } catch (e) {
                                window.location= "'. $this->export_url .'?oper=excel";
                            }

                        }
                    });'."\n";                           
        }        
    }    
    
    // Source for unformatter: http://www.trirand.net/forum/default.aspx?g=posts&t=31
    private function display_script_end(){
        echo "\n". '});' ."\n";                               
        echo 'function getSelRows()
             {
                var rows = $("#'.$this->jq_gridName.'").jqGrid("getGridParam","selarrrow");                               
                return rows;                
             }' ."\n";                                                  
        echo '// cellValue - the original value of the cell
              // options - as set of options, e.g
              // options.rowId - the primary key of the row
              // options.colModel - colModel of the column
              // rowObject - array of cell data for the row, so you can access other cells in the row if needed ' ."\n";
        echo 'function imageFormatter(cellValue, options, rowObject)
             {
                return "<img src=\"" + cellValue + "\" originalValue=\""+ cellValue +"\" title=\""+ cellValue +"\">";
             }' ."\n";
        echo '// cellValue - the original value of the cell
              // options - as set of options, e.g
              // options.rowId - the primary key of the row
              // options.colModel - colModel of the column
              // cellObject - the HMTL of the cell (td) holding the actual value ' ."\n";
        echo 'function imageUnformatter(cellValue, options, cellObject)
             {      
                return $(cellObject.html()).attr("originalValue");
             }' ."\n";
		 echo 'function booleanFormatter(cellValue, options, rowObject)
             {
				var op;
				op = $.extend({},options.colModel.formatoptions);
                myCars=new Array(); 
				//alert(op.No);
				//mycars[cellValue]=  op.boolean.No;
				//mycars[cellValue]=  op.boolean.Yes;
				myCars[op.No]="No";       
				myCars[op.Yes]="Yes";
				//alert(options[boolean]);
				return myCars[cellValue];
             }' ."\n";
        
        echo 'function booleanUnformatter(cellValue, options, cellObject)
             {    var op;
				  op = $.extend({},options.colModel.formatoptions);
				  //alert(op.No);
				  if(cellValue=="No")
				  return (op.No);
				  else
				  return (op.Yes);
            //alert(op.boolean.Yes)
            //return (op.boolean.cellValue);
              //  myCars=new Array(); 
			//	myCars["No"]=\'0\';       
			//	myCars["Yes"]=1;
				//alert(myCars[cellValue]);
				//alert(options.colModel.formatoptions[1]);
				//return myCars[cellValue];
             }' ."\n";
        
        //02.18.2011 yuuki    
        echo $this->col_custom;         
        
        echo '// display ajax reponse used for debug for inline edit'."\n";
        echo 'function aftersavefunc(rowid, d){';
            if($this->debug) echo '$("#ajaxresponse").html("<pre>"+d.responseText+"</pre>");';
        echo '}' ."\n";
        echo '</script>' ."\n";
    }
       
    // Desc: html element as grid placehoder 
    // Must strip out # sign. use str_replace() on pagerName because it also include (")
    private function display_container(){
        echo '<table id="'. $this->jq_gridName .'"></table>' ."\n";
        echo '<div id='. str_replace("#", "", $this->jq_pagerName) .'></div>' ."\n";
        echo '<br />'. "\n";   
    }
    
    // Desc: debug function. dump the grid objec to screen
    private function display_debug(){        
        echo '<hr size="1" />';

        print("<b>CONTROL VALIDATION</b>");        
        print("<pre id='branch1' style='border:1pt dotted black;padding:5pt;background:red;color:white;display:block'>");
        if($this->obj_md!=null && $this->edit_mode=='INLINE'){
            print("\n".'- Grid with both inline edit and master/detail enabled is currently not supported.');
        }
        if($this->jq_multiselect && $this->edit_mode=='NONE'){
            print("\n".'- Grid has multiselect enabled. However, the grid has not been set to be editable.');            
        }        
        if($this->jq_scroll){           
            print("\n".'- Scrolling (set_sroll)is enabled. As a result, pagination is disabled.');            
        }        
        print("</pre>");            

        print("<b>DATAGRID OBJECT</b>");
        print("<pre id='branch2' style='border:1pt dotted black;padding:5pt;background:#E4EAF5;display:block'>");
        print_r($this);
        print("</pre>");
        
        print("<b>SESSION OBJECT</b>");        
        print("<pre id='branch3' style='border:1pt dotted black;padding:5pt;background:#FFDAFA;display:block'>");
        print("<br />SESSION ID: ". session_id() ."<br />");
        print("SESSION KEY: ". GRID_SESSION_KEY.'_'.$this->jq_gridName ."<br />");
        print_r(C_Utility::indent_json(str_replace("\u0000", " ", json_encode($_SESSION)))); // \u0000 NULL
        print("</pre>");
    }
    
    // Desc: display ajax server response message in debug 
    private function display_ajaxresponse(){
        echo '<hr size="1" />';
        
        print("<b>AJAX RESPONSE</b>");        
        print("<div id='ajaxresponse' style='border:1pt dotted black;padding:5pt;background:yellow;color:black;display:block'>");
        print("</div>");            
    }

    // Desc: display finally
    public function display(){
		if($this->debug){ print("<h1>". $this->_ver_num ."</h1>");}

        $this->prepare_grid();

        $this->display_style();
        $this->display_script_includeonce();
        $this->display_script_begin();  
        $this->display_properties_begin();                                                                                 
        $this->display_properties_main();                        
        $this->display_subgrid();
        $this->display_masterdetail();
        $this->display_properties_end();        
        $this->display_toolbar();        
        $this->display_script_end();              
        $this->display_container();
        
        if($this->debug){
            $this->display_ajaxresponse();
            $this->display_debug();
        }
                
        //01.26.2011 yuuki
        if($this->obj_md!=null){  
            for($i=0;$i<count($this->obj_md);$i++) {
                $this->obj_md[$i]->display();
            }
            // save obj to session (used by data.ajax.php). 
            // *** Note **** Must implement __sleep to finalize serialbe properties
            $_SESSION[GRID_SESSION_KEY.'_'.$this->jq_gridName] = serialize($this);         
        }
        
        // save obj to session (used by data.ajax.php). 
        // *** Note **** Must implement __sleep to finalize serialbe properties
        $_SESSION[GRID_SESSION_KEY.'_'.$this->jq_gridName] = serialize($this);         

    }

    // Desc: PHP magic function                                                                                                 
    // executed prior to any serialization  
    public function __sleep(){
        // return all properties of an object in scope
        // reference: http://www.eatmybusiness.com/food/2010/01/11/php-getting-__sleep-to-return-all-properties-of-an-object/136/
        return array_keys(get_object_vars($this));    
    }        
    
    // Desc: PHP magic function 
    // reconstruct any resources that the object may have before unserialization.
    public function __wakeup(){        
    }                        
    
    // Desc: set sql string
    public function set_sql($sqlstr){
        $this->sql = $sqlstr;
    }     
    
	// Desc:For query filter
	public function set_query_filter($where){
		if($where!=''){
		 $this->sql.='  AND '.$where;
		}	
	}

	public function get_filter(){
		return $this->filter_sql;
		
	}

    // Desc: set table name in sql string. Must call this function on client. 
    public function set_sql_table($sqltable){
        $this->sql_table = $sqltable;
    }  
    
    public function get_sql_table(){
        return $this->sql_table;
    }
    
    // Desc: set data url
    // The 2nd parameter adds quote around the pager name
    // It should set to false when called by subgrid, which is a dynamic value using javascript
    public function set_jq_url($url, $add_quote=true){
        $this->jq_url = ($add_quote)?('"'.$url.'"'):$url;        
    }
    
    public function get_jq_url(){
        return $this->jq_url;
    }

    public function set_jq_datatype($datatype){
        $this->jq_datatype = $datatype;
    }
    
    public function get_jq_datatype(){
        return $this->jq_datatype;
    }
        
   
    // Desc: set a hidden column 
    // the 2nd parameter indicates whether it's also hidden during add/edit, applicalbe ONLY to form
    // The value defaults to editable. More:http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules
    public function set_col_hidden($col_name, $edithidden=true){
        $this->col_hiddens[$col_name]['edithidden'] = $edithidden;        
    }
    
    public function get_col_hiddens(){
        return $this->col_hiddens;
    }
    
    
    
        // Desc: set read only columns
    public function set_col_readonly($arr){
        $this->col_readonly = preg_split("/[\s]*[,][\s]*/", $arr);    
    }
    
    public function get_col_readonly(){
        return $this->col_readonly;
    }
    
    // Desc: get sql string
    public function get_sql(){
        return $this->sql;
    }
    
    //Desc: get the currently set database
    public function get_db_connection(){
        return $this->db_connection;    
    }
    
    // Desc: set sql PK
    public function set_sql_key($sqlkey){
        $this->sql_key = $sqlkey;    
    }

    // Desc: get sql PK
    public function get_sql_key(){
        return $this->sql_key;
    }
    
    // Desc: set sql Foreign PK
    public function set_sql_fkey($sqlfkey){
        $this->sql_fkey = $sqlfkey;    
    }

    // Desc: get sql Foreign PK
    public function get_sql_fkey(){
        return $this->sql_fkey;
    }
    
    // Desc: get number of rows
    public function get_num_rows(){
        return $this->_num_rows;
    }
    
    // Desc: vertical scroll to load data. pager is automatically disabled as a result
    // The height MUST NOT be 100%. The default height is 400 when scroll is true.
    public function set_scroll($scroll, $h='400'){
        $this->jq_scroll = $scroll;
        $this->jq_height = $h;
    }
    
    // Desc: edit url (edit.ajax.php)
    public function set_jq_editurl($url){
        $this->jq_editurl = $url;
    }
    
    // Desc: enable edit (cell, inline, form), default to FORM mode   
    public function enable_edit($edit_mode = 'FORM', $options='CRUD'){
        switch($edit_mode)    {
            case 'CELL':
                $this->jq_cellEdit = true; 
                break;
            case 'INLINE':
            case 'FORM':
                $this->jq_editurl = ABS_PATH .'/edit.ajax.php?gn='.$this->jq_gridName.$this->callbackstring;           
                break;
            default:
                // NONE
        } 
        $this->edit_mode = $edit_mode;
        $this->edit_options = $options;           
    }
    
    // Desc: enable integrated toolbar search
    public function enable_search($can_search){
        $this->has_tbarsearch = $can_search;    
    }
    
    //02.12.2011 yuuki
    public function enable_advanced_search($has_adsearch){
			$this->advanced_search = $has_adsearch;
    }
    
    // Desc: sel multiselect       
    public function set_multiselect($multiselect){
        $this->jq_multiselect = $multiselect;
    }
    
    public function has_multiselect(){
        return $this->jq_multiselect;
    }
    
    // Desc: set require column when edit
    public function set_col_required($arr){
        $this->col_required = preg_split("/[\s]*[,][\s]*/", $arr);    
    }
    
    // Desc: set column title
    public function set_col_title($col_name, $new_title){
        $this->col_titles[$col_name] = $new_title;
    }
                                 
    // Desc: get column titles
    public function get_col_titles(){
        return $this->col_titles;
    }


    /* *************************** formatter helper functions ********************************  */
    /* All can be replaced by set_col_format() with specific 3rd format options parameter       */
    /* ******************************************************************************************/    
    // Desc: set column value as hyper link 
    public function set_col_link($col_name, $target="_new"){
        $this->col_formats[$col_name]['link'] = array("target"=>$target);
        // $this->col_links[$col_name] = array("target"=>$target);
    }

    // Desc: set DYNAMIC column link and value in a two dimensional array;
//    public function set_col_dynalink($col_name, $baseLinkUrl="", $idName="id",$addParam="", $target="_new"){
//        $this->col_formats[$col_name]['showlink'] = array("baseLinkUrl"=>$baseLinkUrl, 
//                                                          "idName"=>$idName, 
//                                                          "addParam"=>$addParam, 
//                                                          "target"=>$target);                                                                              
//    }
    
    // Desc: set column as currency when displayed
    public function set_col_currency($col_name, $prefix='$', $suffix='', $thousandsSeparator=',', $decimalSeparator='.', 
                                     $decimalPlaces='2', $defaultValue='0.00'){
         $this->col_formats[$col_name]['currency'] = array("prefix" => $prefix,
                                                            "suffix" => $suffix,
                                                            "thousandsSeparator" => $thousandsSeparator,
                                                            "decimalSeparator" => $decimalSeparator,
                                                            "decimalPlaces" => $decimalPlaces,
                                                            "defaultValue" => $defaultValue);
    }
    
    // Desc: set image column
    public function set_col_img($col_name, $addParma=''){
        $this->col_formats[$col_name]['image'] = array('addParma' => $addParma); 
    }
    /* ***************** end of formatter helper functions ********************************/    
    
    // Desc: jqGrid formatter: integer, number, currency, date, link, showlink, email, select (special case)
    public function set_col_format($col_name, $format, $formatoptions=array()){
        $this->col_formats[$col_name][$format] = $formatoptions;    

    }
    
    //02.26.2011 yuuki
    //Desc: formats a url with id from another column
    public function set_col_dynalink($col_name, $baseLinkUrl="", $idName="id",$addParam="",$target="_new"){
        $sFormatter = "function ".$col_name."_customFormatter(cellValue, options, rowObject){ %s }";
        $sUnformatter = "function ".$col_name."_customUnformatter(cellValue, options, rowObject){ %s }";        
        $results = $this->db->select_limit($this->sql,1, 1);
                 
        $sVal = '                               
        var idVal = rowObject['.$this->db->field_index($results,$idName).'];
        var params = "?'.$idName.'=" + idVal + "'.$addParam.'";
        var url = \''.$baseLinkUrl.'\' + params;
        
        return \'<a href="\'+url+\'" target="'.$target.'" value="\' + cellValue + \'">\'+cellValue+\'</a>\';
        ';
        $sFormatter = sprintf($sFormatter,$sVal);
        $sUnformatter = sprintf($sUnformatter,'var obj = $(rowObject).html(); return $(obj).attr("value");');
        $this->col_custom .= $sFormatter . "\n" . $sUnformatter;                            
        $this->col_formats[$col_name]['custom'] = $addParam;                          
    
    }
		
/*
    //02.17.2011 yuuki
    //Desc: Creates a custom format on the specified column
    public function set_custom_format($col_name, $format, $formatoptions=array()){        
        $sFormatter="function " .$col_name."_customFormatter(cellValue, options, rowObject){ %s }";
        $sUnformatter="function " .$col_name."_customUnformatter(cellValue, options, rowObject){ %s }";
        $sVal ="";        
        
        
        if ($format=="checkbox"){           
           $sVal = '
            var val="";
            
            if(cellValue == 1 || cellValue == true || cellValue.toLowerCase() =="yes")
                val = "checked";
            
            return "<input value=" + cellValue + " type=\"checkbox\" " + val +"/>" ;
            
            ';                
        }
        else if ($format=="css"){                             
           foreach ($formatoptions as $key=>$value) { 
               $sVal .= $key.":".$value.";";               
            }    
            $sVal = 'return "<span value=" + cellValue + " style=\"display:block;background-image:none;margin-right:-2px;margin-left:-2px;height:14px;padding:5px;'.$sVal.'\">"+cellValue+"</span>";';                 
        }                     
        else if ($format=="bar"){
            $this->col_custom_css = $formatoptions;
            $sVal = '
                var dataAsNumber = parseFloat(cellValue); 
                 
                var percentVal = parseInt(cellValue);
                return \'<div value=\' + cellValue + \' class="_gridCellDiv"><div class="_gridGradient" style="width:\'+
                        percentVal+\'%;"></div><div class="_gridCellTextRight">\'+cellValue +
                        \'</div></div>\'
                ';            
        }    
        
        $sFormatter = sprintf($sFormatter,$sVal);
        $sUnformatter = sprintf($sUnformatter,'var obj = $(rowObject).html(); return $(obj).attr("value");');
        $this->col_custom .= $sFormatter . "\n" . $sUnformatter;                            
        $this->col_formats[$col_name]['custom'] = $formatoptions;                          
    }
*/	


	public function set_conditional_value($col_name, $condition="", $formatoptions=array()){        
		$sFormatter="function " .$col_name."_customFormatter(cellValue, options, rowObject){ %s }"."\n";
		$sUnformatter="function " .$col_name."_customUnformatter(cellValue, options, rowObject){ %s }"."\n";
			
			$sVal = "\n".
			'if(cellValue'.$condition.'){'."\n".
			'	return "<span value=\'"+cellValue+"\''. (isset($formatoptions["TCellStyle"])?' class=\''.$formatoptions["TCellStyle"].'\'':'') .'>'. (isset($formatoptions["TCellValue"])?$formatoptions["TCellValue"]:'"+cellValue+"').'</span>";'."\n".                 	
			'}else{'."\n".
			'	return "<span value=\'"+cellValue+"\''. (isset($formatoptions["FCellStyle"])?' class=\''.$formatoptions["FCellStyle"].'\'':'') .'>'. (isset($formatoptions["FCellValue"])?$formatoptions["FCellValue"]:'"+cellValue+"').'</span>";'."\n".                 
			'}'."\n";                			
			
		$sFormatter = sprintf($sFormatter,$sVal);
		$sUnformatter = sprintf($sUnformatter,'var obj = $(rowObject).html(); return $(obj).attr("value");');
		$this->col_custom .= $sFormatter . "\n" . $sUnformatter;                            
		$this->col_formats[$col_name]['custom'] = $formatoptions;                          
	}
  
    // Desc : formats a cell or row based on the specified condition  
    public function set_conditional_format($col_name, $type, $formatoptions=array()){        
        if($type =="ROW") {
            $this->jq_rowConditions[] = array("col"=>$col_name,"options"=>$formatoptions);
        }
        else if ($type == "CELL"){
            $this->jq_cellConditions[] = array("col"=>$col_name,"options"=>$formatoptions);            
        }                
    }
    
    // Desc: set grid height and width, the default height is 100%
    public function set_dimension($w, $h='100%'){
        $this->jq_width=$w;
        $this->jq_height=$h;    
    }
    
    // Desc: enable resizable grid(through jquery UI. Experimental feature)
    public function enable_resize($is_resizable, $min_w=350, $min_h=80){
        $this->jqu_resize["is_resizable"]   = $is_resizable; 
        $this->jqu_resize["min_width"]      = $min_w; 
        $this->jqu_resize["min_height"]     = $min_h;    
    }

    // Desc: master detail. This is different from subgrid 
    // Modification - 01.26.2011 yuuki
    // added parameter : $gdNo -> Grid Detail Number to have a unique identity for each detail grid 
    public function set_masterdetail($obj_grid, $fkey){
        $gdNo = count( $this->obj_md)+1;
        
        if($obj_grid instanceof C_DataGrid){                 
            $obj_grid->set_jq_gridName($this->jq_gridName .'_d'.$gdNo);
            $obj_grid->set_jq_pagerName(trim($this->jq_pagerName, '"') .'_d'.$gdNo);                          
            $obj_grid->set_jq_url(ABS_PATH .'/masterdetail.ajax.php?gn='. $obj_grid->jq_gridName .$this->callbackstring.'&id=');
            $obj_grid->set_jq_editurl(ABS_PATH .'/edit.ajax.php?gn='. $obj_grid->jq_gridName.$this->callbackstring.'&src=md');
            $obj_grid->set_sql_fkey($fkey);
            $obj_grid->enable_search(false);       
            $obj_grid->prepare_grid();             
            
            $this->obj_md[] = $obj_grid;                          
        }else{
            echo 'Invalid master/detail object. Error 102.';
        }        
    }
     
    // Desc: use a grid as subgrid. Must pass the foreign key as second parameter
    // *** Note ***
    // It's very important to call prepara_grid() method first before make grid as a subgrid
    // Though it's 'possible, but editing is not supported in subgrid. 
    public function set_subgrid($obj_grid, $fkey){        
        if($obj_grid instanceof C_DataGrid){                               
            $this->jq_gridview = false;     // MUST disable load all data at once (slower)
            $obj_grid->set_jq_url(ABS_PATH .'/subgrid.ajax.php?gn='. $this->jq_gridName.$this->callbackstring.'&id=');
            $obj_grid->set_sql_fkey($fkey);
            $obj_grid->set_caption('');  // remove caption
            $obj_grid->prepare_grid();             
            
            $this->obj_subgrid = $obj_grid;                          
        }else{
            echo 'Invalid subgrid object. Error 101.';
        }
    }

    // Desc: set pager name. 
    // *** Note *** 
    // The 2nd parameter adds quote around the pager name
    // It should set to false when called by subgrid, which is a dynamic value using javascript
    public function set_jq_pagerName($pagerName, $add_quote=true){
        $this->jq_pagerName = ($add_quote)?('"'.$pagerName.'"'):$pagerName;    
    }
         
    // Desc: set grid name
    public function set_jq_gridName($gridName){
        $this->jq_gridName = $gridName;
        $this->jq_pagerName = '"#'. $gridName .'_pager1"';  // Notice the double quote;
        $this->jq_url = '"'. ABS_PATH .'/data.ajax.php?gn='.$gridName.$this->callbackstring.'"'; 
        $this->export_url = ABS_PATH .'/export.ajax.php?gn='. $this->jq_gridName.$this->callbackstring;  
    }

    // Desc: get grid name
    public function get_jq_gridName(){
        return $this->jq_gridName;    
    }   
     
    // Desc: set sort name
    public function set_sortname($sortname){
        $this->jq_sortname = $sortname;    
    }   
 
    public function enable_export($type='EXCEL'){
        $this->export_type = $type;
    }
                                                                              
    // Desc: set control used during editing
    // ### Note: The function can probably be improved using the cls_control.php later ###s
    // dataUrl is only valid when type equal to 'select'
    // multiple indicates whether it's a multi-value data
    // Modification: 
    // 02.01.2011 yuuki: Check if the key-value pair parameter is an sql statement
    public function set_col_edittype($col_name, $ctrl_type, $keyvalue_pair=null, $multiple=false, $dataUrl=null,$extra_params=null){               
        if($ctrl_type == "select") {            
            $regex = "/^(SELECT|Select|select)([\s]|[a-zA-Z0-9\.\*-_,]|[\s])+[(FROM|From|from)]/";         
            $data ="";
            if (preg_match($regex , $keyvalue_pair))               
            {                                
                $result = $this->db->select_limit_array($keyvalue_pair,-1,0);            
                for($i=0;$i<count($result);$i++){
                    if(count($result[$i])==2) {
                        $key   = $i;
                        $value = $result[$i][0];
                    }
                    else if (count($result[$i])> 2) {
                        $key = $result[$i][$col_name];                       
                             
                        for ($j=0;$j < count($result[$i]);$j++) {
                            if( $key!= $result[$i][$j]) {
                                $value = $result[$i][$j];
                                break;
                            }
                        }
                    }
                    $data = $data.$key.":".$value.";";
                }            
                $keyvalue_pair = substr($data,0,strlen($data)-1);
            }
        }
               
        $this->col_edittypes[$col_name]['type']         = $ctrl_type;
        $this->col_edittypes[$col_name]['value']        = $keyvalue_pair;
        $this->col_edittypes[$col_name]['multiple']     = $multiple;       
        $this->col_edittypes[$col_name]['dataUrl']      = $dataUrl;
        $this->col_edittypes[$col_name]['extra_params'] = $extra_params;
    }

    // Desc: overwrite color properties of jQuery UI: ui-state-hover, ui-state-highlight
    // Alternatively, user can directly declare style in HTML to overwrite. If done so, additional css properties other
    // than background color can be used in CSS class. For example:
    /* <style>
        #list1 .ui-state-hover{background:blue;[other properties]}
        #list1 .ui-state-highlight{background:red;[other properties]}
        #list1 .ui-priority-secondary{background:yellow;[other properties]}    
       </style>
    */ 
    public function set_row_color($hover_color, $highlight_color=null, $altrow_color=null){
        $this->alt_colors['hover'] = $hover_color;
        $this->alt_colors['highlight'] = $highlight_color;
        $this->alt_colors['altrow'] = $altrow_color;                                     
    }
    public function set_conditional_row_color($colName, $condition=array(),$default=""){
        $this->jq_conditionalRows[] = array("col"=>$colName,"default"=>$default,"condition"=>$condition);
    }
    
    public function set_conditional_cell_color($colName, $condition=array(),$default=""){
        $this->jq_conditionalRows[] = array("col"=>$colName,"default"=>$default,"condition"=>$condition);                                             
    }
    
    
    // Desc: overwrite color properties of jQuery UI: ui-state-hover, ui-state-highlight, ui-priority-secondary 
    // Alternatively, user can directly declare style in HTML to overwrite. If done so, additional css properties other
    // than background color can be used in CSS class. For example:
    /* <style>
        #list1 .ui-state-hover{background:blue;[other properties]}
        #list1 .ui-state-highlight{background:red;[other properties]}
        #list1 .ui-priority-secondary{background:yellow;[other properties]}        
       </style>
    */ 

    // Desc: set jQuery theme
    public function set_theme($theme){
        $this->theme_name = $theme;
    }

    // Desc: set locale
    public function set_locale($locale){
        $this->locale = $locale;
    }
    
    // Desc: enable debug
    public function enable_debug($debug){
        $this->debug = $debug;
        $this->db->db->debug = $debug;
    }
    
    // Desc: set caption text
    public function set_caption($caption){
        $this->jq_caption = $caption;
    }
    
    // Desc: set page size
    // Note: pagination is disabled when set_scroll is set to true. 
    // The grid height is set in the 2nd param of set_scroll(). See method for more info
    public function set_pagesize($pagesize){
        $this->jq_rowNum = $pagesize;
    }
    
    // Desc: boolean whether display sequence number to each row
    public function enable_rownumbers($has_rownumbers){
        $this->jq_rownumbers = $has_rownumbers;    
    }

	// set coulmn width
	 public function set_col_width($col_name, $width){
        $this->col_widths[$col_name]['width'] = $width;        
    }
    // get coulmn width
    public function get_col_width(){
        return $this->col_widths;
    }

    // set coulmn width
     public function set_col_align($col_name, $align="left"){
        $this->col_aligns[$col_name]['align'] = $align;        
    }
    // get coulmn width
    public function get_col_align(){
        return $this->col_aligns;
    }
    
	public function set_group_properties($feildname, $groupCollapsed=false, $showSummaryOnHide=true){
        $this->jq_grouping=true;
        $this->jq_is_group_fild_hidden =true;    
        $this->jq_group_name=$feildname;
        $this->jq_groupcollapse=$groupCollapsed;
        $this->jq_showSummaryOnHide=$showSummaryOnHide;
	}

	public function set_group_summary($col_name, $summaryType){
        $this->jq_is_group_summary=true;    
		$this->jq_summary_col_name[$col_name]['summaryType'] = $summaryType;    
	}              
}
?>
