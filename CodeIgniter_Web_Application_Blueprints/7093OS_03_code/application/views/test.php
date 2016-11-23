<?php

#$test=NULL;
#$_SESSION['foo'] = bar;
#var_dump($_SESSION);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */?>
        <div class="alert alert-danger">
            <?php echo 'OK';?>
           <?php echo $this->session->flashdata('flag_err'); ?> 
        </div>