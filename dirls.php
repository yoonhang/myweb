<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title>YDir</title>
<STYLE>
BODY, TD, SELECT, INPUT, TEXTAREA { FONT-SIZE:10pt; COLOR:#333333; FONT-FAMILY:Arial; line-height:150%; }
A:link    { COLOR: #0000dd; TEXT-DECORATION: none; }
A:visited { COLOR: #0000dd; TEXT-DECORATION: none; }
A:active  { COLOR: #0000dd; TEXT-DECORATION: none; }
A:hover   { COLOR: #0000dd; TEXT-DECORATION: underline }
</STYLE>

</head>

    
<body>
	List of files:<br>

<?php
// 20201015, kyh

$root = "./";

$param = $_GET["path"];
//echo "$param<br>";

$isroot = 0;
if($param == "") $isroot = 1;

// 폴더명 지정
$dir = "";
if($isroot == 0)
{
	$dir .= $param;
}


// 핸들 획득
$handle  = opendir($root . $dir);

$files = array();
$dirs = array();

// 디렉터리에 포함된 파일을 저장한다.
while (false !== ($filename = readdir($handle))) {
	/*
	if($filename == "." || $filename == ".."){
		continue;
	}
	*/
	// . 으로 시작하는 파일 무시
	if(substr($filename, 0, 1) == ".") continue;

	
	if(is_file($root . $dir . "/" . $filename))
	{
		$files[] = $filename;
	}
	else
	{
		$dirs[] = $filename;
	}
}

// 핸들 해제 
closedir($handle);

// 정렬, 역순으로 정렬하려면 rsort 사용
sort($files);
sort($dirs);



// Root 가 아닌 경우, Home 등 버튼을 표시한다.
if($isroot == 0)
{
	// Home
	echo "<a href=?path=>[home]</a><br>";

	// 상위 폴더
	$pos = strrpos($dir, '/');
	$updir = substr($dir, 0, $pos);
	echo "<a href=?path=";
	echo $updir;
	echo ">[upper]</a><br>";
	
	echo "<br>";
}

// 폴더명 출력
$hasdir = 0;
foreach ($dirs as $f)
{
	$hasdir = 1;

	echo "<a href=?path=";
	echo $param;
	echo "/";
	echo $f;
	echo ">[";
	echo $f;
	echo "]</a><br>";
}
if($hasdir) echo "<br>";

// 파일명을 출력한다.
foreach ($files as $f) {
	echo "<a href=";
	echo $param;
	echo "/";
	echo $f;
	echo ">";
	echo $f;
	echo "</a><br>";
} 
?>
</body>
</html>

