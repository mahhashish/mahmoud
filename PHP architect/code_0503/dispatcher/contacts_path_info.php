<?php

require_once('DB.php'); // This is PEAR's DB
require_once('dispatcher_path_info.php');

class contacts extends dispatcher {

    var $dbh;

    function contacts(){
        $this->dispatcher();

        $dsn       = 'mysql://jhorner:@localhost/phpa';
        $this->dbh = DB::connect($dsn, true);

        if (DB::isError($this->dbh)){
            die($this->dbh->getMessage());
        }

		$this->dbh->setFetchMode(DB_FETCHMODE_ASSOC);

        $new_contact_url = $this->url('new_contact');

        include('header.php');
    }

    function main(){

        $contacts = $this->dbh->getAll(
                        "select * from contacts order by name"
                    );
        if (count($contacts)){

            include('contact_list_head.php');

            $cgi_vars = array();
            foreach ($contacts as $contact){

                $cgi_vars['cid'] = $contact['cid'];
                $edit_url        = $this->url('edit_contact',$cgi_vars);
                $delete_url      = $this->url('delete_contact',$cgi_vars);

                include('contact_list_entry.php');
            }
            include('contact_list_foot.php');
        } else {
            include('list_empty.php');
        } 
    }

    function new_contact(){

        $post_url  = $this->url('create_contact');
        $submit_value = 'Add';
        $contact      = array (
                            'cid' => '',
                            'name' => '',
                            'email' => ''
                        );
        include('contact_form.php');
    }

    function create_contact(){

        if ($_POST['submit'] != 'Cancel'){
                
            $name  = $_POST['name'];
            $email = $_POST['email'];

            $this->dbh->query(
                "insert into contacts (name,email) values ('$name','$email')"
            );
        }

        $this->main();
    }

    function edit_contact(){
        $cid          = $_GET['cid'];

        $post_url  = $this->url('update_contact');
        $submit_value = 'Update';
        $contact      = $this->dbh->getRow(
                            "select * from contacts where cid=$cid"
                        );
        include('contact_form.php');
    }

    function update_contact(){

        if ($_POST['submit'] != 'Cancel'){

            $cid   = $_POST['cid'];
            $name  = $_POST['name'];
            $email = $_POST['email'];

            $this->dbh->query(
                "update contacts set name='$name',email='$email' where cid=$cid"
            );
        }
        $this->main();
    }

    function delete_contact(){
        $cid = $_GET['cid'];
        $this->dbh->query("delete from contacts where cid=$cid");
        $this->main();
    }
}

$obj = new contacts();
$obj->dispatch();

include('footer.php');

?>
