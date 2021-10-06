<!DOCTYPE html>
<html>
<body>

<?php
$find = array(' ','/');
$replace = array("-",'-');

//$arr = "Delhi/Ncr";
$arr = "South Delhi";
//$find = array("Hello","world");
//$replace = array("B");
//$arr = array("Hello","Hello","!");
print_r(str_replace($find,$replace,$arr));



?>

</body>
</html>