<?php
/**
 *	class to create Phrame Mapping arrays
 *
 *	@author		Jason E. Sweat	
 *	@since		2003-01-13
 */
class MappingManager extends Object
{
	/**
	 *	@var	array
	 */
	var $_aaMap = array(
		 _ACTION_FORMS		=> array()
		,_ACTION_MAPPINGS	=> array()
		);

	/**
	 *	@var	array
	 */
	var $_aaOptions = array();

	/**
	 *	constructor
	 *
	 *	@return void
	 */
	function MappingManager()
	{
		trigger_error("MappingManager is a virtual class, please extend for your application");
		return false;
	}

	/**
	 *	add a form to the mapping
	 *
	 *	"protected" function to be used by the constructor function of a derived class
	 *	@return boolean sucess
	 */
	function _AddForm($psIdent, $psType)
	{
		if (!is_string($psIdent) || 0 == strlen($psIdent)) {
			trigger_error("invalid form identifier '$psIdent'");
			return false;
		}
		if (!is_string($psType) || 0 == strlen($psType)) {
			trigger_error("invalid form type '$psType'");
			return false;
		}

		$a_new_form = array(_TYPE => $psType);
		$this->_aaMap[_ACTION_FORMS][$psIdent] = $a_new_form;
	}

	/**
	 *	add a map to the mapping
	 *
	 *	"protected" function to be used by the constructor function of a derived class
	 *	@return	boolean sucess
	 */
	function _AddMapping($psIdent, $psType, $psInput, $psForm='_DEFAULT_', $piValidate=0)
	{
		if (!is_string($psIdent) || 0 == strlen($psIdent)) {
			trigger_error("invalid mapping identifier '$psIdent'");
			return false;
		}
		if (!is_string($psType) || 0 == strlen($psType)) {
			trigger_error("invalid mapping type '$psType'");
			return false;
		}
		if ('_DEFAULT_' == $psForm) {
			$psForm = $psIdent;
			if (!array_key_exists($psForm, $this->_aaMap[_ACTION_FORMS])) {
				trigger_error("no form mapping to '$psForm' has been established");
				return false;
			}
		}
		if (1 != $piValidate) {
			$piValidate = 0;
		}

		$a_new_map = array(
			 _TYPE				=> $psType
			,_NAME				=> $psForm
			,_INPUT				=> $psInput
			,_VALIDATE			=> $piValidate
			,_ACTION_FORWARDS	=> array()
			);

		$this->_aaMap[_ACTION_MAPPINGS][$psIdent] = $a_new_map;
		return true;
	}

	/**
	 *	add a forward to an existing mapping
	 *
	 *	"protected" function to be used by the constructor function of a derived class
	 *	@return	boolean sucess
	 */
	function _AddForward($psMapIdent, $psFwdIdent, $psPath='_DEFAULT_', $piRedir=0)
	{
		if (!array_key_exists($psMapIdent, $this->_aaMap[_ACTION_MAPPINGS])) {
			trigger_error("invalid mapping identifier '$psMapIdent'");
			return false;
		}
		if (!is_string($psFwdIdent) || 0 == strlen($psFwdIdent)) {
			trigger_error("invalid mapping forward identifier '$psFwdIdent'");
			return false;
		}
		if (!is_string($psPath) || 0 == strlen($psPath)) {
			trigger_error("invalid mapping forward path '$psPath'");
			return false;
		} elseif ('_DEFAULT_' == $psPath) {
			$psPath = $this->_aaMap[_ACTION_MAPPINGS][$psMapIdent][_INPUT];
		}
		if (1 != $piRedir) {
			$piRedir = 0;
		}

		$a_new_fwd = array (
			 _PATH		=> $psPath
			,_REDIRECT	=> $piRedir
			);

		$this->_aaMap[_ACTION_MAPPINGS][$psMapIdent][_ACTION_FORWARDS][$psFwdIdent] =
			$a_new_fwd;
		return true;	
	}

	/**
	 *
	 *	@return void
	 */
	function _SetOptions(		$psErrorHandler='handle_error'
					,$piCache=0
					,$piErrorReporting=-1
					)
	{
		if (!1 == $piCache) {
			$piCache = 0;
		}
		if (-1 == $piErrorReporting) {
			$piErrorReporting = E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE;
		}

		$this->_aaOptions = array(
				 _CACHE 			=> $piCache
				,_ERROR_REPORTING	=> $piErrorReporting
				,_ERROR_HANDLER		=> $psErrorHandler
			);
	}

	/**
	 *	retrieve mappings
	 *
	 *	@return	array
	 */
	function GetMappings()
	{
		return $this->_aaMap;
	}

	/**
	 *	retrieve options
	 *
	 *	@return	array
	 */
	function GetOptions()
	{
		return $this->_aaOptions;
	}
}

?>
