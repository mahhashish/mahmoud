<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Coding Cage : Sign In</title>
        <link href="<?php echo base_url('views/assets/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" media="screen">
            <!--<link href="<?php echo base_url('views/assets/bootstrap/css/bootstrap-theme.min.css'); ?>" rel="stylesheet" media="screen"-->
                </head>
                <body>

                    <div class="container">
                        <?php
                        $attributes = array('id' => 'myform');
                        echo form_open('app', $attributes);
                        ?>

                        <h2>Sign In</h2><hr />

                        <div class="form-group">
                            <?php
                            echo form_error('username');
                            $data = array(
                                'class' => 'form-control',
                                'name' => 'username',
                                'placeholder' => 'Enter Username',
                                'value' => set_value('username')
                            );
                            echo form_input($data);
                            ?>

                        </div>
                        <div class="form-group">
                            <?php
                            echo form_error('password');
                            $data = array(
                                'class' => 'form-control',
                                'name' => 'password',
                                'placeholder' => 'Enter Password',
                                'value' => set_value('password')
                            );
                            echo form_password($data);
                            ?>
                        </div>
                        <div class="clearfix"></div><hr />
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" name="submit" value="Login">SIGN IN
                            </button>
                        </div>
                        <br /><?php
                        echo form_close();
                        ?>
                    </div>
                    </div>

                    </div>

                </body>
                </html>