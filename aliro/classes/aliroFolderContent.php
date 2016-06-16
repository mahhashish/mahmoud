<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

abstract class aliroFolderContent extends aliroDatabaseRow {
	protected $folderfield = 'folderid';

	function getBasicFolder () {
		$folderfield = $this->folderfield;
		return aliroFolderHandler::getInstance()->getBasicFolder($this->$folderfield);
	}
	
	function getFolder () {
		$folderfield = $this->folderfield;
		return aliroFolderHandler::getInstance()->getFolder($this->$folderfield);
	}

	function getCategoryName () {
    	$parent = $this->getBasicFolder();
    	return is_object($parent) ? $parent->getCategoryName(true) : '';
    }

    function getFamilyNames () {
    	$parent = $this->getBasicFolder();
    	return $parent->getFamilyNames(true);
    }

	???
	function &getEditSelectList ($type, $parm, &$user) {
		$repository =& remositoryRepository::getInstance();
		$clist = $repository->getSelectList(false, $this->containerid, $type, $parm, $user);
	return $clist;
	}

	???
	function isFieldHTML ($field) {
		return in_array($field, array('description', 'smalldesc', 'license'));
	}

	???
	function togglePublished (&$idlist, $value) {
		$cids = implode( ',', $idlist );
		$sql = "UPDATE #__downloads_files SET published=$value". "\nWHERE id IN ($cids)";
		remositoryRepository::doSQL($sql);
	}

	???
	function resetDownloadCounts () {
		remositoryRepository::doSQL('UPDATE #__downloads_files SET downloads=0');
	}

	function getFilesSQL ($published, $count=false, $containerid=0, $descendants=false, $orderby=2, $search='', $limitstart=0, $limit=0, $submitter=0) {
		$sorter = array ('', ' ORDER BY id', ' ORDER BY filetitle', ' ORDER BY downloads DESC', ' ORDER BY submitdate DESC', ' ORDER BY u.username');
		if (!isset($sorter[$orderby]) OR $orderby == 0) $orderby = 2;
		if ($count) $results = 'count(f.id)';
		else $results = 'f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count';
		if ($submitter) $results .= ', u.username';
		if ($descendants AND $containerid) {
			$sql = "SELECT $results FROM #__downloads_structure AS s INNER JOIN #__downloads_files AS f ON f.containerid=s.item";
			$where[] = "s.container = $containerid";
		}
		else {
			$sql = "SELECT $results FROM #__downloads_files AS f ";
			if ($containerid) $where[] = "f.containerid = $this->id";
			else $where[] = "f.metatype = 0";
		}
		if ($submitter) $where[] = "f.submittedby = $submitter";
		if (!$count) $sql .= ' LEFT JOIN #__downloads_log AS l ON l.type=3 AND l.fileid=f.id';
		if ($submitter OR (5 == $orderby)) $sql .= ' LEFT JOIN #__users AS u ON u.id=f.submittedby';
		if ($published) $where[] = 'f.published=1';
		$interface =& remositoryInterface::getInstance();
		if ($search) {
			$search = $interface->getEscaped($search);
			$where[] = "LOWER(f.filetitle) LIKE '%$search%'";
		}
		if (isset($where)) $sql .= ' WHERE '.implode(' AND ',$where);
		$repository =& remositoryRepository::getInstance();
		$user = $interface->getUser();
		$sql .= remositoryAbstract::visibilitySQL ($user, $repository->See_Files_no_download);
		if (!$count) {
			$sql .= ' GROUP BY f.id';
			$sql .= $sorter[$orderby];
		}
		if ($limit) $sql .= " LIMIT $limitstart,$limit";
		return $sql;
	}

	function popularLoggedFiles ($category, $max, $days, $user) {
		$interface =& remositoryInterface::getInstance();
		$database =& $interface->getDB();
		$sql = 'SELECT f.id, f.filetitle, f.autoshort, f.description, f.smalldesc, f.filedate, f.icon, f.containerid, c.name, COUNT( l.fileid ) AS downloads FROM #__downloads_log AS l, #__downloads_files AS f, #__downloads_containers AS c';
		if ($category) $sql .= ', #__downloads_structure AS s';
		$sql .= ' WHERE c.id = f.containerid AND f.published=1 AND l.type=1 AND l.fileid=f.id';
		$repository =& remositoryRepository::getInstance();
		$sql .= remositoryAbstract::visibilitySQL ($user, $repository->See_Files_no_download);
		if ($category) $sql .= " AND f.containerid=s.item AND s.container=$category";
		$sql .= " AND DATE_SUB(CURDATE(),INTERVAL $days DAY ) <= l.date";
		$sql .= " GROUP BY l.fileid ORDER BY downloads DESC LIMIT $max";
		$database->setQuery($sql);
		$files = $database->loadObjectList();
		if ($files) return $files;
		else return array();
	}

	function popularDownloadedFiles ($category, $max, $user) {
		$interface =& remositoryInterface::getInstance();
		$database =& $interface->getDB();
		$sql = 'SELECT f.id, f.downloads, f.filetitle, f.autoshort, f.description, f.smalldesc, f.filedate, f.icon, f.containerid, c.name from #__downloads_files AS f, #__downloads_containers AS c';
		if ($category) $sql .= ', #__downloads_structure AS s';
		$sql .= ' WHERE f.containerid = c.id AND f.published=1';
		$repository =& remositoryRepository::getInstance();
		$sql .= remositoryAbstract::visibilitySQL ($user, $repository->See_Files_no_download);
		if ($category) $sql .= " AND f.containerid=s.item AND s.container=$category";
		$sql .= " ORDER BY downloads DESC LIMIT $max";
		$database->setQuery($sql);
		$files = $database->loadObjectList();
		if ($files) return $files;
		else return array();
	}

	function newestFiles ($category, $max, $user) {
		$interface =& remositoryInterface::getInstance();
		$database =& $interface->getDB();
		$sql = 'SELECT f.id, f.filetitle, f.autoshort, f.description, f.smalldesc, f.filedate, f.icon, f.containerid, c.name from #__downloads_files AS f, #__downloads_containers AS c';
		if ($category) $sql .= ', #__downloads_structure AS s';
		$sql .= ' WHERE f.containerid = c.id AND f.published=1';
		$repository =& remositoryRepository::getInstance();
		$sql .= remositoryAbstract::visibilitySQL ($user, $repository->See_Files_no_download);
		if ($category) $sql .= " AND f.containerid=s.item AND s.container=$category";
		$sql .= " ORDER BY f.filedate DESC LIMIT $max";
		$database->setQuery($sql);
		$files = $database->loadObjectList();
		if ($files) return $files;
		else return array();
	}

	function getCountInContainer ($id, $published, $search='') {
		$interface =& remositoryInterface::getInstance();
		$database =& $interface->getDB();
		$sql = "SELECT COUNT(id) FROM #__downloads_files WHERE containerid = $id";
		if ($published) $sql .= ' AND published=1';
		if ($search) $sql .= " AND LOWER(filetitle) LIKE '%$search%'";
		$database->setQuery($sql);
		return $database->loadResult();
	}

	function searchFilesSQL($search_text, $seek_fields, &$user, $countOnly, $limitstart=0, $limit=0) {
		$results = $countOnly ? 'COUNT(id)' : 'id,containerid,filetitle,description,icon,filesize,downloads';
		$sql="SELECT $results FROM #__downloads_files AS f WHERE metatype = 0";
		foreach ($seek_fields as $field) $orcondition[] = "$field LIKE '%$search_text%'";
		if (isset($orcondition)) $sql .= ' AND ('.implode(' OR ', $orcondition).') ';
		else {
			echo '<br/>&nbsp;<br/>'._DOWN_SEARCH_ERR;
			exit;
		}
		$repository =& remositoryRepository::getInstance();
		$sql .= remositoryAbstract::visibilitySQL ($user, $repository->See_Files_no_download);
		$sql .= ' ORDER BY filetitle';
		if ($limit AND !$countOnly) $sql .= " LIMIT $limitstart,$limit";
		return $sql;
	}

}

?>