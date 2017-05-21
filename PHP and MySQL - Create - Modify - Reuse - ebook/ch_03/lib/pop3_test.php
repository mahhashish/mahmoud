<?php

include ('POP3Client.php');
$p = new POP3Client();
echo '+object' . '<br>';
// connect to mail.example.com POP3 server
if ($p->connect('pop.gmail.com', 995)) {
    echo $p->response . '+connect' . '<br>';
#die();
} else {
    echo '-connect' . '<br>';
    exit();
}
echo '1';
// login using the desired account credentials
if ($p->user('gmahhashish') || !$p->password('GO$flower987654')) {
    echo $p->response . '+login' . '<br>';
#$p->quit();
#die();
} else {
    echo '-login' . '<br>';
}
echo '2' . '<br>';
// test the connection
$p->noop();
echo $p->response . '<br>' . '<br>';

echo '3' . '<br>';
if ($p->_stat()) {
    echo $p->response . '<br>' . '<br>';
#die();
} else {
echo '-_stat' . '<br>';   
}
echo '4' . '<br>';

if ($p->_list()) {
    echo $p->response . '<br>' . '<br>';
#die();
} else {
echo '-_list' . '<br>';   
}
echo '5' . '<br>';
$p->quit();
?>
