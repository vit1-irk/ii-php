<?php
date_default_timezone_set("UTC");
require_once("transports.php");

if (!file_exists("config.php")) copy("config.default.php", "config.php");

require_once("config.php");
require_once("filter.php");

function checkUser($authstr) {
	global $parr;
	for($i=0;$i<count($parr);$i++) {
		if($parr[$i][0]==$authstr) {
			$authname=$parr[$i][1];

			if (isset($parr[$i][2])) $addr=$parr[$i][2];
			else $addr=$i+1;
			break;
		}
	}
	if(isset($authname) and !empty($authname)) return [$authname, $addr];
	else return false;
}

function b64c($s) {
	return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}

function b64d($s) {
	return base64_decode(str_pad(strtr($s, '-_', '+/'), strlen($s) % 4, '=', STR_PAD_RIGHT),true);
}

function hsh($s) {
	$s1 = strtr(b64c(hash("sha256",$s,true)), "-_", "Az");
	return substr($s1,0,20);
}

function hsh_file($filename) {
	$s1 = strtr(b64c(hash_file("sha256",$filename,true)), "-_", "Az");
	return substr($s1,0,20);
}

$access=new BaseAccess($transport, $blacklist_file, $msgtextlimit);
$file_access = new FileAccess($file_transport, $fblacklist_file, $filesize_limit, $max_dir_quota);

function pointSend($msg, $authname, $addr) {
	$goodmsg=explode("\n", b64d($msg));

	$echo=$goodmsg[0];
	$receiver=$goodmsg[1];
	$subj=$goodmsg[2];
	$rep=$goodmsg[4];
	$time=time();

	$othermsg="";

	for($i=5;$i<count($goodmsg);$i++) {
		if($i==(count($goodmsg)-1)) {
			$othermsg.=$goodmsg[$i];
		} else {
			$othermsg.=$goodmsg[$i]."\n";
		}
	}

	if(substr($rep,0,7)=="@repto:") {
		$repto=substr($rep,7);
	} else {
		$repto=false;
		if(!empty($othermsg)) {
			$othermsg=$rep."\n".$othermsg;
		} else {
			$othermsg=$rep;
		}
	}

	if(empty($subj) or empty($othermsg)) {
		die("error: empty message or subject!");
	}

	$sent=msg_to_ii($echo,$othermsg,$authname,$addr,$time,$receiver,$subj,$repto);
	if ($sent) {
		echo "msg ok:".$sent;
	} else echo "error: couldn't save the message (server problem?)";
}

function msg_to_ii($echo, $msg, $username, $addr, $time, $receiver, $subj, $repto) {
	global $access;

	$message=[
		"tags" => ["ii" => "ok", "repto" => $repto],
		"echo" => $echo,
		"time" => $time,
		"from" => $username,
		"addr" => $addr,
		"to" => $receiver,
		"subj" => $subj,
		"msg" => $msg,
		"repto" => $repto
	];
	return $access->saveMessage($msgid=NULL, $echo, $message, $raw=false);
}

function displayEchoList($echos=null, $counter=false, $descriptions=false) {
	global $echolist, $access;

	$public_echolist_assoc=[];

	foreach ($echolist as $line) {
		$public_echolist_assoc[$line[0]]=$line[1];
	}

	if ($echos===null) {
		$echos=array_keys($public_echolist_assoc);
	}

	foreach ($echos as $echo) {
		if ($access->checkEcho($echo)) {
			echo $echo;
		}

		if ($counter) {
			echo ":".$access->countMessages($echo);
		}

		if ($descriptions) {
			echo ":";
			if (isset($public_echolist_assoc[$echo])) {
				echo $public_echolist_assoc[$echo];
			}
		}

		echo "\n";
	}
}

?>
