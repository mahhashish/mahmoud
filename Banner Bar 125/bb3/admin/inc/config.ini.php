<?php

/**
 * Database configuration
 */
$server           = 'localhost';
$user             = '';
$pass             = '';
$db               = '';

/**
 * jQuery version to use
 */
$jQueryjs         = "//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js";

/**
 * Define Banner Bar login username, SHA-1 password hash and salt
 */
$login_name       = "Maxwell";
$login_hash       = "5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8"; //<-- This SHA-1 hash is "password" for testing purposes
$salt             = substr(md5(date("F")), 8);

/**
 * Paypal sandbox enable/disable
 * sandbox on = 1
 * sandbox off = 0
 */
$sandbox          = 1;

/**
 * Define website (no trailing slash) e.g. http://ianjgough.com
 * Define relative path to banner bar folder (no trailing slash) e.g. bb3 (Everything after $website and before Admin like bb3 not bb3/admin).
 */
$website          = "http://ianjgough.com";
$location         = "bb3";

/**
 * Define table names for database
 */
$t_banners        = "banners";
$t_client_banners = "client_banners";
$t_settings       = "settings";
$t_paypal         = "paypal";
$t_payments       = "payments";
?>