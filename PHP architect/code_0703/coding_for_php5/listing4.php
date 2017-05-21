class word_list
{
	var $word_list;
}
class word_count
{
	var $word_list;
	function word_count()
	{
		$this->word_list = new word_list();
	}
