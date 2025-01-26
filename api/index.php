<?php
include('../.env');

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
$pathinfo = preg_split("/\//", $_SERVER['PATH_INFO']);
if ($pathinfo[0] == "") array_shift($pathinfo);

$rsc = array_shift($pathinfo);
$action = array_shift($pathinfo);
if (count($pathinfo)) {
    $id = array_shift($pathinfo);
}

if ($action === "get") {
    
}
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
        case "update_record":
            $out = updateRecord();
            break;
        case "rm_record":
            $out = rmRecord();
            break;
        case "new_record":
            $out = newRecord();
            break;
        case "rm_zone":
            $out = rmZone($in['domain']);
            break;
        case "new_zone":
            $out = newZone();
            break;
        default:

    }
}

if ($out) {
    header("Content-Type: application/json");
    print json_encode($out);
}

function rmRecord() {
    global $link;
    global $in;

    $domain = $in['domain'];
    $out = new stdClass();

    if (!isset($domain)) {
        $out->status = "error";
        $out->error = "rmRecord: missing domain";
        return $out;
    }
    
    if (!isset($in['id'])) {
        $out->status = "error";
        $out->error = "rmRecord: missing record id";
        return $out;
    }

    $sql = "DELETE FROM dns WHERE id='".mysqli_real_escape_string($link, $in['id'])."';";

    $result = mysqli_query($sql);
    
    if ($result) {
        $out->status = "ok";
        $out->message = "rmRecord: Record id {$in['id']} removed";
    } else {
        $out->status = "error";
        $out->error = "rmRecord: error removing record id {$in['id']} from dns";
    }
    return $out;

}

function newRecord() {
    global $link;
    global $in;

    // First grab one record so we can get field names
    $result = mysqli_query($link, "SELECT * FROM dns LIMIT 1");
    $out = new stdClass();
    $in['zone'] = $in['domain'];

    if ($result) {
        $fieldobjs = mysqli_fetch_fields($result);
        $fields = array();
        $vals = array();

        // loop through our fields, looking for any matching keys in our input
        foreach ($fieldobjs as $field) {
            if (array_key_exists($field->name, $in)) {
                $fields[] = $field->name;
                $vals[] = mysqli_real_escape_string($link, $in[$field->name]);
            }
        }

        $sql = "INSERT INTO dns (".implode(",", $fields).") VALUES ('".implode("','", $vals)."');";

        print $sql."\n";
        $result = mysqli_query($link, $sql);
        $newid = mysqli_insert_id($link);

        if ($result) {
            $out->status = "ok";
            $out->sql = $sql;
            $out->id = $newid;
            $out->message = "newRecord: record added successfully";
        } else {
            $out->status = "error";
            $out->error = "newRecord: error creating new record";
            $out->sql = $sql;
            $out->in = json_encode($in);
        }
    }
    return $out;
}

function rmZone($domain) {
    global $link;
    global $in;
    global $env;

    if (!isset($domain)) {
        $out = new stdClass();
        $out->status = "error";
        $out->error = "rmZone: missing domain";
        return $out;
    }

    $backup = `mysqldump --compact -t -u{$env->db->user} -p{$env->db->pass} -w"zone='{$domain}'" system dns`;

    $dumpfile = "/home/cdr/backup/$domain";
    if (file_exists($dumpfile.".sql")) {
        $idx = 1;
        while (file_exists($dumpfile."_".$idx.".sql")) {
            $idx++;
        }
        $dumpfile = $dumpfile . "_" . $idx;
    } 
    file_put_contents($dumpfile.".sql", $backup);
    $out = new stdClass();
    $out->status = "ok";
    $out->data = $backup;
    $out->message = "Zone '$domain' removed";

    return $out;
}

function newZone() {
    global $link;
    global $in;

    // First make sure we have a domain, return error if not
    if (!array_key_exists("domain", $in)) {
        $out = new stdClass();
        $out->status = "error";
        $out->error = "missing domain";
        return $out;
    }

    // Now make sure we don't already have records for this domain, return error if we do
    $results = mysqli_query($link, "SELECT * from dns WHERE zone='".mysqli_real_escape_string($link, $in['domain'])."'");
    if (mysqli_num_rows($results)) {
        $out = new stdClass();
        $out->status = "error";
        $out->error = "domain '{$in['domain']}' already exists";
        return $out;
    }

    // Set default responsible peerson if one was not provided
    if (!array_key_exists("email", $in)) {
        $in['email'] = "cdr@netoasis.net";
    }
    $in['email'] = preg_replace("/@/", '.', $in['email']);
    
    // Make serial for SOA record
    $in['serial'] = date("YmdH");

    $dns = array("INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'SOA','@',NULL,NULL,'ns.netoasis.net.','%%email%%',%%serial%%,14400,3600,604800,28800, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'NS','@',NULL,'ns.netoasis.net.',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'NS','@',NULL,'ns2.netoasis.net.',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'MX','@',10,'mail.%%domain%%.',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'A','@',NULL,'198.46.172.170',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'A','www',NULL,'198.46.172.170',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'A','mail',NULL,'198.46.172.170',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'CNAME','imap',NULL,'mail.%%domain%%.',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'CNAME','pop',NULL,'mail.%%domain%%.',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');",
    "INSERT INTO `dns` VALUES (null,'%%domain%%',3600,'CNAME','smtp',NULL,'mail.%%domain%%.',NULL,NULL,NULL,NULL,NULL,NULL,NULL, '', '');");

    foreach ($dns as $rec) {
        $rec = preg_replace_callback("/\%\%([^%]*)\%\%/", function($match) {
            global $in;
            if (array_key_exists($match[1], $in)) {
                return $in[$match[1]];
            } else {
                return "";
            }
        }, $rec);
        print $rec."\n";
        // mysqli_query($link, $rec);
    }
}

function getZones() {
    global $link;
    global $in;
    $out = array();
    $result = mysqli_query($link, "SELECT DISTINCT(zone) from dns");

    while ($row = mysqli_fetch_object($result)) {
        $out[] = $row->zone;
    }
    $wrap = ["status"=>"ok", "data"=>$out];
    return $wrap;
}

function getZone() {
    global $link;
    global $in;
    $out = array();
    $result = mysqli_query($link, "SELECT * from dns where zone='{$in['zone']}'");

    while ($row = mysqli_fetch_object($result)) {
        $out[] = $row;
    }
    $wrap = ["status"=>"ok", "data"=>$out];
    return $wrap;
}
