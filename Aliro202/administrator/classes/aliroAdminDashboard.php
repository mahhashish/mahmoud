<?php

/***************************************
/* Part of Aliro 
/* 
/* Copyright 2008 Aliro Software Limited
/*
/***************************************/

class aliroAdminDashboard {

	public static function showSQLdata () {
	
		$handler = aliroComponentHandler::getInstance();
		$handler->startBuffer();
		$database = aliroCoreDatabase::getInstance();
		$info1 = $database->doSQLget("SELECT SUBSTRING(TIMEDIFF(NOW(),stamp),1,2) AS hours_ago,"
		."\n COUNT(*) AS requests, AVG(elapsed) AS response,"
		."\n MAX(elapsed) AS worst_response, AVG(memory)/1000000 AS memused, MAX(memory) AS worst_memory"
		."\n FROM `#__query_stats` WHERE SUBDATE(NOW(), INTERVAL 48 HOUR) < stamp GROUP BY hours_ago ORDER BY hours_ago DESC");
		
		$info2 = $database->doSQLget("SELECT SUBSTRING(TIMEDIFF(NOW(),stamp),1,2) AS hours_ago,"
		."\n COUNT(*) AS requests, AVG(elapsed) AS response,"
		."\n SUM(elapsed) AS activetime, AVG(count) AS sqlops, AVG(total) AS avgsql,"
		."\n MAX(total) AS worst_sqltotal, AVG(memory)/1000000 AS memused, MAX(memory) AS worst_memory"
		."\n FROM `#__query_stats` WHERE SUBDATE(NOW(), INTERVAL 48 HOUR) < stamp GROUP BY hours_ago ORDER BY hours_ago");

		// This is temporary code to get some kind of display - the intention is to provide graphs etc.
		$infotemp = $database->doSQLget("SELECT COUNT(*) AS requests, AVG(count) AS sqlops, AVG(total) AS sqltime,"
			." AVG(elapsed) AS response, SUM(elapsed)/(48*36) AS loadfactor, AVG(memory)/1000000 AS memused"
			." FROM #__query_stats WHERE SUBDATE(NOW(), INTERVAL 48 HOUR) < stamp");

		$header = T_('Site performance over the last 48 hours');
		$label_rq = T_('Number of requests handled');
		$label_sql = T_('Average SQL operations per request');
		$label_sqlt = T_('Average time for SQL queries per request (seconds)');
		$label_sqlt1 = T_('Average time for one SQL query (seconds)');
		$label_lapse = T_('Average elapsed time per request (seconds)');
		$label_load = T_('Load factor (% of time active)');
		$label_mem = T_('Average memory used (megabytes)');

		$perquery = $infotemp[0]->sqltime / max(1,$infotemp[0]->sqlops);
		$futureplan = T_('In future, more detailed information will be provided graphically');

		echo <<<DASH_BOARD

				<h2>$header</h2>
				<table>
					<tr>
						<td>
							$label_rq
						</td>
						<td>
							{$infotemp[0]->requests}
						</td>
					</tr>
					<tr>
						<td>
							$label_sql
						</td>
						<td>
							{$infotemp[0]->sqlops}
						</td>
					</tr>
					<tr>
						<td>
							$label_sqlt
						</td>
						<td>
							{$infotemp[0]->sqltime}
						</td>
					</tr>
					<tr>
						<td>
							$label_sqlt1
						</td>
						<td>
							{$perquery}
						</td>
					</tr>
					<tr>
						<td>
							$label_lapse
						</td>
						<td>
							{$infotemp[0]->response}
						</td>
					</tr>
					<tr>
						<td>
							$label_load
						</td>
						<td>
							{$infotemp[0]->loadfactor}
						</td>
					</tr>
					<tr>
						<td>
							$label_mem
						</td>
						<td>
							{$infotemp[0]->memused}
						</td>
					</tr>
				</table>
				<p>
					$futureplan
				</p>
DASH_BOARD;

		$handler->endBuffer();
		return;
		
		$html1 = '';
		foreach ($info1 as $line1) $html1 .= <<<INFO_TYPE1
		
			<tr>
				<td>
					$line1->hours_ago
				</td>
				<td>
					$line1->requests
				</td>
				<td>
					$line1->response
				</td>
				<td>
					$line1->worst_response
				</td>
				<td>
					$line1->memused
				</td>
				<td>
					$line1->worst_memory
				</td>
			</tr>
		
INFO_TYPE1;
		
		$html2 = '';
		foreach ($info2 as $line2) $html2 .= <<<INFO_TYPE2
		
			<tr>
				<td>
					$line2->hours_ago
				</td>
				<td>
					$line2->requests
				</td>
				<td>
					$line2->response
				</td>
				<td>
					$line2->activetime
				</td>
				<td>
					$line2->sqlops
				</td>
				<td>
					$line2->avgsql
				</td>
				<td>
					$line2->worst_sqltotal
				</td>
				<td>
					$line2->memused
				</td>
				<td>
					$line2->worst_memory
				</td>
			</tr>
		
INFO_TYPE2;

		echo <<<SQL_STATS
		
		<table>
			$html1
		</table>
		<table>
			$html2
		</table>
		
SQL_STATS;

		$handler->endBuffer();
	}
	
}