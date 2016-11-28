@extends('template_masterpage')

@section('content')
	<h1 id="replyh">Sign Up</h1>
	<p class="bluey">Please fill all the credentials correctly to register to our site</p>
	{{Form::open(array('route'=>'signup_form_post'))}}
		<p class="minihead">First Name:</p>
		{{Form::text('first_name',Input::get('first_name'),array('class'=>'fullinput'))}}
		<p class="minihead">Last Name:</p>
		{{Form::text('last_name',Input::get('last_name'),array('class'=>'fullinput'))}}
		<p class="minihead">E-mail address:</p>
		{{Form::email('email',Input::get('email'),array('class'=>'fullinput'))}}
		<p class="minihead">Password:</p>
		{{Form::password('password',array('class'=>'fullinput'))}}
		<p class="minihead">Re-password:</p>
		{{Form::password('re_password',array('class'=>'fullinput'))}}
		<p class="minihead">Your personal info will not be shared with any 3rd party companies.</p>
		{{Form::submit('Register now!')}}
	{{Form::close()}}
@stop