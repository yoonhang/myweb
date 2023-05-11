<?php
/*
$ip = $_SERVER['REMOTE_ADDR'];
$arrip = explode(".", $ip);

if ($arrip[0] != "172" || $arrip[1] != "16" || ($arrip[2] != "20" && $arrip[2] != "23" && $arrip[2] != "31"))
{
	echo "Access denied.";
	exit;
}
*/
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title></title>
<STYLE>
BODY, TD, SELECT, INPUT, TEXTAREA { FONT-SIZE:10pt; COLOR:#333333; FONT-FAMILY:Arial; line-height:150%; }
A:link    { COLOR: #0000dd; TEXT-DECORATION: none; }
A:visited { COLOR: #0000dd; TEXT-DECORATION: none; }
A:active  { COLOR: #0000dd; TEXT-DECORATION: none; }
A:hover   { COLOR: #0000dd; TEXT-DECORATION: underline }
</STYLE>
</head>

<body leftmargin="20" topmargin="20">

<?php
$arrfile = array();

$arrfile = read_dir(".", $arrfile);

sort($arrfile);

echo "<h3 style='color:#aa0000;'>Execute Lists..</h3>\n";

echo "<table width='100%' cellpadding='2' cellspacing='1' border='0' bgcolor='#cccccc'>\n";

for ($i=0; $i<sizeof($arrfile); $i++)
{
	if ($i%2 == 0) echo "<tr bgcolor='#ffffff'>\n";
	else           echo "<tr bgcolor='#eeeeee'>\n";
	
	$fexe = explode(".", $arrfile[$i]);
	//echo $fexe[2]."<br>";

	if ($fexe[2] == "php" || $fexe[2] == "htm" || $fexe[2] == "html" || $fexe[2] == "txt" || $fexe[2] == "mp4") {
		echo "<td align='center'>".($i+1)."</td>\n";
		echo "<td style='padding-left:10px;'><a href='".$arrfile[$i]."'>".$arrfile[$i]."</a></td>\n";
		//echo "<td align='right' style='padding-right:10px;'>".conv_filesize(filesize($arrfile[$i]))."</td>\n";
		echo "</tr>\n";
	}
}

echo "</table>\n";
?>


<hr>
<div align="left"><b><font size="2">
<a href="http://118.128.66.133/trac/ideafactory/wiki/yhkim">홈</a>|
<a href="javascript:history.back(-1)">뒤로</A>|
<a href="javascript:history.go(1)">앞으로</A>|
<a href="javascript:location.reload()">새로고침</a>|
<a href="javascript:window.close()">창닫기</A>|
<a href="javascript:window.print()">인쇄 </a> 
</font></b></div>

</body>

</html>

<?php

function read_dir($path, $alist)
{
	$dir = opendir($path);

	while($file=readdir($dir))
	{
		if ($file == "." || $file == ".." || $file == "index.htm") continue;

		$pathfile = $path."/".$file;

		if (substr(basename($pathfile),0,1) == '.')
		{
			continue;
		}
/* 현재 디렉토리만 검색....
		else if (is_dir($pathfile))
		{
			$npath = $path."/".$file;
			$alist = read_dir($npath, $alist);
		}
*/
		else if (is_file($pathfile))
		{
			$alist[] = $pathfile;
		}
	}

	closedir($dir);

	return $alist;
}

function conv_filesize($size)
{
	if ($size < 1024) return "< 1 KB"; //return $size." Byte";
	else if ($size < (1024*1024)) return sprintf("%.2f",($size/1024))." KB";
	else if ($size < (1024*1024*1024)) return sprintf("%.2f",($size/(1024*1024)))." MB";
	else if ($size < (1024*1024*1024*1024)) return sprintf("%.2f",($size/(1024*1024*1024)))." GB";
	else if ($size < (1024*1024*1024*1024*1024)) return sprintf("%.2f",($size/(1024*1024*1024*1024)))." TB";
	else if ($size < (1024*1024*1024*1024*1024*1024)) return sprintf("%.2f",($size/(1024*1024*1024*1024*1024)))." PB";
	else return $size." Byte";

	return $strsize;
}

?>
