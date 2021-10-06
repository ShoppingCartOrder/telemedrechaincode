<?php 
session_start();

// Code Sinppet-1 for Shield Square
include 'ss2.php';

$shieldsquare_userid   = ""; // Enter the UserID of the user
$shieldsquare_calltype = 1;
$shieldsquare_pid      = "";
$shieldsquare_response = shieldsquare_ValidateRequest($shieldsquare_userid, $shieldsquare_calltype, $shieldsquare_pid);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="en" />
<title>Sample Home Page</title>
</head>
<body>
<?php
if ($shieldsquare_response->responsecode == 0)
	echo "Allow the user request";
elseif ($shieldsquare_response->responsecode == 1)
	echo "Monitor this Traffic";
elseif ($shieldsquare_response->responsecode == 2)
	echo "Show CAPTCHA before displaying the content";
elseif ($shieldsquare_response->responsecode == 3)
	echo "Block This request";
elseif ($shieldsquare_response->responsecode == 4)
	echo "Feed Fake Data";
elseif ($shieldsquare_response->responsecode == -1)
{
	echo "Curl Error - " . $shieldsquare_response->reason . "<BR>";
	echo "Please reach out to ShieldSquare support team for assistance <BR>";
	echo "Allow the user request";
}
?>

<script type="text/javascript">
	var __uzdbm_a = "<?php echo $shieldsquare_response->pid; ?>"; 
	var __uzdbm_b = "<?php echo $shieldsquare_response->url; ?>";
	<?php echo $shieldsquare_response->dynamic_JS; ?>
</script>
<script type="text/javascript" src="https://cdn.perfdrive.com/static/browser_detect_min.js"></script>

<h1>
	<BR>YOUR CODE GOES HERE
</h1>
</body>
</html>
