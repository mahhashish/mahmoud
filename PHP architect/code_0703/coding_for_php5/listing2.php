foreach($word_list as $entry_key => $list_entry)
{
	if($word_list[$entry_key]->get_word() == $this->get_word())
	{
		$word_list[$entry_key]->increment();
		$added = true;
	}
}
