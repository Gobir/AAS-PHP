<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * add-user.php
 * ---------------------------------------------------------------
 * Adds a user.
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

//Page title.
$title = "Admin - Add Options for User";
//Email displayed in navigation section.
$email = $_SESSION['admin_email'];
//Names of CSS files to load.
$css = [
    "admin",
    "octicons",
    "bootstrap-datepicker",
    "bootstrap-datetimepicker-standalone",
    "bootstrap-datetimepicker"
];
//Names of JS files to load.
$js = [
    "jquery",
    "bootstrap.bundle",
    "jquery.easing",
    "admin",
    "bootstrap-datepicker",
    "moment.min",
    "bootstrap-datetimepicker.min",
    "decimalonly"
];
//Part of the sidebar menue to show and highlight.
$active = [
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "show",
    "active",
    "",
    "",
    "",
    ""
];
$query = "SELECT * FROM users WHERE created_by = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array($email), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}

$currencies = array(
    'ALL' => 'Albania Lek',
    'AFN' => 'Afghanistan Afghani',
    'ARS' => 'Argentina Peso',
    'AWG' => 'Aruba Guilder',
    'AUD' => 'Australia Dollar',
    'AZN' => 'Azerbaijan New Manat',
    'BSD' => 'Bahamas Dollar',
    'BBD' => 'Barbados Dollar',
    'BDT' => 'Bangladeshi taka',
    'BYR' => 'Belarus Ruble',
    'BZD' => 'Belize Dollar',
    'BMD' => 'Bermuda Dollar',
    'BOB' => 'Bolivia Boliviano',
    'BAM' => 'Bosnia and Herzegovina Convertible Marka',
    'BWP' => 'Botswana Pula',
    'BGN' => 'Bulgaria Lev',
    'BRL' => 'Brazil Real',
    'BND' => 'Brunei Darussalam Dollar',
    'KHR' => 'Cambodia Riel',
    'CAD' => 'Canada Dollar',
    'KYD' => 'Cayman Islands Dollar',
    'CLP' => 'Chile Peso',
    'CNY' => 'China Yuan Renminbi',
    'COP' => 'Colombia Peso',
    'CRC' => 'Costa Rica Colon',
    'HRK' => 'Croatia Kuna',
    'CUP' => 'Cuba Peso',
    'CZK' => 'Czech Republic Koruna',
    'DKK' => 'Denmark Krone',
    'DOP' => 'Dominican Republic Peso',
    'XCD' => 'East Caribbean Dollar',
    'EGP' => 'Egypt Pound',
    'SVC' => 'El Salvador Colon',
    'EEK' => 'Estonia Kroon',
    'EUR' => 'Euro Member Countries',
    'FKP' => 'Falkland Islands (Malvinas) Pound',
    'FJD' => 'Fiji Dollar',
    'GHC' => 'Ghana Cedis',
    'GIP' => 'Gibraltar Pound',
    'GTQ' => 'Guatemala Quetzal',
    'GGP' => 'Guernsey Pound',
    'GYD' => 'Guyana Dollar',
    'HNL' => 'Honduras Lempira',
    'HKD' => 'Hong Kong Dollar',
    'HUF' => 'Hungary Forint',
    'ISK' => 'Iceland Krona',
    'INR' => 'India Rupee',
    'IDR' => 'Indonesia Rupiah',
    'IRR' => 'Iran Rial',
    'IMP' => 'Isle of Man Pound',
    'ILS' => 'Israel Shekel',
    'JMD' => 'Jamaica Dollar',
    'JPY' => 'Japan Yen',
    'JEP' => 'Jersey Pound',
    'KZT' => 'Kazakhstan Tenge',
    'KPW' => 'Korea (North) Won',
    'KRW' => 'Korea (South) Won',
    'KGS' => 'Kyrgyzstan Som',
    'LAK' => 'Laos Kip',
    'LVL' => 'Latvia Lat',
    'LBP' => 'Lebanon Pound',
    'LRD' => 'Liberia Dollar',
    'LTL' => 'Lithuania Litas',
    'MKD' => 'Macedonia Denar',
    'MYR' => 'Malaysia Ringgit',
    'MUR' => 'Mauritius Rupee',
    'MXN' => 'Mexico Peso',
    'MNT' => 'Mongolia Tughrik',
    'MZN' => 'Mozambique Metical',
    'NAD' => 'Namibia Dollar',
    'NPR' => 'Nepal Rupee',
    'ANG' => 'Netherlands Antilles Guilder',
    'NZD' => 'New Zealand Dollar',
    'NIO' => 'Nicaragua Cordoba',
    'NGN' => 'Nigeria Naira',
    'NOK' => 'Norway Krone',
    'OMR' => 'Oman Rial',
    'PKR' => 'Pakistan Rupee',
    'PAB' => 'Panama Balboa',
    'PYG' => 'Paraguay Guarani',
    'PEN' => 'Peru Nuevo Sol',
    'PHP' => 'Philippines Peso',
    'PLN' => 'Poland Zloty',
    'QAR' => 'Qatar Riyal',
    'RON' => 'Romania New Leu',
    'RUB' => 'Russia Ruble',
    'SHP' => 'Saint Helena Pound',
    'SAR' => 'Saudi Arabia Riyal',
    'RSD' => 'Serbia Dinar',
    'SCR' => 'Seychelles Rupee',
    'SGD' => 'Singapore Dollar',
    'SBD' => 'Solomon Islands Dollar',
    'SOS' => 'Somalia Shilling',
    'ZAR' => 'South Africa Rand',
    'LKR' => 'Sri Lanka Rupee',
    'SEK' => 'Sweden Krona',
    'CHF' => 'Switzerland Franc',
    'SRD' => 'Suriname Dollar',
    'SYP' => 'Syria Pound',
    'TWD' => 'Taiwan New Dollar',
    'THB' => 'Thailand Baht',
    'TTD' => 'Trinidad and Tobago Dollar',
    'TRY' => 'Turkey Lira',
    'TRL' => 'Turkey Lira',
    'TVD' => 'Tuvalu Dollar',
    'UAH' => 'Ukraine Hryvna',
    'GBP' => 'United Kingdom Pound',
    'USD' => 'United States Dollar',
    'UYU' => 'Uruguay Peso',
    'UZS' => 'Uzbekistan Som',
    'VEF' => 'Venezuela Bolivar',
    'VND' => 'Viet Nam Dong',
    'YER' => 'Yemen Rial',
    'ZWD' => 'Zimbabwe Dollar'
);
//Requires the header.
require 'header.php';
?>
<body id="page-top">
    <?php
    //Requires the navigation menue.
    require 'navigation.php';
    ?>
    <div id="wrapper">
        <?php
        //Requires the sidebar.
        require 'sidebar.php';
        ?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Reports</a>
                    </li>
                    <li class="breadcrumb-item active">Options</li>
                </ol>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="icon octicon octicon-person"></i>&nbsp;
                        Add / Update Options for user</div>
                    <div class="card-body">
                        <form class="form-horizontal form-bordered" method="post">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-append" data-area="true">
                                        <span class="input-group-text icon octicon octicon-person rounded-0"></span>
                                        <select title="Select User" class="form-control form-control-sm w-100" id="users" name="users">
                                            <option selected="selected" disabled="disabled" value="">--Select User--</option>
                                            <?php foreach ($results as $result) { ?>
                                                <option value="<?= $result["email"]; ?>"><?= $result["email"] . " (" . $result["fullname"] . ")"; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-calendar rounded-0"></span>
                                        <input type="text" title="Select Sick Days" class="form-control form-control-sm w-100 datepicker" placeholder="--select sick days---" value="" name="sick_days" id="sick_days" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-calendar rounded-0"></span>
                                        <input type="text" title="Select Leave Days" class="form-control form-control-sm w-100 datepicker" placeholder="--select leave days--" value="" name="leave_days" id="leave_days" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-clock rounded-0"></span>
                                        <input type="text" title="Select Start Time" class="form-control form-control-sm w-100 datepicker" placeholder="--select start time--" value="" name="start_time" id="start_time" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-clock rounded-0"></span>
                                        <input type="text" title="Select End Time" class="form-control form-control-sm w-100 datepicker" placeholder="--select end time--" value="" name="end_time" id="end_time" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-list-ordered rounded-0"></span>
                                        <input type="text" class="form-control form-control-sm w-100 decimal" placeholder="--enter deduction per hour--" value="" name="deduction_hour" id="deduction_hour" title="Enter Deduction Per Hour" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-list-ordered rounded-0"></span>
                                        <input type="text" class="form-control form-control-sm w-100 decimal" placeholder="--enter basic salary--" value="" name="basic_salary" id="basic_salary" title="Enter Basic Salary" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-list-ordered rounded-0"></span>
                                        <input type="text" class="form-control form-control-sm w-100 decimal" placeholder="--enter bonus--" value="" name="bonus" id="bonus" title="Enter Bonus" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-list-ordered rounded-0"></span>
                                        <input title="Enter Tax" type="text" class="form-control form-control-sm w-100 decimal" placeholder="--enter tax %--" value="" name="tax" id="tax" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group" style="margin-bottom: -1rem;">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-list-ordered rounded-0"></span>
                                        <input title="Enter Pension" type="text" class="form-control form-control-sm w-100 decimal" placeholder="--enter pension %--" value="" name="pension" id="pension" style="width:350px !important;">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-append" data-area="true">
                                        <span class="input-group-text icon octicon octicon-arrow-right rounded-0"></span>
                                        <select class="form-control form-control-sm w-100" id="currency" name="currency">
                                            <option title="Select Currency" selected="selected" disabled="disabled" value="">--Select Currency--</option>
                                            <?php foreach ($currencies as $currencyKey => $currencyVal) { ?>
                                                <option value="<?= $currencyKey; ?>"><?= $currencyKey . " (" . $currencyVal . ")" ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label></label>
                                <div class="input-group mb-3">
                                    <?= getHiddenInputString() . PHP_EOL ?>
                                    <input type="button" id="save_update" class="btn btn-primary" value="Save / Update" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
//Requires the footer.
            require 'footer.php';
            ?>
        </div>
    </div>
    <script>
        $('#sick_days, #leave_days').datepicker({
            multidate: true,
            format: 'mm/dd/yyyy'
        });
        $('#start_time').datetimepicker({
            format: 'HH:mm'
        });
        $('#end_time').datetimepicker({
            format: 'HH:mm'
        });
        $("#users").change(function () {
            $.ajax('<?= getWebRootUrl(); ?>/get-options.php',
                    {
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            users: $(this).val()
                        },
                        success: function (data) {
                            var sick_days = data.sick_days.split(",");
                            var leave_days = data.leave_days.split(",");
                            var normal_start_day_parts = data.normal_start_time.split(":");
                            var normal_end_day_parts = data.normal_end_time.split(":");
                            var deduction_hour = data.deduction_hour;
                            var basic_salary = data.basic_salary;
                            var bonus = data.bonus;
                            var currency = data.currency;
                            var tax = data.tax;
                            var pension = data.pension;
                            var leave_days_dates = [];
                            var sick_days_dates = [];
                            for (var i = 0; i < sick_days.length; i++) {
                                var parts = sick_days[i].split("/");
                                sick_days_dates.push(new Date(parts[2], parts[0] - 1, parts[1]));
                            }
                            for (var i = 0; i < leave_days.length; i++) {
                                var parts = leave_days[i].split("/");
                                leave_days_dates.push(new Date(parts[2], parts[0] - 1, parts[1]));
                            }
                            $('#sick_days,#leave_days').datepicker({dateFormat: 'mm/dd/yyyy'});
                            $('#sick_days').datepicker('setDates', sick_days_dates);
                            $('#leave_days').datepicker('setDates', leave_days_dates);
                            $('#start_time').datetimepicker('date', moment(new Date()).hours(normal_start_day_parts[0]).minutes(normal_start_day_parts[1]).seconds(0).milliseconds(0));
                            $('#end_time').datetimepicker('date', moment(new Date()).hours(normal_end_day_parts[0]).minutes(normal_end_day_parts[1]).seconds(0).milliseconds(0));
                            $("#deduction_hour").val(deduction_hour);
                            $("#basic_salary").val(basic_salary);
                            $("#bonus").val(bonus);
                            $("#currency").val(currency);
                            $("#tax").val(tax * 100);
                            $("#pension").val(pension * 100);
                        },
                        error: function () {
                        }
                    });
        });
        $("#save_update").click(function () {
            $.ajax('<?= getWebRootUrl(); ?>/save-options.php',
                    {
                        type: 'POST',
                        data: {
                            users: $("#users").val(),
                            sick_days: $('#sick_days').val(),
                            leave_days: $('#leave_days').val(),
                            start_time: $('#start_time').val(),
                            end_time: $('#end_time').val(),
                            deduction_hour: $("#deduction_hour").val(),
                            basic_salary: $("#basic_salary").val(),
                            bonus: $("#bonus").val(),
                            currency: $("#currency").val(),
                            tax: $("#tax").val(),
                            pension: $("#pension").val()
                        },
                        success: function (data) {
                            alert(data);
                        },
                        error: function () {
                            alert("An error occurred! Try again please.");
                        }
                    });
        });
    </script>
</body>
</html>