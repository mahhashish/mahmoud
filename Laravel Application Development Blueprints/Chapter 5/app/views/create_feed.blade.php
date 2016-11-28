<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Save a new ATOM Feed to Database</title>
</head>
<body>
	<h1>Save a new ATOM Feed to Database</h1>

	@if(Session::has('message'))
		<h2>{{Session::get('message')}}</h2>
	@endif

    {{Form::open(array('url' => 'feeds/create', 'method' => 'post'))}}

    <h3>Feed Category</h3>
    {{Form::select('category',array('News'=>'News','Sports'=>'Sports','Technology'=>'Technology'),Input::old('category'))}}

    <h3>Title</h3>
    {{Form::text('title',Input::old('title'))}}

    <h3>Feed URL</h3>
    {{Form::text('feed',Input::old('feed'))}}

    <h3>Show on Site?</h3>
    {{Form::select('active',array('1'=>'Yes','2'=>'No'),Input::old('active'))}}

    {{Form::submit('Save!',array('style'=>'margin:20px 100% 0 0'))}}


    {{Form::close()}}
</body>
</html>
