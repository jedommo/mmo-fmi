<?php
error_reporting(E_ALL & ~E_WARNING);

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = $_POST['account'];
    $password = $_POST['password'];
    $SN = $_POST['SN'];
    $UDID = $_POST['UDID'];

    Gettoken($account, $password, $SN, $UDID);
}

function Gettoken($account, $Password, $SN, $UDID)
{
    // Your existing Gettoken function code

    $url = 'https://setup.icloud.com/setup/fmipauthenticate/' . $account;

    $post_data = '';
    $bacio = base64_encode($account . ':' . $Password);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: setup.icloud.com", "Accept: */*", "Authorization: Basic" . $bacio, "Proxy-Connection: keep-alive", "X-MMe-Country: EC", "Accept-Language: es-es", "Content-Type: text/plist", "Connection: keep-alive"));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Ajustes/1.0 CFNetwork/711.1.16 Darwin/14.0.0");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $xml_response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_message = curl_error($ch);
        $error_no = curl_errno($ch);

        echo "error_message: " . $error_message . "<br>";
        echo "error_no: " . $error_no . "<br>";
    }

    curl_close($ch);

    $response = $xml_response;

    $ds = explode("<key>dsid</key>", $response)[1];
    $dsi = explode("<string>", $ds)[1];
    $dsid = explode("</string>", $dsi)[0];
    $me = explode("<key>mmeFMIPWipeToken</key>", $response)[1];
    $mme = explode("<string>", $me)[1];
    $mmeFMIPWipeToken = explode("</string>", $mme)[0];
    file_put_contents('dsid', $dsid);
    file_put_contents('token', $mmeFMIPWipeToken);

    Remove($dsid, $mmeFMIPWipeToken, $SN, $UDID);
}

function Remove($dsid, $mmeFMIPWipeToken, $SN, $UDID)
{
    $url = "https://p33-fmip.icloud.com/fmipservice/findme/" . $dsid . "/" . $UDID . "/unregisterV2";

    $post_data = '{
    "serialNumber": "' . $SN . '",
    "deviceContext": {
        "deviceTS": "2023-10-01T20:37:17.880Z"
    },
    "deviceInfo": {
        "productType": "iPhone10,2",
        "udid": "' . $UDID . '",
        "fmipDisableReason": 1,
        "buildVersion": "17A878",
        "productVersion": "16.6.1"
    }
}';

    $bac = base64_encode($dsid . ':' . $mmeFMIPWipeToken);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: p33-fmip.icloud.com", "Accept-Language: es-es", "X-Apple-PrsId: " . $dsid, "Accept: */*", "Content-Type: application/json", "X-Apple-Find-API-Ver: 6.0", "X-Apple-I-MD-RINFO: 17106176", "Connection: keep-alive", "Authorization: Basic " . $bac, "Content-Length: " . strlen($post_data), "X-Apple-Realm-Support: 1.0"));

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: FMDClient/6.0 iPhone9,3/13G36");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $xml_response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_message = curl_error($ch);
        $error_no = curl_errno($ch);

        echo "error_message: " . $error_message . "<br>";
        echo "error_no: " . $error_no . "<br>";
    }

    curl_close($ch);
    $response = $xml_response;
    preg_match('/HTTP\/\d\.\d\s+(\d+)\s+/', $response, $matches);
    $status = $matches[1];

    if ($status === '200') {
        $a = '<br><h2 style="color:green;"><center>Find My iPhone: OFF ✅ <center></h2>';
    } else {
        $a = '<br><h2 style="color:red;"><center>Token Expier <center></h2>';
    }
     echo $a;
}
?>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/common.js" type="application/javascript"></script>
    <link href="css/common.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Tamma+2:wght@500&display=swap" rel="stylesheet">
    <style>
#check {
    font-family: 'Baloo Tamma 2', cursive;
  background-color: black; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}
#clear {
    font-family: 'Baloo Tamma 2', cursive;
    background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}

        .error{
            color:red;
        }
        input[type='text']{
            width:60%;
            padding:10px;
        }
    
  #formz {
    font-family: 'Baloo Tamma 2', cursive;
    width: 70%;
  border-radius: 0px;
  background-color: #f2f2f2;
  padding: 20px;
}
input[type=text], select {
    font-family: 'Baloo Tamma 2', cursive;
  width: 70%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: 'Baloo Tamma 2', cursive;
}

/* Style the side navigation */
.sidenav {
  height: 100%;
  width: 200px;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: #111;
  overflow-x: hidden;
}


/* Side navigation links */
.sidenav a {
  color: white;
  padding: 16px;
  text-decoration: none;
  display: block;
}

/* Change color on hover */
.sidenav a:hover {
  background-color: #ddd;
  color: black;
}

/* Style the content */
.content {
  margin-left: 200px;
  padding-left: 20px;
}

</style>
</head>
    <title>iCloud Token</title>
    
    <center><h2 style="color:white;">REMOVE ICLOUD OPEN MENU</h2>
<center></head>
<body>
    <form method="post" action="">
    <label for="account"></label> <br>
    <input type="text" name="account" id="account" placeholder="APPLE ID:" class="validate">
    <br>
    <label for="text"></label><br>
    <input type="text" name="password" id="password" placeholder="Token:" class="">
    <br>
    <label for="SN"></label><br>
    <input type="text" name="SN" id="SN" placeholder="Serial Number:" class="">
    <br>
    <label for=""></label><br>
    <input type="text" name="UDID" id="UDID" placeholder="UDID:" class="">
    <br><br>

        <input type="submit" value="FMI OFF">
    </form>
    <center><h2 style="color:Green;">Server Online</h2>

    <?php
    
    // Display the response if it's available
    if (isset($a)) {
        
    echo "Response: $a";
    }
    ?>
</div>
</div>
<style type="text/css">
body::after {
  background: url('img/Untitleddesign(4).jpg');
  content: "";
  opacity: 0.9;
  position: absolute;
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  z-index: -1;   
}
.logo img {
    width: 60%;
    margin: -55px 0 0 61px;
}
.logo {
    width: 30%;
    float: left;
}
.loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('img/Curve-Loading22.gif') 50% 50% no-repeat rgb(249,249,249);
    opacity: 1.8;
}
#social-buttons {
  top: 17px;
    text-align: center;
  position: relative;
  
}
svg:not(:root).svg-inline--fa {
    overflow: visible;
}


.svg-inline--fa.fa-w-16 {
    width: 1em;
}
svg:not(:root) {
    overflow: hidden;
}

.svg-inline--fa {
    display: inline-block;
    font-size: inherit;
    height: 1em;
    overflow: visible;
    vertical-align: -.125em;
}
#checkerWait {
    width: 92%;
    font-size: 100%;
    display: block;
}

@media (max-width: 450px){
.form-wrapper {
    width: 100%!important;
}
.logo {
    text-align: center!important;
    width: 100%!important;
    float: none!important;
}
div#checker_area {
    margin-top: 0px !important;
}
.logo img {
    width: 60%;
    margin: 0px!important;
}
#checker_area {
    width: 100% !important;
}
}
@media (max-width: 979px){
    div#checker_area {
    margin-top: 0px !important;
}
.logo img {
    width: 22% !important;
    margin: 0px!important;
}
.logo {
    text-align: center!important;
    width: 100%!important;
    float: unset!important;
}
}
.f_copyright {
    display: grid;
    text-align: center;
    margin-top: 17px;
    color: #fff;
} 
#deviceIcon,.form-wrapper{overflow:hidden;border-radius:15px}#checker_area{width:580px;margin:0 auto;text-align:center;padding:10px 0;min-height:250px;box-shadow:0 0 18px #888;border-radius:15px}.cf:after,.cf:before{content:"";display:table}.cf:after{clear:both}.cf{zoom:1}.form-wrapper{width:450px;padding:12px 15px 6px;margin:15px auto 0;background:#444;background:rgba(0,0,0,.15);box-shadow:0px 1px 1px 0px rgba(0,0,0,.4), 0 1px 0 rgba(255,255,255,.2)}#deviceOverview{border-radius:15px 15px 0 0}#deviceIcon{float:left;display:inline-block;position:relative;vertical-align:middle;width:70px;height:70px;background:#fff;margin-bottom:7px;box-shadow:0 0 20px 0 rgba(0,0,0,.4),0 1px 0 rgba(255,255,255,.2)}.serviceItm{margin:inherit!important;border-radius:inherit!important;border:0!important;padding:12px 15px 6px!important}.serviceItm:active,.serviceItm:hover{background:rgba(0,0,0,.23);color:#fff!important}.lastItm{border-radius:0 0 15px 15px!important}.form-wrapper input{width:230px;height:40px;padding:10px 5px;float:left;font-family:'Avant Garde',Avantgarde,'Century Gothic',CenturyGothic,AppleGothic,sans-serif;border:0;background:#eee;border-radius:10px 0 0 10px;text-align:inherit}.form-wrapper input:focus{outline:0;background:#fff;box-shadow:0 0 2px rgba(0,0,0,.8) inset}.form-wrapper input::-webkit-input-placeholder{color:#999;font-weight:400}.form-wrapper input:-moz-placeholder{color:#999;font-weight:400;font-style:italic}.form-wrapper input:-ms-input-placeholder{color:#999;font-weight:400;font-style:italic}.form-wrapper button{overflow:visible;position:relative;float:right;border:0;padding:0;cursor:pointer;height:40px;width:110px;font:700 15px/40px 'lucida sans','trebuchet MS',Tahoma;color:#fff;text-transform:uppercase;text-shadow:0 -1px 0 rgba(0,0,0,.3)}.form-wrapper button:hover{background:#e54040}#feedback,#help{border-radius:0 10px 10px 0;background:#eab310;border:1px solid #eee}.form-wrapper button:active,.form-wrapper button:focus{outline:0}#submit_imei{border-radius:0;background:#0678ff;border:1px solid #eee}#submit_imei:active,#submit_imei:focus,#submit_imei:hover{background:#1477c3}#feedback:active,#feedback:hover,#help:active,#help:hover{background:#dc8e21}#feedback{width:50%}#startOver{border:1px solid #fff;width:50%;border-radius:10px 0 0 10px;background:#4dd052}.form-wrapper button::-moz-focus-inner{border:0;padding:0}pre{text-align:left}.inner_checker{min-height:220px}.message_area{margin:10px 0;width:100%;text-align:center;padding:10px 5px 0px 5px;display:none}#testIMEI{margin-bottom:0px}.label2{margin-bottom:10px}#checkerNotices{padding:20px 10px 0;color:#fff}#testIMEI{margin-top:20px}#checker_area h3{text-align:center;font-family:'Avant Garde',Avantgarde,'Century Gothic',CenturyGothic,AppleGothic,sans-serif;font-size:24px;font-style:normal;font-variant:normal;font-weight:500;line-height:26px}.label,.label3{font-weight:700;white-space:nowrap}.imei_left{display:inline-block;width:178px}.imei_right{display:inline;width:80px;vertical-align:text-top}#loader{position:relative;width:100%;height:200px;background:#333;display:none;opacity:.8;top:-230px;padding-top:63px}.label,.label2,.label3{color:#fff;text-align:center;vertical-align:baseline;background-color:#5cb85c}.label3{display:inline;padding:.2em .6em .3em;font-size:75%;line-height:1;border-radius:.25em}.label2{margin-left:5px;margin-right:5px;display:-webkit-inline-box;padding:.4em .6em .6em;font-size:80%;line-height:1.3;border-radius:.5em;border:1px solid #fff}.label{display:inline;padding:.2em .5em;font-size:66%;line-height:1;border-radius:.7em}.label-success{background-color:#5fd05f}.label-warning{background-color:#eab310}.label-info{background-color:#357dc5}.label-danger{background-color:#d9534f}.btn{display:inline-block;margin-bottom:0!important;font-weight:400!important;text-align:center!important;-ms-touch-action:manipulation!important;touch-action:manipulation!important;cursor:pointer!important;background-image:none!important;border:1px solid transparent!important;white-space:nowrap!important;padding:6px 12px!important;font-size:14px!important;line-height:1.42857143!important;border-radius:4px!important;-webkit-user-select:none!important;-moz-user-select:none!important;-ms-user-select:none!important;user-select:none!important}.btn-success{color:#fff!important;background-color:#60d660!important;border-color:#52aa52!important}.btn-success.focus,.btn-success:focus,.btn-success:hover{color:#fff!important;background-color:#51c15e!important;border-color:#4f9b4f!important}.btn-warning{color:#fff!important;background-color:#f0ad4e!important;border-color:#de8e1b!important}.btn-warning.focus,.btn-warning:focus{color:#fff;background-color:#ec971f;border-color:#985f0d}.btn-warning:hover{color:#fff;background-color:#ec971f;border-color:#d58512}.btn-danger{color:#fff;background-color:#d9534f;border-color:#d43f3a}.btn-danger.focus,.btn-danger:focus{color:#fff;background-color:#c9302c;border-color:#761c19}.btn-danger:hover{color:#fff;background-color:#c9302c;border-color:#ac2925}.swal2-image{max-height:120px!important;cursor:pointer}.fb_iframe_widget{display:inline-flex!important}.labelSup{display:-webkit-inline-box;padding:.6em .6em .7em;font-size:70%;color:#fff;text-align:center;vertical-align:baseline;border-radius:.5em;background-color:#f0ad4e}#startOver:active,#startOver:hover{background:#43c348}.main_theme{background:#0678ff;background:-moz-radial-gradient(center,ellipse cover,#72b0de 0%,#0678ff 100%);background:-webkit-radial-gradient(center,ellipse cover,#72b0de 0%,#0678ff 100%);background: radial-gradient(ellipse at center, #72b0de 0%,#0678ff 100%)
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#72b0de', endColorstr='#0678ff',GradientType=1 )}.header{background:#0678ff;background:-moz-radial-gradient(center,ellipse cover,#239df7 0%,#0678ff 100%);background:-webkit-radial-gradient(center,ellipse cover,#239df7 0%,#0678ff 100%);background: radial-gradient(ellipse at center, #239df7 0%,#0678ff 100%)
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#239df7', endColorstr='#0678ff',GradientType=1 )}#imei{border:1px solid #eee}.FB-modal{max-height:90%;overflow-y:auto!important}.fb-comments{width:100%!important}.hideInfo{border-radius:.4em;color:#000;background-color:#000;vertical-align:baseline}.progress{background-color:transparent!important;padding-left:20px!important;padding-right:20px!important}#submit_imei:active:before,#submit_imei:focus:before,#submit_imei:hover:before{border-right-color:#1477c3;transition:all .3s}#submit_imei:before{content:'';position:absolute;border-width:8px 8px 8px 0;border-style:solid solid solid none;border-color:transparent #0678ff;top:12px;left:-6px;transition:all .3s}.swal2-modal .swal2-checkbox input{vertical-align:baseline!important}</style>

</div>
</div>
    </div><br><br>
<footer>
    <div id="social-buttons">

<a target="_blank" href="https://api.whatsapp.com/send?phone=9647713728754&text=&source=&data=" rel="me"><img title="whatsapp" src="img/whatsapp-32.png" alt="whatsapp Button">
</a>
<a target="_blank" href="https://t.me/DekanUnlock" rel="me"><img title="telegram" src="img/telegram-32.png" alt="telegram Button">
</a>
<a target="_blank" href="https://server.dekan-unlock.com" rel="me"><img title="Server" src="img/website32.png" alt="Server Button">
</a>
<a target="_blank" href="https://server.dekan-unlock.com/register" rel="me"><img title="Register" src="img/register-32.png" alt="Register Button">
</a>
<a target="_blank" href="https://server.dekan-unlock.com/main/lgin" rel="me"><img title="Login" src="img/login-32.png" alt="Login Button">
</a>

</div>
<div class="f_copyright">
<br><br>

<span>©2019-2023 DEKAN-UNLOCK TM All right reserved</span>
<br>

</body>
</html>
