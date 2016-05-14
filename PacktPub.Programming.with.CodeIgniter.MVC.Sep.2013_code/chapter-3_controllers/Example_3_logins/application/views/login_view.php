<!DOCTYPE html">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<html>
    <head>
        <script src="http://code.jquery.com/jquery-latest.js" type='text/javascript'></script>
    </head>
    <body>
        <H1>Login Here</H1>
        <?php
        $submit_attributes = array('onsubmit' => "return check_if_valid();", 'id' => 'auth');
        // We will use the 'form' heper :
        // 'auth' will be called on submission if check_if_valid will return true!
        // the attributes are he form action attributes 				  
        echo form_open('auth', $submit_attributes);

        echo "<table><tr><td>";
        // The attributes of the <input tag>
        echo form_label("User Name");
        echo "</td><td>";
        echo form_input(array('name' => 'user_name', 'value' => ''));
        echo "</td><td>";
        echo "<label id='user_name_err' style='color:red; display:none'> name is too short </label>";
        echo "</td></tr>";

        echo '<tr><td>';
        echo form_label("Password");
        echo "</td><td>";
        echo form_password("password", "");
        echo "</td><td>";
        echo "<label id='password_err' style='color:red; display:none'> password is too short </label>";
        echo "</td></tr>";
        echo "</table>";

        echo form_input(array('type' => 'submit',
            'value' => 'Login'));
        echo form_close();
        ?>

        <HR></HR> 
        <p style="color:red"><?php echo $msg; ?></p>

    </body>
    <!-- Local JavaScript service -->
    <script type='text/javascript'>

        function check_if_valid() {
            var submit = true;
            var user_name = $('[name="user_name"]').val();
            var password = $('[name="password"]').val();

            if (user_name.length < 2) {
                $('#user_name_err').show();
                submit = false;
            } else
                $('#user_name_err').hide();

            if (password.length < 6) {
                $('#password_err').show();
                submit = false;
            } else
                $('#password_err').hide();

            return submit;
        }

    </script> 

</html>