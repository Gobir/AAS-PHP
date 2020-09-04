<?php
session_start();
require 'functions.php';
session_destroy();
//Redirects to webroot URL
redirectToWebrootUrl("login.php");

