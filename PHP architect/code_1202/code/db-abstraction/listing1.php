
<?
switch($alias) {

    /*   Query: get_age  */

    case 'get_age':

        $query['sql']   = "SELECT (YEAR(NOW()) - YEAR('$date')) as age";
        break;


    /*   Query: another query  */

    case 'get_age':

        $query['sql']   = "SELECT field FROM table";
        break;


   // and so on.... For every database a parallel file.

}
?>