<?php
include('.env');

$in = $_REQUEST;
$out = array();
$link = mysqli_connect($env->db->host, $env->db->user, $env->db->pass, "system");

session_start();

/* check connection */
if (mysqli_connect_errno()) {
     printf("Connect failed: %s\n", mysqli_connect_error());
     exit();
}

$out = array();

if (array_key_exists("x", $in)) {
    switch($in['x']) {
        case "get_zones":
            $out = getZones();
            break;
        case "get_record":
        case "get_zone":
            $out = getZone();
            break;
        case "get_all":
            $out = getAllDNS();
            break;
        default:

    }
}

if ($out) {
    header("Content-Type: application/json");
    print json_encode($out);
}

function getZones() {
    global $link;
    global $in;
    $out = array();
    $result = mysqli_query($link, "SELECT DISTINCT(zone) from dns");

    while ($row = mysqli_fetch_object($result)) {
        $out[] = $row->zone;
    }
    return $out;
}

function getZone() {
    global $link;
    global $in;
    $out = array();
    $result = mysqli_query($link, "SELECT * from dns where zone='{$in['zone']}'");

    while ($row = mysqli_fetch_object($result)) {
        $out[] = $row;
    }
    return $out;
}
