<?php

class listErr404HTML extends basicAdminHTML {

	// Required because gettext does not find T_('abc') inside heredoc
	public function __construct ($controller) {
		parent::__construct($controller);
		$this->translations['The page 404 error log is empty'] = T_('The page 404 error log is empty');
		$this->translations['404 Log Review'] = T_('404 Log Review');
		$this->translations['Timestamp'] = T_('Timestamp');
		$this->translations['URI'] = T_('URI');
		$this->translations['Referer'] = T_('Referer');
		$this->translations['POST data'] = T_('POST data');
		$this->translations['Trace'] = T_('Trace');
	}
	
	public function showErrors ($errors) {
   		$k = $i = 0;
		$htmlset = '';
		$rowcount = count($errors);
		if ($rowcount) foreach ($errors as $error) {
			$htmlset .= <<<SET_HTML

			<tr class="row$k">
				<td>
				{$this->html('idbox', $i, $error->eluri)}
				</td>
				<td>
					<a href="$error->details">$error->timestamp</a>
				</td>
				<td>
					$error->eluri
				</td>
			</tr>
SET_HTML;

			$i++;
			$k = 1 - $k;
		}
		else $htmlset = <<<NO_ERRORS

		<tr><td colspan="3" align="center">{$this->T_('The page 404 error log is empty')}</td></tr>

NO_ERRORS;

		echo <<<END_OF_HTML

		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
				{$this->T_('404 Log Review')}
			</th>
		</tr>
		</thead>
		<tbody><tr><td></td></tr></tbody>
		</table>

		<table class="adminlist">
		<thead>
		<tr>
			<th width="3%" class="title">
			<input type="checkbox" name="toggle" value="" onclick="checkAll($rowcount);" />
			</th>
			<th class="title">
				{$this->T_('Timestamp')}
			</th>
			<th class="title">
				{$this->T_('URI')}
			</th>
		</tr>
		</thead>
		<tbody>
        $htmlset
        </tbody>
	</table>
	{$this->pageNav->getListFooter()}
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	$this->optionline

END_OF_HTML;

	}

	public function showDetailedError ($error) {
	    echo <<<DETAIL_HTML
		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
				{$this->T_('404 Log Review')}
			</th>
		</tr>
		</thead>
		<tbody><tr><td></td></tr></tbody>
		</table>
		<div style="padding-left: 40px">
	    <div>
	    <h3>{$this->T_('URI')}</h3>
	    <textarea readonly="readonly" rows="3" cols="85">$error->uri</textarea>
	    </div>
	    <div>
	    <h3>{$this->T_('Timestamp')}</h3>
	    <input type="text" readonly="readonly" value="$error->timestamp" />
	    </div>
	    <div>
	    <h3>{$this->T_('Referer')}</h3>
	    <textarea readonly="readonly" rows="3" cols="85">$error->showreferer</textarea>
	    </div>
	    <div>
	    <h3>{$this->T_('POST data')}</h3>
	    <textarea readonly="readonly" rows="6" cols="85">$error->showpost</textarea>
	    </div>
	    <div>
	    <h3>{$this->T_('Trace')}</h3>
	    $error->trace
	    </div>
	    </div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	$this->optionline
	
DETAIL_HTML;

	}

}