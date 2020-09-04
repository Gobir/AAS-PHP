<?php
/*
 * ---------------------------------------------------------------
 * header.php
 * ---------------------------------------------------------------
 * Displays a header at the top of a page.
 * HTML source at: https://github.com/BlackrockDigital/startbootstrap-sb-admin
 */

/* The MIT License (MIT)
 * 
 * Copyright (C) 2013-2019 Blackrock Digital LLC
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy* 
 * of this software and associated documentation files (the "Software"), to deal* 
 * in the Software without restriction, including without limitation the rights* 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell* 
 * copies of the Software, and to permit persons to whom the Software is* 
 * furnished to do so, subject to the following conditions:* 
 * 
 * The above copyright notice and this permission notice shall be included in* 
 * all copies or substantial portions of the Software.* 
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR* 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,* 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE* 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER* 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,* 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN* 
 * THE SOFTWARE.
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="apple-touch-icon" sizes="57x57" href="<?= getWebRootUrl(); ?>images/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?= getWebRootUrl(); ?>images/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?= getWebRootUrl(); ?>images/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?= getWebRootUrl(); ?>images/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= getWebRootUrl(); ?>images/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?= getWebRootUrl(); ?>images/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= getWebRootUrl(); ?>images/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?= getWebRootUrl(); ?>images/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= getWebRootUrl(); ?>images/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?= getWebRootUrl(); ?>images/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= getWebRootUrl(); ?>images/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?= getWebRootUrl(); ?>images/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= getWebRootUrl(); ?>images/favicon-16x16.png">
        <link rel="manifest" href="<?= getWebRootUrl(); ?>images/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?= getWebRootUrl(); ?>images/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <title><?= $title; ?></title>
        <?php
        //Loops through all CSS and JS names in the $css & $js arrays and add them to the HTML DOM.
        for ($i = 0; $i < sizeof($css); $i++) {
            if ($css[$i] === "mapquest") {
                ?>
                <link href="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.css" rel="stylesheet" type="text/css">
                <?php
            } else {
                ?>
                <link href="<?= getWebRootUrl(); ?>css/<?= $css[$i]; ?>.css" rel="stylesheet" type="text/css">
                <?php
            }
        }
        for ($i = 0; $i < sizeof($js); $i++) {
            if ($js[$i] === "mapquest") {
                ?>
                <script src="https://api.mqcdn.com/sdk/mapquest-js/v1.3.2/mapquest.js"></script>
                <?php
            } else {
                ?>
                <script src="<?= getWebRootUrl(); ?>js/<?= $js[$i]; ?>.js"></script>
                <?php
            }
        }
        ?>
    </head>
