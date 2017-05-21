foreach($word_list as $entry_key => $list_entry)
{
	$list_entry = & $word_list[$entry_key];
	if($list_entry->get_word() == $this->get_word())
	{
		$list_entry->increment();
		$added = true;
	}
}
