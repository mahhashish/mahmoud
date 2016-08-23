<?

require_once('class_pet.eng.inc.php');

$template = new pet;
$template->read_file('catalogue.tpl.html');
$db = mysql_connect('localhost', 'ademmer', 'letmein');

$query = "SELECT authors.name as name,
                   books.name as book
              FROM authors,
                   books
             WHERE books.author_id = authors.id
          ORDER BY authors.name,
                   books.name";

mysql_select_db('my_db', $db);
$results = mysql_query($query, $db);

$last_author = '';
$item = array();
$num_authors = 0;
$num_books = 0;

while($author = mysql_fetch_assoc($results)){
	if($author['name'] != $last_author){
		if(sizeof($item) != 0){
			$items[] = $item;
			$item = array();
		}
		$num_authors++;
		$last_author = $author['name'];
		$item['name'] = $author['name'];
	}
	$item['books'][]['title'] = $author['book'];
	$num_books++;
}

$items[] = $item;

$template->add_content($items, 'authors');
$template->add_content($num_authors, 'num_authors');
$template->add_content($num_books, 'num_books');

$template->parse();
$template->output();

?>