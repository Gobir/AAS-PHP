<?php
/*
 * ---------------------------------------------------------------
 * sidebar.php
 * ---------------------------------------------------------------
 * Displays a sidebare on the left of a page.
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
<ul class="sidebar navbar-nav">
    <li class="nav-item <?= $active[0]; ?>">
        <?php if (isset($_SESSION["superuser"]) && $_SESSION["superuser"]) { ?>
            <a class="nav-link" href="<?= getWebRootUrl(); ?>super-user.php">
            <?php } else { ?>
                <a class="nav-link" href="<?= getWebRootUrl(); ?>settings.php">
                <?php } ?>
                <i class="icon octicon octicon-settings"></i>
                <span><?php
                    if (isset($_SESSION["superuser"]) && $_SESSION["superuser"]) {
                        echo "Super User ";
                    }
                    ?>Settings</span>
            </a>
    </li>
    <li class="nav-item dropdown <?= $active[1]; ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php if (isset($_SESSION["superuser"]) && $_SESSION["superuser"]) { ?>
                <i class="icon octicon octicon-organization"></i>
                <span>Admins</span>
            </a>
            <div class="dropdown-menu <?= $active[2]; ?>" aria-labelledby="pagesDropdown">
                <a class="dropdown-item <?= $active[3]; ?>" href="<?= getWebRootUrl(); ?>add-admin.php">Add Admin:</a>
                <a class="dropdown-item <?= $active[4]; ?>" href="<?= getWebRootUrl(); ?>admins.php">List Admins:</a>
            </div>
        <?php } else { ?>
            <i class="icon octicon octicon-organization"></i>
            <span>Users</span>
            </a>
            <div class="dropdown-menu <?= $active[2]; ?>" aria-labelledby="pagesDropdown">
                <a class="dropdown-item <?= $active[3]; ?>" href="<?= getWebRootUrl(); ?>add-user.php">Add User:</a>
                <a class="dropdown-item <?= $active[4]; ?>" href="<?= getWebRootUrl(); ?>users.php">List Users:</a>
            </div>
        <?php } ?>
    </li>
    <?php if (isset($_SESSION["superuser"]) && $_SESSION["superuser"]) { ?>
        <li class="nav-item <?= $active[5]; ?>">
            <a class="nav-link" href="<?= getWebRootUrl(); ?>payments.php">
                <i class="icon octicon octicon-credit-card"></i>
                <span>Payments</span></a>
        </li>
    <?php } ?>   
    <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"]) { ?>
        <li class="nav-item <?= $active[6]; ?>">
            <a class="nav-link" href="<?= getWebRootUrl(); ?>track-user.php">
                <i class="icon octicon octicon-location"></i>
                <span>Track User</span></a>
        </li>
        <li class="nav-item dropdown <?= $active[7]; ?>">
            <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon octicon octicon-organization"></i>
                <span>Reports</span>
            </a>
            <div class="dropdown-menu <?= $active[8]; ?>" aria-labelledby="pagesDropdown">
                <a class="dropdown-item <?= $active[9]; ?>" href="<?= getWebRootUrl(); ?>add-options.php">Add Options:</a>
                <a class="dropdown-item <?= $active[10]; ?>" href="<?= getWebRootUrl(); ?>daily-monthly-report.php">Daily Monthly Report:</a>
                <a class="dropdown-item <?= $active[11]; ?>" href="<?= getWebRootUrl(); ?>general-report.php">General Report:</a>
                <a class="dropdown-item <?= $active[12]; ?>" href="<?= getWebRootUrl(); ?>pay-calculation.php">Pay Calculation:</a>
            </div>
        </li>
        <li class="nav-item <?= $active[13]; ?>">
            <a class="nav-link" href="<?= getWebRootUrl(); ?>mobile-app-logs.php">
                <i class="icon octicon octicon-bug"></i>
                <span>Mobile App Logs</span></a>
        </li>
    <?php } ?>
</ul>