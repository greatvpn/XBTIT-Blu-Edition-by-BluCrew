<?php
$THIS_BASEPATH=dirname(__FILE__);
require_once("include/functions.php");
dbconn();
require_once (load_language("lang_login.php"));
require_once (load_language("lang_recover.php"));
require_once ("btemplate/bTemplate.php");

global $btit_settings, $USE_IMAGECODE, $CURUSER;

$sp = $_SERVER['SERVER_PORT']; $ss = $_SERVER['HTTPS']; if ( $sp =='443' || $ss == 'on' || $ss == '1') $p = 's';
$domain = 'http'.$p.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$domain = str_replace('/login_new.php', '', $domain);

/*if($btit_settings["fmhack_force_ssl"]=="enabled" && $CURUSER["force_ssl"]=="yes")
{
$BASEURL=str_replace("http","https",$BASEURL);
}*/

if ($BASEURL != $domain) {
 $currentFile = $_SERVER['REQUEST_URI']; preg_match("/[^\/]+$/",$currentFile,$matches);
 $filename = "/" . $matches[0];
 header ("Location: " . $BASEURL . $filename . "");
}

(isset($_POST["cr"]) && $_POST["cr"] == 1) ? $cr = (int)1:$cr = 0;
(isset($_POST["bo"]) && $_POST["bo"] >1) ? ($bo=(int)$_POST["bo"]):($bo=0);
(isSet($_POST["vu"]) && $_POST['vu'] == 1) ? $vu = (int)1:$vu = 0;

$logintpl = new bTemplate();

$logintpl->set("login.action", "index.php?page=login"); //example set value
$logintpl->set("recover.action", "index.php?page=recover&amp;act=takerecover"); //example set value
$logintpl->set("div_setting","");
$logintpl->set("title","");
$logintpl->set("error_message","");
$logintpl->set("singup_enabled",false,true);
$logintpl->set("year",date("Y"));
$logintpl->set("IMAGE_CODE",$language['IMAGE_CODE']);
$logintpl->set("SECURITY_CODE",$language['SECURITY_CODE']);

if($cr==1)
{
    $logintpl->set("div_setting","shake");
    $logintpl->set("title","Login Error");
    $logintpl->set("error_message","<p><span class='ui-icon ui-icon-alert' style='float:left;margin:0 7px 20px 0;'></span>Incorrect username/password</p>");
}

if($bo>1)
{
    $que = do_sqlquery("SELECT `u`.`booted`, `u`.`whybooted`, `u`.`addbooted` FROM {$TABLE_PREFIX}users u WHERE id={$bo} AND `u`.`booted`='YES'");

    if(sql_num_rows($que)==1)
    {
        $que_res = $que->fetch_assoc();
        $logintpl->set("div_setting","shake");
        $logintpl->set("title","You have been booted!");
        $logintpl->set("error_message",("<p><span class='ui-icon ui-icon-alert' style='float:left;margin:0 7px 20px 0;'></span>  <b>".$language["BOOTED"]." <br>".$language["BOOTEDUT"]." ".unesc($que_res['addbooted'])."<br>".$language["WHYBOOTED"]."&nbsp;&nbsp;".unesc($que_res['whybooted'])."</b></p>"));
    }
}
if($vu==1)
{
    $logintpl->set("div_setting","shake");
    $logintpl->set("title","User not Validated.");
    $logintpl->set("error_message","<p><span class='ui-icon ui-icon-alert' style='float:left;margin:0 7px 20px 0;'></span>Your account has not been validated, please check your account for an email from us.</p>");
}

//ImageCode
if($USE_IMAGECODE)
{
    if(extension_loaded('gd'))
    {
        $arr = gd_info();
        if($arr['FreeType Support'] == 1)
        {
            $p = new ocr_captcha();
            $logintpl->set("CAPTCHA", true, true);
            $logintpl->set("recover_captcha", $p->display_captcha(true));
            $private = $p->generate_private();
        }
        else
        {
            include ("$THIS_BASEPATH/include/security_code.php");
            $scode_index = rand(0, count($security_code) - 1);
            $scode = "<input type=\"hidden\" name=\"security_index\" value=\"$scode_index\" />\n";
            $scode .= $security_code[$scode_index]["question"];
            $logintpl->set("scode_question", $scode);
            $logintpl->set("CAPTCHA", false, true);
        }
    }
    else
    {
        include ("$THIS_BASEPATH/include/security_code.php");
        $scode_index = rand(0, count($security_code) - 1);
        $scode = "<input type=\"hidden\" name=\"security_index\" value=\"$scode_index\" />\n";
        $scode .= $security_code[$scode_index]["question"];
        $logintpl->set("scode_question", $scode);
        $logintpl->set("CAPTCHA", false, true);
    }
}
else
{
    include ("$THIS_BASEPATH/include/security_code.php");
    $scode_index = rand(0, count($security_code) - 1);
    $scode = "<input type=\"hidden\" name=\"security_index\" value=\"$scode_index\" />\n";
    $scode .= $security_code[$scode_index]["question"];
    $logintpl->set("scode_question", $scode);
    $logintpl->set("CAPTCHA", false, true);
}
//ImageCode

echo $logintpl->fetch(load_template("login_new.tpl"));
?>