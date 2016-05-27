<?php

class listErrorsHTML extends basicAdminHTML {

	// Required because gettext does not find T_('abc') inside heredoc
	public function __construct ($controller) {
		parent::__construct($controller);
		$this->translations['The error log is empty'] = T_('The error log is empty');
	}

	public function showErrors ($errors, $selection=array()) {
		$htmlset = '';
		$heading = T_('Error Log Review');
		$timestamp = T_('Timestamp');
		$ip = T_('IP Address');
		$smessage = T_('Short Message');
        $rowcount = count($errors);
		$pagenavtext = $this->pageNav->getListFooter();
   		$k = $i = 0;
		foreach ($errors as $error) {
			$idbox = $this->html('idBox', $i, $error->id, false, 'cid', in_array($error->id, $selection));
			$link = $this->getCfg('admin_site').'/index.php?core=cor_errors&task=edit&id='.$error->id;
			$htmlset .= <<<SET_HTML

			<tr class="row$k">
				<td>
				$idbox
				</td>
				<td>
					<a href="$link">$error->timestamp</a>
				</td>
				<td>
					$error->ip
				</td>
				<td>
					$error->smessage
				</td>
			</tr>
SET_HTML;

			$i++;
			$k = 1 - $k;
		}
		if (!$htmlset) $htmlset = <<<NO_ERRORS

		<tr><td colspan="3" align="center">{$this->T_('The error log is empty')}</td></tr>

NO_ERRORS;

		echo <<<END_OF_HTML

		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
			$heading
			</th>
		</tr>
		</thead>
		<tbody><tr><td></td></tr></tbody>
		</table>

		<table class="adminlist">
		<thead>
		<tr>
			<th width="3%" class="title">
			<input type="checkbox" id="toggle" name="toggle" value="" />
			</th>
			<th class="title">
			$timestamp
			</th>
			<th class="title">
			$ip
			</th>
			<th class="title">
			$smessage
			</th>
		</tr>
		</thead>
		<tbody>
        $htmlset
        </tbody>
	</table>
	$pagenavtext
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
	$this->optionline

END_OF_HTML;

	}

	private function displayArray ($arr, $depth=0) {
		$result = '';
		foreach ($arr as $key=>$value) {
			if (is_array($value)) $result .= "[$key] = ".$this->displayArray($value, $depth+1);
			else {
				for ($i = 0; $i < $depth; $i++) $result .= "\t";
				$result .= "[$key] = $value\n";
			}
		}
		return $result;
	}

	public function showDetailedError ($error) {
		$heading = T_('Entry from Error Log');
	    $timestamp = T_('Timestamp');
	    $smessage = T_('Short Message');
	    $lmessage = T_('Long Message');
	    $uri = T_('URI');
	    $referer = T_('Referer');
	    $ip = T_('IP Address');
	    $post = T_('POST data');
	    $trace = T_('Trace');
	    $dbname = T_('Database Name');
	    $dberror = T_('Database Error');
	    $sql = T_('SQL');
	    $dbtrace = T_('Database call trace');
		$postdata = unserialize(base64_decode($error->post));

	    echo <<<DETAIL_HTML
		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
			$heading
			</th>
		</tr>
		</thead>
		</table>
		<div style="padding-left: 40px">
	    <div>
	    <h3>$timestamp</h3>
	    <input type="text" readonly="readonly" value="$error->timestamp" />
	    </div>
	    <div>
	    <h3>$smessage</h3>
	    <textarea readonly="readonly" rows="3" cols="85">$error->smessage</textarea>
	    </div>
	    <div>
	    <h3>$lmessage</h3>
	    <textarea readonly="readonly" rows="10" cols="85">$error->lmessage</textarea>
	    </div>
	    <div>
	    <h3>$referer</h3>
	    <textarea readonly="readonly" rows="3" cols="85">$error->referer</textarea>
	    </div>
	    <div>
	    <h3>$ip</h3>
	    <textarea readonly="readonly" rows="1" cols="85">$error->ip</textarea>
	    </div>
	    <div>
	    <h3>$uri</h3>
	    <textarea readonly="readonly" rows="3" cols="85">$error->get</textarea>
	    </div>
	    <div>
	    <h3>$post</h3>
	    <textarea readonly="readonly" rows="6" cols="85">{$this->displayArray($postdata)}</textarea>
	    </div>
	    <div>
	    <h3>$trace</h3>
	    $error->trace
	    </div>
	    <div>
	    <h3>$dbname</h3>
	    <input type="text" readonly="readonly" value="$error->dbname" />
	    </div>
	    <div>
	    <h3>$dberror</h3>
	    <textarea readonly="readonly" rows="3" cols="85">$error->dberror $error->dbmessage</textarea>
	    </div>
	    <div>
	    <h3>$sql</h3>
	    <textarea readonly="readonly" rows="3" cols="85">$error->sql</textarea>
	    </div>
	    <div>
	    <h3>$dbtrace</h3>
	    $error->dbtrace
	    </div>
	    </div>
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
	$this->optionline
DETAIL_HTML;
	}

}