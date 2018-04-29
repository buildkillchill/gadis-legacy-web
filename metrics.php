<?php
	header("Content-Type: application/json");
	require 'settings.php';
	// 0 = Connected or Joined
	// 1 = Disconnected or Left
	// 2 = Kicked
	$ACTIONS = [ 0, 1, 2, ];
	// 0 = GMod
	// 1 = Discord
	$ACCOUNT = [ 0, 1, ];
	function output(int $code, string $message = NULL, $exno = NULL, string $exmsg = NULL)
	{
		if($code == 0x000) $status = "SUCCESS";
		if($code >  0x000) $status = "ERROR";
		$output = [ "version" => "1.0a", ];
		$output["status"] = [ "code" => $code, ];
		if($message !== null)
			$output["status"]["message"] = $status.": ".$message;
		else
			$output["status"]["message"] = $status;
		if(isset($exno) && isset($exmsg)) $output["exception"] = [ "errno" => $exno, "msg" => $exmsg, ];
		echo json_encode($output);
		die();
	}
	if(!isset($_GET["id"]) || !isset($_GET["act"]) || !isset($_GET["acc"])) output(0x101, "Missing parameters");
	if(!in_array($_GET["acc"], $ACCOUNT)) output(0x102, "Invalid account");
	if(!in_array($_GET["act"], $ACTIONS)) output(0x103, "Invalid action");
	try
	{
		$conn = new mysqli($HOST, $USER, $PASSWORD, $DATABASE);
		if($conn->connect_errno) output(0x201, "MySQL connection failed.");
		if(!$conn->query("INSERT INTO `metrics` (id, account, action) VALUES (".$_GET["id"].",".$_GET["acc"].",".$_GET["act"].")")) output(0x2002, "MySQL query failed.", $conn->errno, $conn->error);
		output(0x000);
	} catch (Exception $e) {
		output(0xFFF, "Unknown error", 0, $e->getMessage());
	}
?>