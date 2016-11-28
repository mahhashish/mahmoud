<html>
<head>
	<title>Login Form</title>
</head>
<body>
<form name="login" method="POST" action="{{URL::to_route('login')}}">
<input type="text" name="email" />
<input type="password" name="password" />
<input type="submit" name="submit" />
</form>
</body>
</html>