<?php
/**
* @package Mambo
* @subpackage Languages
* @copyright  Refer to copyright.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Mambo Foundation Inc see README.php
*/

class catalogsView extends aliroView {

    public function render($renderer)
    {
        $lang = $renderer->getvars('lang');
        $language = new aliroLanguageBasic($lang, null, true);
        $col = $this->session('col') ? $this->session('col') : 'domain';
        $asc = $this->session('asc') ? $this->session('asc') : 1;
        $order = array();
        $files = $language->files;
		$catalogs = array();
		// search
        $search = $renderer->getvars('search');
		if ($search) {
			foreach ($files as $file) {
				if ('po' !== $file['filetype']) continue;
				$content = file_get_contents($this->class_base.'/language/'.$lang.'/'.$file['filename']);
				if(false !== strpos($content, $search)) $results[] = $file;
			}
			if (isset($results)) $files = $results;
		}
		// end search
        foreach ($files as $key => $row) if ($row['filetype'] == 'po') $catalogs[]  = $row;
        // Obtain a list of columns
        foreach ($catalogs as $key => $row) $order[$key]  = $row[$col];
        array_multisort($order, $asc == 1 ? SORT_ASC : SORT_DESC, $catalogs);
        $renderer->addvar('col', $col);
        $renderer->addvar('asc', $asc);
        $renderer->addvar('rows', $catalogs);
        $renderer->addvar('header', sprintf(T_('Manage Translations: %s'), $lang));
        $renderer->addvar('content', $renderer->fetch('catalogs.tpl.php'));
        $renderer->display('form.tpl.php');
    }

	private function session ($key) {
		return isset($_SESSION['cor_languages_session'][$key]) ? $_SESSION['cor_languages_session'][$key] : null;
	}

}