<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * track-user.php
 * ---------------------------------------------------------------
 * Show a MapQuest map to track the user position live.
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

//Gets the user information to display in the dropdown list of emails.
$querySelect = "SELECT * FROM users WHERE created_by = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($querySelect, array($_SESSION["admin_email"]), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}
//Gets the user coordinates and active dates from sessions created after posting data to the file get-tracking-data.php.
$coordinates = $_SESSION["coordinates"];
$activeDates = $_SESSION["activeDates"];

if (isset($_SESSION['info']) && !empty($_SESSION['info']) && !is_null($_SESSION['info'])) {
    //Gets the user information from "users" table if session info is not empty.
    $queryInfo = "SELECT id, fullname, email, profile_img, tracking_time_interval FROM users WHERE email = ?";
    $sth = mf_do_query($queryInfo, array($_SESSION['info']["email"]), $dbh);
    $info = mf_do_fetch_result($sth);
} else {
    //Case where session info is empty.
    $info = $_SESSION["info"];
}
//Creates variable to hold server sent events JavaScript code.
$sseCode = "";
$script = "";
//Creates a variable to hold the delete locations button HTML code.
$deleteButton = "";
//If a calander date was selected and coordinates are not empty.
if (isset($_SESSION["datepicker"]) && $coordinates !== '[]') {
    //Server side events JavaScript code should only be loaded if the selected date is today (+/-) one day.
    if (isSseAllowed($_SESSION["datepicker"])) {
        $sseCode .= '
//Starts SSE.
var source = new EventSource("sse.php");
//On SSE data reception.
source.onmessage = function (event) {
    var parsedJson = JSON.parse(event.data);
    //If data received is # than the data already used.
    if (!coordinates.equals(parsedJson)) {
        coordinates = parsedJson;
        //Show the new location(s) on the map.
        var featureGroup = generateMarkersFeatureGroup(parsedJson);
        featureGroup.addTo(map);
        map.fitBounds(featureGroup.getBounds());
        //Shows "LIVE" as message.
        $("#notification").addClass("badge badge-success").html("LIVE");
        //Calculates and shows distance crossed and speed.
        $("#distanceCrossed").html(calculateDistance());
    }
};';
    }
}
//If a calander date was selected and coordinates are empty.
if (isset($_SESSION["datepicker"]) && $coordinates === '[]') {
    //Server side events JavaScript code should only be loaded if the selected date is today (+/-) one day.
    if (isSseAllowed($_SESSION["datepicker"])) {
        $sseCode .= '
//Starts SSE.
var source = new EventSource("sse.php");
//On SSE data reception.
source.onmessage = function (event) {
    var arrayData = JSON.parse(event.data);
    //If data received is not empty, reloads the page to load new SSE code.
    if (arrayData.length > 0) {
        location.reload(true);
    }
};';
    }
}
//If coordinates are not empty, loads an empty map.
if ($coordinates === "[]") {
    $script .= "
//Loads an empty MapQuest map and SSE code.
window.onload = function () {
    //MapQuest key.
    L.mapquest.key = '" . MQ_KEY . "';
    var baseLayer = L.mapquest.tileLayer('map');
    //Centering the map.
    var map = L.mapquest.map(
        'map', {
            center: [10.5289167, 7.4566446],
            layers: baseLayer,
            zoom: 12
        }
    );
    //Setting controls
    L.control.layers({
        'Map': baseLayer,
        'Hybrid': L.mapquest.tileLayer('hybrid'),
        'Satellite': L.mapquest.tileLayer('satellite')
    }).addTo(map);
    map.addControl(L.mapquest.navigationControl());
    " . $sseCode . "
};";
//Loads map with coordinates.
} else {
    $script .= "
//Loads map with coordinates and SSE code.
var coordinates = " . $coordinates . ";
window.onload = function () {
    //MapQuest key.
    L.mapquest.key = '" . MQ_KEY . "';
    var baseLayer = L.mapquest.tileLayer('map');
    //Centering the map.
    var map = L.mapquest.map(
        'map', {
            center: [coordinates[0][1], coordinates[0][2]],
            layers: baseLayer,
            zoom: 12
        });
        //Setting controls
        L.control.layers({
            'Map': baseLayer,
            'Hybrid': L.mapquest.tileLayer('hybrid'),
            'Satellite': L.mapquest.tileLayer('satellite')
        }).addTo(map);
    map.addControl(L.mapquest.navigationControl());
    //Generates markers to show on the map.
    var featureGroup = generateMarkersFeatureGroup(coordinates);
    //Adds markers to the map.
    featureGroup.addTo(map);
    //Makes all markers visibale on the map.
    map.fitBounds(featureGroup.getBounds());
    //Generates map markers.
    function generateMarkersFeatureGroup(response) {
        var group = [];
        var lines = [];
        var text = '';
        var j = 1;
        //Generates text to display in the text area.
        for (var i = 0; i < response.length; i++) {
            text += '-User was in location ' + j + ' on ' + response[i][3] + ' ' + response[i][0] + ' ' + response[i][4] + '\\n';
            var customIcon = L.mapquest.icons.marker({
                primaryColor: '#3b5998',
                size: 'sm',
                symbol: j++
            });
            var marker = L.marker({'lat': response[i][1], 'lng': response[i][2]}, {icon: customIcon}).bindPopup('</b>' + response[i][0] + '<b>');
            lines.push([response[i][1], response[i][2]]);
            group.push(marker);
        }
        var textarea = $('#textarea');
        textarea.text(text);
        textarea.scrollTop(textarea[0].scrollHeight);
        L.polyline(lines, {color: '#005300'}).addTo(map);
        return L.featureGroup(group);
    }
    // Warn if overriding existing method
    if (Array.prototype.equals)
        console.warn(\"Overriding existing Array.prototype.equals. Possible causes: New API defines the method, there's a framework conflict or you\'ve got double inclusions in your code.\");
    // attach the .equals method to Array's prototype to call it on any array
    Array.prototype.equals = function (array) {
        // if the other array is a falsy value, return
        if (!array)
            return false;
        // compare lengths - can save a lot of time 
        if (this.length != array.length)
            return false;
        for (var i = 0, l = this.length; i < l; i++) {
            // Check if we have nested arrays
            if (this[i] instanceof Array && array[i] instanceof Array) {
                // recurse into the nested arrays
                if (!this[i].equals(array[i]))
                    return false;
            } else if (this[i] != array[i]) {
                // Warning - two different object instances will never be equal: {x:20} != {x:20}
                return false;
            }
        }
        return true;
    }
    // Hide method from for-in loops
    Object.defineProperty(Array.prototype, 'equals', {enumerable: false});
	//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	//:::                                                                         :::
	//:::  This routine calculates the distance between two points (given the     :::
	//:::  latitude/longitude of those points). It is being used to calculate     :::
	//:::  the distance between two locations using GeoDataSource (TM) prodducts  :::
	//:::                                                                         :::
	//:::  Definitions:                                                           :::
	//:::    South latitudes are negative, east longitudes are positive           :::
	//:::                                                                         :::
	//:::  Passed to function:                                                    :::
	//:::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :::
	//:::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :::
	//:::    unit = the unit you desire for results                               :::
	//:::           where: 'M' is statute miles (default)                         :::
	//:::                  'K' is kilometers                                      :::
	//:::                  'N' is nautical miles                                  :::
	//:::                                                                         :::
	//:::  Worldwide cities and other features databases with latitude longitude  :::
	//:::  are available at https://www.geodatasource.com                         :::
	//:::                                                                         :::
	//:::  For enquiries, please contact sales@geodatasource.com                  :::
	//:::                                                                         :::
	//:::  Official Web site: https://www.geodatasource.com                       :::
	//:::                                                                         :::
	//:::               GeoDataSource.com (C) All Rights Reserved 2018            :::
        //:::   This program is free software: you can redistribute it and/or modify  :::
        //:::   it under the terms of the GNU General Public License as published by  :::
        //:::   the Free Software Foundation, either version 3 of the License, or     :::
        //:::   (at your option) any later version.                                   :::
        //:::                                                                         :::
        //:::   This program is distributed in the hope that it will be useful,       :::
        //:::   but WITHOUT ANY WARRANTY; without even the implied warranty of        :::
        //:::   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         :::
        //:::   GNU General Public License for more details.                          :::
        //:::                                                                         :::
        //:::   You should have received a copy of the GNU General Public License     :::
        //:::   along with this program.  If not, see https://www.gnu.org/licenses/   :::
	//:::                                                                         :::
	//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    function distance(lat1, lon1, lat2, lon2, unit) {
    	if ((lat1 === lat2) && (lon1 === lon2)) {
            return 0;
    	}
    	else {
            var radlat1 = Math.PI * lat1/180;
            var radlat2 = Math.PI * lat2/180;
            var theta = lon1-lon2;
            var radtheta = Math.PI * theta/180;
            var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
            if (dist > 1) {
                dist = 1;
            }
            dist = Math.acos(dist);
            dist = dist * 180/Math.PI;
            dist = dist * 60 * 1.1515;
            if (unit==='K') { dist = dist * 1.609344; }
            if (unit==='N') { dist = dist * 0.8684; }
            return dist;
    	}
    }
    //Calculates the total distance crossed and speed average. 
    function calculateDistance(){
        var totalSpeed = 0.00;
        var totalDistance = 0.00;
        var j = 0;
        var unit = 'M';
        var trackingCrossedDistanceUnit = 'K';
        var trackingTimeInterval = '" . $_SESSION['info']["tracking_time_interval"] . "';
        if(trackingCrossedDistanceUnit.toUpperCase() === 'K'){
            unit = 'K';
        }
        for (var i = 0; i < coordinates.length; i++) {
            j = i + 1;
            if(j <coordinates.length){
                var distanceCrossedBetweenTwoPoints = distance(coordinates[i][1], coordinates[i][2], coordinates[j][1], coordinates[j][2], unit);
                var speedBetweenTwoPoints = 60 * distanceCrossedBetweenTwoPoints / trackingTimeInterval;
                totalDistance = totalDistance + distanceCrossedBetweenTwoPoints;
                totalSpeed = totalSpeed + speedBetweenTwoPoints;
            }
        }
        var averageTotalSpeed = totalSpeed / coordinates.length;
        var speedUnit = '';
        var distanceUnit = '';
        //If unit is Meters / Kilometers
        if(trackingCrossedDistanceUnit.toUpperCase() === 'K'){
            if(totalDistance < 1){
                var crossedDistance = totalDistance * 1000;
                distanceUnit = ' meters';
            }else{
                var crossedDistance = totalDistance;
                distanceUnit = ' kilometers';
            }
            speedUnit = ' kilometers / hour';
        //If unit is Feets / Miles
        }else if(trackingCrossedDistanceUnit.toUpperCase() === 'M'){
            if(totalDistance < 1){
                var crossedDistance = totalDistance * 5280;
                distanceUnit = ' feets';
            }else{
                var crossedDistance = totalDistance;
                distanceUnit = ' miles';
            }
            speedUnit = ' miles / hour';
        }
        return ' | Crossed Distance: <b>' + crossedDistance.toFixed(2) + distanceUnit + '</b> | Average Speed: <b>' + averageTotalSpeed.toFixed(2) + speedUnit + '</b>';
    }
    $('#distanceCrossed').html(calculateDistance());
    " . $sseCode . "
};";
}
$script .= "
//Gets the available tracking dates.
var availableDates = " . json_encode($activeDates) . ";
//Datepicker settings.
$('#datepicker').datepicker({
    autoclose: true,
    endDate: '+1d',
    beforeShowDay: function (dt) {
        var dmy = ('0' + (dt.getMonth() + 1)).slice(-2) + '/' + ('0' + dt.getDate()).slice(-2) + '/' + dt.getFullYear();
        if ($.inArray(dmy, availableDates) !== -1) {
            return {
                //Shows available tracking dates in green.
                tooltip: 'Tracking data available',
                classes: 'green'
            };
        }
    }
}).on('changeDate', function (e) {
    //Submits the form on email selection.
    if ($('#email option:selected').val() !== '') {
        $('#form').submit();
    }
});
//Adds 0 before number when it is < 10, to have a nice formatting 
function checkTime(i) {
    if (i < 10) {
        i = '0' + i;
    }
    return i;
}
//Gets the user current timezone.
function getTimeZone() {
    var d = new Date();
    var offset = -1 * d.getTimezoneOffset() / 60;
    return offset = 'UTC' + (offset >= 0 ? '+' + offset : offset);
}
//Starts a time counter to display user time in real time.
function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('dateTime').innerHTML = 'Current Time: ' + h + ':' + m + ':' + s + ' ' + getTimeZone();
    t = setTimeout(function () {
        startTime();
    }, 500);
}
startTime();
//Submits the form on email selection.
$('#email').on('change', function () {
    $('#form').submit();
});
//Initializing swipebox.
$('.swipebox').swipebox();";
//Page title.
$title = "Admin - Tracking";
//Email displayed in navigation section.
$email = $_SESSION['admin_email'];
//Names of CSS files to load.
$css = [
    "dataTables.bootstrap4",
    "responsive.bootstrap4",
    "admin",
    "octicons",
    "mapquest",
    "bootstrap-datepicker",
    "swipebox"
];
//Names of JS files to load.
$js = [
    "jquery",
    "bootstrap.bundle",
    "jquery.easing",
    "jquery.dataTables",
    "dataTables.bootstrap4",
    "dataTables.responsive",
    "responsive.bootstrap4",
    "admin",
    "jquery.swipebox",
    "mapquest",
    "bootstrap-datepicker",
    "EventSource"
];
//Part of the sidebar menue to show and highlight.
$active = [
    "",
    "",
    "",
    "",
    "",
    "active",
    ""
];
//Requires the header.
require 'header.php';
?>
<body id="page-top">
    <?php
    //Requires the navigation.
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
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Users</a>
                    </li>
                    <li class="breadcrumb-item active">Tracking</li>
                </ol>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <form id="form" action="<?= getWebRootUrl(); ?>get-tracking-data.php" method="POST">
                                <div class="card-header small text-muted">
                                    <div class="form-group" style="margin-bottom: -1em;">
                                        <div class="input-group mb-3">
                                            <div class="input-group-append">
                                                <span class="input-group-text icon octicon octicon-device-mobile rounded-0"></span>
                                                <select class="form-control form-control-sm w-100" id="email" name="email">
                                                    <option selected="selected" disabled="disabled" value="">--Select Email</option>
                                                    <?php
                                                    $postValue = "";
                                                    if (sizeof($info) !== 0) {
                                                        $postValue = $info["email"];
                                                    }
                                                    foreach ($results as $result) {
                                                        if ($result["email"] === $postValue) {
                                                            ?>
                                                            <option selected="selected" value="<?= $result["email"]; ?>"><?= $result["email"] . " (" . $result["fullname"] . ")"; ?></option>
                                                        <?php } else { ?>
                                                            <option value="<?= $result["email"]; ?>"><?= $result["email"] . " (" . $result["fullname"] . ")"; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <table class="table table-bordered dt-responsive nowrap" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>User Picture</th>
                                                    <th>User Info</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center;">
                                                        <?php if (sizeof($info) !== 0) { ?>
                                                            <a href="<?= getWebRootUrl(); ?>images/users/<?= $info["profile_img"]; ?>" class="swipebox" title="<?= $info["fullname"]; ?>">
                                                                <img src="<?= getWebRootUrl(); ?>images/users/<?= $info["profile_img"]; ?>" class="img-thumbnail" alt="<?= $info["fullname"]; ?>" style="width: 160px;border: none;">                                                            
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="<?= getWebRootUrl(); ?>images/question_mark.png" class="swipebox" title="No Image Available">
                                                                <img src="<?= getWebRootUrl(); ?>images/question_mark.png" class="img-thumbnail" alt="" style="width: 160px;border: none;">
                                                            </a>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (sizeof($info) !== 0) {
                                                            ?>
                                                            <p><i class="icon octicon octicon-person space"></i>&nbsp;<?= $info["fullname"]; ?></p>
                                                            <p><i class="icon octicon octicon-mail space"></i>&nbsp;<?= $info["email"]; ?></p>
                                                            <p><i class="icon octicon octicon-device-mobile space"></i>&nbsp;<?= $info["email"]; ?></p>
                                                            <p><i class="icon octicon octicon-clock space"></i><i class="icon octicon octicon-location space"></i><span class="badge badge-info">Every&nbsp;<?= $info["tracking_time_interval"]; ?>&nbsp;minutes</span></p>
                                                        <?php } else { ?>
                                                            <p><i class="octicon octicon-person space"></i> ---</p>
                                                            <p><i class="icon octicon octicon-mail space"></i> ---</p>
                                                            <p><i class="icon octicon octicon-lock space"></i> ---</p>
                                                            <p><i class="icon octicon octicon-device-mobile space"></i> ---</p>
                                                            <p><i class="icon octicon octicon-clock space"></i><i class="icon octicon octicon-location space"></i> ---</p>
                                                            <p><i class="icon octicon octicon-dashboard space"></i> ---</p>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <hr>
                                        <textarea id="textarea" class="form-control" style="resize: none; height: 260px;" readonly="readonly"></textarea>
                                    </div>
                                </div>
                                <div class="card-footer small text-muted">
                                    <div class="form-group" style="margin-bottom: -1rem;">
                                        <div class="input-group mb-3">
                                            <div class="input-group-append">
                                                <span class="input-group-text icon octicon octicon-clock rounded-0"></span>
                                                <?php
                                                if (isset($_SESSION["datepicker"]) && !empty($_SESSION["datepicker"])) {
                                                    $postValue = $_SESSION["datepicker"];
                                                    ?>
                                                    <input type="text" class="form-control form-control-sm w-100 datepicker" value="<?= $postValue; ?>" name="datepicker" id="datepicker">
                                                <?php } else { ?>
                                                    <input type="text" class="form-control form-control-sm w-100 datepicker" value="<?= date("m/d/Y"); ?>" name="datepicker" id="datepicker">
                                                <?php }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?= getHiddenInputString() . PHP_EOL ?>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <p>
                                    <i class="icon octicon octicon-location"></i>Tracking Locations&nbsp;
                                    <span id="notification" style="margin-left: 20px;font-weight: bold;"></span>
                                </p>
                            </div>
                            <div class="card-body">
                                <div id="map" style="height:600px;"></div>
                            </div>
                            <div class="card-footer small text-muted">
                                <div id="dateTime" style="display:inline-block;"></div>
                                <div id="distanceCrossed" style="display:inline-block;"></div> 
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                //Requires the footer.
                require 'footer.php';
                ?>
            </div>
        </div>
    </div>
    <script>
        //Server sent events JavaScript code generated.
<?= $script; ?>
        $(document).ready(function () {
            //Datatable initialization.
            $('#dataTable').DataTable({
                responsive: true,
                paging: false,
                ordering: false,
                info: false,
                searching: false
            });
        });
    </script>
</body>
</html>
