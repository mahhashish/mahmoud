$InputValue = $_GET['Value'];

$OutputValue = DoSomeStuff($InputValue);

//Send our new value back to delphi
$_GET['Value'] = $OutputValue;
