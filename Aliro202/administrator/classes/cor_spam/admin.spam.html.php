<?php

class listSpamHTML extends basicAdminHTML {

	public function showSpamChecks ($checks) {
   		$k = $i = 0;
		$htmlset = '';
		$rowcount = count($checks);
		if ($rowcount) {
			$this->html('toggleManyScript', $rowcount);
			foreach ($checks as $check) {
				if (!trim($check->title)) $check->title = T_('No Title Given');
				$htmlset .= <<<SET_HTML

			<tr class="row$k">
				<td>
				{$this->html('idbox', $i, $check->id)}
				</td>
				<td>
					<a href="$check->details">$check->title</a>
				</td>
				<td>
					$check->articledate
				</td>
				<td>
					$check->checkers
				</td>
				<td>
					$check->spaminess
				</td>
				<td>
					$check->status
				</td>
				<td>
					$check->authorip
				</td>
				<td>
					$check->authorid
				</td>
				<td>
					<a href="mailto:$check->authoremail">$check->authoremail</a>
			</tr>
SET_HTML;

				$i++;
				$k = 1 - $k;
			}
		}
		else $htmlset = <<<NO_ERRORS

		<tr><td colspan="3" align="center">{$this->T_('The spam check log is empty')}</td></tr>

NO_ERRORS;

		echo <<<END_OF_HTML

		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
				{$this->T_('Spam Check Log Review')}
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
				{$this->T_('Title')}
			</th>
			<th class="title">
				{$this->T_('Date')}
			</th>
			<th class="title">
				{$this->T_('Checkers')}
			</th>
			<th class="title">
				{$this->T_('Spaminess')}
			</th>
			<th class="title">
				{$this->T_('Status')}
			</th>
			<th class="title">
				{$this->T_('IP Address')}
			</th>
			<th class="title">
				{$this->T_('Author ID')}
			</th>
			<th class="title">
				{$this->T_('Author Email')}
			</th>
		</tr>
		</thead>
		<tbody>
        $htmlset
        </tbody>
	</table>
	{$this->pageNav->getListFooter()}
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
	$this->optionline

END_OF_HTML;

	}

	public function showDetailedSpamLog ($check, $results) {
		if (!trim($check->title)) $check->title = T_('No Title Given');
		$check->trusted = $check->trusted ? T_('Yes') : T_('No');
	    echo <<<DETAIL_HTML
		<table class="adminheading">
		<thead>
		<tr>
			<th class="user">
				{$this->T_('Spam Check Log Review')}
			</th>
		</tr>
		</thead>
		<tbody><tr><td></td></tr></tbody>
		</table>
		<div style="padding-left: 40px">
	    <div>
	    <h3>{$this->T_('Title')}</h3>
	    <input type="text" readonly="readonly" value="$check->title" />
	    </div>
	    <div>
	    <h3>{$this->T_('Date')}</h3>
	    <input type="text" readonly="readonly" value="$check->articledate" />
	    </div>
	    <div>
	    <h3>{$this->T_('Author Name')}</h3>
	    <input type="text" readonly="readonly" value="$check->authorname" />
	    </div>
	    <div>
	    <h3>{$this->T_('Author Email')}</h3>
	    <input type="text" readonly="readonly" value="$check->authoremail" />
	    </div>
	    <div>
	    <h3>{$this->T_('IP address')}</h3>
	    <input type="text" readonly="readonly" value="$check->authorip" />
	    </div>
	    <div>
	    <h3>{$this->T_('Author OpenID')}</h3>
	    <input type="text" readonly="readonly" value="$check->openid" />
	    </div>
	    <div>
	    <h3>{$this->T_('Author ID')}</h3>
	    <input type="text" readonly="readonly" value="$check->authorid" />
	    </div>
	    <div>
	    <h3>{$this->T_('Trusted')}</h3>
	    <input type="text" readonly="readonly" value="$check->trusted" />
	    </div>
	    <div>
	    <h3>{$this->T_('Author URI')}</h3>
	    <textarea readonly="readonly" rows="6" cols="85">$check->authoruri</textarea>
	    </div>
	    <div>
	    <h3>{$this->T_('Article Body')}</h3>
	    <textarea readonly="readonly" rows="6" cols="85">$check->body</textarea>
	    </div>
	    <div>
	    <h3>{$this->T_('Permalink')}</h3>
	    <textarea readonly="readonly" rows="6" cols="85">$check->permalink</textarea>
	    </div>
		<div>
			{$this->showResults($results)}
		</div>
	    </div>
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" id="id" name="cid" value="$check->id" />
	<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
	$this->optionline
	
DETAIL_HTML;

	}

	private function showResults ($results) {
		$listhtml = '';
		foreach ($results as $result) $listhtml .= <<<ONE_RESULT

					<tr>
						<td>
							$result->checker
						</td>
						<td>
							$result->status
						</td>
						<td>
							$result->spaminess
						</td>
						<td>
							$result->identifier
						</td>
					</tr>

ONE_RESULT;

		return <<<ALL_RESULTS

				<table>
					<thead>
						<th>
							{$this->T_('Checker')}
						</th>
						<th>
							{$this->T_('Status')}
						</th>
						<th>
							{$this->T_('Spaminess')}
						</th>
						<th>
							{$this->T_('Identifier')}
						</th>
					</thead>
					<tbody>
						$listhtml
					</tbody>
				</table>

ALL_RESULTS;

	}

}