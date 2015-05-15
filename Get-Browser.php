<?php
function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";
    $strPlatform = '';
    
    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Edge\//i',$u_agent))
    {
        $bname = 'Edge';
        $ub = "Edge";
    }
    elseif(preg_match('/Trident/i',$u_agent))
    {
    	$bname = 'Internet Explorer';
    	$ub = "rv";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ :]+(?<version>[0-9.|a-zA-Z.]*)#';
    preg_match_all($pattern, $u_agent, $matches);
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    
    // Now that we have a version, convert Windows' versions to their actual deployment name
    if (($platform == 'windows') && ($version != '?'))
    {
        $strPlatform = 'Windows';
        
        // Attempt to grab the NT version out here
        $finds = array();
        if (preg_match('/win[\w ]+[nt ][\w.]+/', strtolower($_SERVER['HTTP_USER_AGENT']), $finds) == 1)
        {
            // Now grab the version itself
            $versionSet = array();
            preg_match('/[\w]+\.[\w]+/', $finds[0], $versionSet);
            $versionCompare = $versionSet[0];
        }
        
        // If we don't have a major AND a minor, we are going to skip this
        if (isset($versionCompare) && !empty($versionCompare))
        {
            switch(strval($versionCompare))
            {
                case '3.1':
                    $strPlatform = 'Windows NT 3.1 [Server]';
                    break;
                    
                case '3.5':
                    $strPlatform = 'Windows NT 3.5 [Server]';
                    break;
                
                case '3.51':
                    $strPlatform = 'Windows NT 3.51 [Server]';
                    break;
                    
                case '4.0':
                    $strPlatform = 'Windows NT 4.0';
                    break;
                    
                case '5.0':
                    $strPlatform = 'Windows 2000';
                    break;
                    
                case '5.1':
                    $strPlatform = 'Windows XP [32-Bit]';
                    break;
                    
                case '5.2':
                    $strPlatform = 'Windows XP [64-Bit]';
                    break;
                    
                case '6.0':
                    $strPlatform = 'Windows Vista';
                    break;
                    
                case '6.1':
                    $strPlatform = 'Windows 7';
                    break;
                    
                case '6.2':
                    $strPlatform = 'Windows 8';
                    break;
                    
                case '6.3':
                    $strPlatform = 'Windows 8.1';
                    break;
                
                case '10.0':
                    $strPlatform = 'Windows 10';
                    break;
                
                default:
                    $strPlatform = 'Windows [Unidentified]';
                    break;
            }
        }
    }
   
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern,
        'strPlatform' => $strPlatform
    );
}

$ua=getBrowser();

?>


<html>

<head>

<script type="text/javascript" src="swfobject.js"></script>

<script>
function GetBrowser(){
	var browser = "<?php echo($ua['name']) ?>";
	var cookies = CheckCookies();
	var dcookies = navigator.cookieEnabled;
	if(dcookies == true){
		dcookies = "Enabled";
	}else{
		dcookies = "Disabled";
	}

	var java = navigator.javaEnabled();
	
	if(java == true){
		java = "Enabled";
	}else{
		java = "Disabled";
	}
	
	var platform = <?php echo($ua['strPlatform'] != '') ? "\"{$ua['strPlatform']}\"" : 'navigator.platform'; ?>;
	var version = "<?php echo($ua['version']) ?>";
	var header = navigator.userAgent;

	var playerVersion = swfobject.getFlashPlayerVersion();
	var flashver = playerVersion.major + "." + playerVersion.minor + "." + playerVersion.release;

	document.getElementById('stoof').innerHTML = 'Please copy the information below:<br /><br />Browser Name: '+browser+'<br />Browser Version: '+version+'<br />Browser Platform: '+platform+'<br />Flash Version: '+flashver+'<br />Cookies (default): '+dcookies+'<br />Cookies (this domain): '+cookies+'<br />Javascript: '+java+'<br />User Agent: '+header;
}

function set_cookie ( name, value, exp_y, exp_m, exp_d, path, domain, secure ){
	var cookie_string = name + "=" + escape ( value );

	if ( exp_y ){
		var expires = new Date ( exp_y, exp_m, exp_d );
		cookie_string += "; expires=" + expires.toGMTString();
	}else{
		var expires = new Date ( 2110, 01, 01 );
		cookie_string += "; expires=" + expires.toGMTString();
	}

	if ( path ){
		cookie_string += "; path=" + escape ( path );
	}
	if ( domain ){
		cookie_string += "; domain=" + escape ( domain );
	}
	if ( secure ){
		cookie_string += "; secure";
	}
	document.cookie = cookie_string;
}

function get_cookie ( cookie_name ){
	var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );

	if ( results ){
		return ( unescape ( results[2] ) );
	}else{
		return null;
	}
}

function delete_cookie ( cookie_name ){
	var cookie_date = new Date ( );  // current date & time
	cookie_date.setTime ( cookie_date.getTime() - 1 );
	document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}

function CheckCookies(){
	set_cookie("test", "test");
	if(get_cookie("test")){
		delete_cookie("test");
		return "Enabled";
	}else{
		return "Disabled";
	}
}

function SendEmail(){
	var mess = document.getElementById("text").value;
	
}

</script>
</head>

<body>
<div id="stoof"><h1>Javascript is disabled!</h1><p>Please enable javascript and reload the page.</p></div>
<!-- <div id="morestoof" style="margin-top:25px;">
	Please describe your problem with as many details as possible below:<br />
	<form action="SendMail.php" method="post">
		<textarea rows="10" cols="75" id="text" name="text" title=""></textarea><br /><input type="Submit" value="Submit" />
		<input type="hidden" id="browser" name="browser" value="moo">
		<input type="hidden" id="version" name="browserver" value="moo">
		<input type="hidden" id="platform" name="browserplat" value="moo">
		<input type="hidden" id="cookies" name="cookies" value="moo">
		<input type="hidden" id="dcookies" name="dcookies" value="moo">
		<input type="hidden" id="agent" name="agent" value="moo">
		<input type="hidden" id="flash" name="flashver" value="moo">
		<input type="hidden" id="javascript" name="javascript" value="moo">
	</form>
</div> -->
</body>
<script>GetBrowser();</script>
</html>
