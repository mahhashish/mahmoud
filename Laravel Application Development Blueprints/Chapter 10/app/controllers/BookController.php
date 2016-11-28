<?php
class BookController extends BaseController {

	public function getIndex()
	{
		$books = Book::all();

		return View::make('book_list')->with('books',$books);
	}

}