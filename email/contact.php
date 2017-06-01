<?php

require_once("../libs/jsonwrapper/jsonwrapper.php");

if(!function_exists("sanitize")) {

    function sanitize($string) {
        return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
    }

}

function get_value($name) {
    if(@array_key_exists($name, $_GET)) {
        return sanitize(urldecode($_GET[$name]));
    }
    return null;
}

$name = get_value("name");
$company = get_value("company");
$phone_number = get_value("phone_number");
$email = get_value("email");
$question = nl2br(get_value("question"));

$response = array(
    'text' => 'error'
);

if(empty($name)) {
    $response['text'] = "Leeg naam veld";
    die(json_encode($response));
}

if(empty($company)) {
    $response['text'] = "Leeg bedrijfs veld";
    die(json_encode($response));
}

if(empty($phone_number)) {
    $response['text'] = "Leeg telefoon nummer veld";
    die(json_encode($response));
}

if(!preg_match("/^(?!.{255,})(?!.{65,}@)([!#-'*+\\/-9=?^-~-]+)(?>\\.(?1))*@(?!.*[^.]{64,})(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?\\.){1,126}[a-z]{2,6}$/iD", $email)) {
    $response['text'] = "Fout geschreven email";
    die(json_encode($response));
}

require_once("../captcha/captcha.php");

if(use_captcha(1, $email, $_SERVER['REMOTE_ADDR'], true)) {
    if(!verify_captcha($_GET['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'])) {
        $response['text'] = "Captcha is niet correct";
        die(json_encode($response));
    }
}

require 'PHPMailerAutoload.php';

$html = <<<HTML
<html>
  <body style="margin:0">
    <table style="background-color:#eeeeee" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EEEEEE">
      <tbody>
        <tr>
          <td style="min-width:630px" align="center" valign="top">
            <table width="630" border="0" cellspacing="0" cellpadding="0" align="center">
              <tbody>
                <tr>
                  <td style="color:#808080;font-size:13px;font-family:Arial,Helvetica,sans-serif" align="center" valign="top" width="100%">
                    <table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td height="20"></td>
                        </tr>
                      </tbody>
                    </table><span class="im">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                          <tr>
                            <td style="color:#808080;font-size:13px;font-family:Arial,Helvetica,sans-serif" align="left" valign="top" width="350" height="43"><table><tbody><tr><td height="1"></td></tr></tbody></table><span>Wilt u contact met ons opnemen?</span></td>
                            <td style="color:#808080;font-size:13px;font-family:Arial,Helvetica,sans-serif" align="left" valign="top" width="169" height="43"></td>
                            <td valign="top" width="111">
                              <td height="20" valign="top">
                                <table width="111" border="0" cellspacing="0" cellpadding="0">
                                  <tbody>
                                    <tr>
                                      <td valign="top"><a href="https://twitter.com/kpn" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/twitter.jpg" alt="Twitter" width="22" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                      <td valign="top"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="7" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                      <td valign="top"><a href="https://www.facebook.com/kpn" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/facebook.jpg" alt="Facebook" width="23" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                      <td valign="top"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="5" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                      <td valign="top"><a href="https://www.linkedin.com/company/kpn" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/linkedin.jpg" alt="LinkedIn" width="22" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                      <td valign="top"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="7" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                      <td valign="top"><a href="https://www.youtube.com/user/KPN" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/youtube.jpg" alt="YouTube" width="22" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                      <td valign="top"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="3" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </td>
                          </tr>
                        </tbody>
                      </table></span>
                  </td>
                </tr>
                <tr>
                  <td align="left" width="100%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
                      <tbody>
                        <tr>
                          <td align="left" valign="top" width="30"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="30" height="100" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                          <td align="left" valign="top" width="133"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/kpn_logo.jpg" alt="kpn" width="133" height="100" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                          <td align="left" valign="top" width="100%"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td align="left" width="100%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
                      <tbody>
                        <tr>
                          <td style="width:630px" align="center" valign="top" width="630"><img style="display:block;border:0" src="http://kpnhotspots.welikemilk.nl/mailings/1/kpn-wifi-hotspots-email-header-contactformulier.png" alt="Slimmer om thuis vast te bellen i.p.v. mobiel" width="630" height="210" border="0" tabindex="0" class="CToWUd a6T"/>
                            <div dir="ltr" style="opacity: 0.01; left: 582px; top: 2498.42px;" class="a6S">
                              <div id=":aaj" role="button" tabindex="0" aria-label="Download attachment " data-tooltip-class="a1V" data-tooltip="Download" class="T-I J-J5-Ji aQv T-I-ax7 L3 a5q">
                                <div class="aSK J-J5-Ji aYr"></div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top" width="100%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#EEEEEE">
                      <tbody>
                        <tr bgcolor="#EEEEEE">
                          <td align="center" valign="top" bgcolor="#EEEEEE"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="630" height="20" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td align="left" valign="top" width="100%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
                      <tbody>
                        <tr>
                          <td valign="top" width="100%">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
                              <tbody>
                                <tr bgcolor="#FFFFFF">
                                  <td align="center" bgcolor="#FFFFFF"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="630" height="15" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td align="left" valign="top" width="100%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
                      <tbody>
                        <tr>
                          <td align="left" valign="top" width="100%">
                            <table width="570" border="0" cellspacing="0" cellpadding="0" align="center">
                              <tbody>
                                <tr>
                                  <td align="left" valign="top">
                                    <table style="font-family:Arial,Helvetica,sans-serif;color:#000000;font-size:14px;line-height:21px" width="570" border="0" cellspacing="0" cellpadding="0" align="right">
                                      <tbody>
                                        <tr>
                                          <td style="font-family:Arial,Helvetica,sans-serif;color:#000000;font-size:14px;line-height:21px" align="left" valign="top" width="496"><span style="font-family:Arial,Helvetica,sans-serif;color:#000000;font-size:14px;line-height:21px"><span class="im"><br/>Beste $name,<br/><br/>Bedankt voor uw interesse in KPN WiFi HotSpots!<br/><br/>U heeft deze contactgegevens aan ons doorgegeven:<br/><br/>Bedrijf: $company<br/>Telefoonnummer: <a href="tel:$phone_number">$phone_number</a><br/>E-mailadres:&nbsp;<a href="mailto:$email">$email</a><br/>Vraag:&nbsp;$question<br/><br/>We reageren zo snel mogelijk op uw vraag. Mocht u eerder contact met ons willen, dan staan we u graag te woord op 0800 0414.<br/><br/>Met vriendelijke groet,<br/><br/>Robert Bakker<br/>Commercieel Manager<br/>KPN WiFi HotSpots</span></span></td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top" width="100%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
                      <tbody>
                        <tr bgcolor="#FFFFFF">
                          <td align="center" bgcolor="#FFFFFF"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="630" height="15" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top" width="100%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
                      <tbody>
                        <tr bgcolor="#FFFFFF">
                          <td align="center" bgcolor="#FFFFFF"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="630" height="15" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr bgcolor="#EEEEEE">
                  <td align="center" valign="top" bgcolor="#EEEEEE"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="630" height="30" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                </tr>
                <tr>
                  <td style="color:#808080;font-size:11px;font-family:Verdana,Arial,Helvetica,sans-serif" align="center" valign="top" width="100%" height="43">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="left" valign="top" width="100%">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td align="left" valign="top">
                                    <table width="223" border="0" cellspacing="0" cellpadding="0" align="left">
                                      <tbody>
                                        <tr>
                                          <td valign="top">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                              <tbody>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" align="left" width="114" height="13"><a href="https://www.kpn.com/algemeen/alle-voorwaarden.htm" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/conditions_text.jpg" alt="Alle voorwaarden" width="150" height="15" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                                  <td width="19" height="10"><img src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="19" height="10" class="CToWUd"/></td>
                                                </tr>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" width="19" height="11"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="19" height="11" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                                </tr>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" align="left" width="114" height="13"><a href="https://www.kpn.com/algemeen/missie-en-privacy-statement.htm" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/privacy_text.jpg" alt="Privacy" width="150" height="15" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                                </tr>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" width="19" height="11"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="19" height="11" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <table width="10" border="0" cellspacing="0" cellpadding="0" align="left">
                                      <tbody>
                                        <tr>
                                          <td style="font-size:1px;line-height:1px" align="left" valign="top" width="100%"><img src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="10" height="20" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <table width="223" border="0" cellspacing="0" cellpadding="0" align="left">
                                      <tbody>
                                        <tr>
                                          <td valign="top">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                              <tbody>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" align="left" width="112" height="13"><a href="https://www.kpn.com/prive/klantenservice/veilig-internetten.htm" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/safe_internetting_text.jpg" alt="Veilig internetten" width="120" height="15" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                                  <td width="19" height="10"><img src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="19" height="10" class="CToWUd"/></td>
                                                </tr>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" width="19" height="11"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="19" height="11" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                                </tr>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" align="left" width="19" height="13"><a href="https://www.kpn.com/prive/klantenservice.htm" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/contact_text.jpg" alt="Contact" width="120" height="15" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                                </tr>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" align="left" width="112" height="13"></td>
                                                </tr>
                                                <tr>
                                                  <td style="font-size:1px;line-height:1px" width="19" height="11"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="19" height="11" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <table width="10" border="0" cellspacing="0" cellpadding="0" align="left">
                                      <tbody>
                                        <tr>
                                          <td style="font-size:1px;line-height:1px" align="left" valign="top" width="100%"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="10" height="20" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <table width="111" border="0" cellspacing="0" cellpadding="0" align="right">
                                      <tbody>
                                        <tr>
                                          <td valign="top">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                              <tbody>
                                                <tr>
                                                  <td align="right" valign="top">
                                                    <table width="111" border="0" cellspacing="0" cellpadding="0">
                                                      <tbody>
                                                        <tr>
                                                          <td valign="top"><a href="https://twitter.com/kpn" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/twitter.jpg" alt="Twitter" width="22" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                                          <td valign="top"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="7" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                                          <td><a href="https://www.facebook.com/kpn" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/facebook.jpg" alt="Facebook" width="23" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                                          <td valign="top"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="5" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                                          <td valign="top"><a href="https://www.linkedin.com/company/kpn" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/linkedin.jpg" alt="LinkedIn" width="22" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                                          <td valign="top"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="7" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                                          <td valign="top"><a href="https://www.youtube.com/user/KPN" target="_blank"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/youtube.jpg" alt="YouTube" width="22" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></a></td>
                                                          <td valign="top"><img style="display:block;border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="3" height="22" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
                                                        </tr>
                                                      </tbody>
                                                    </table>
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr bgcolor="#EEEEEE">
          <td align="center"><img style="border:none;outline:none;text-decoration:none" src="http://kpnhotspots.welikemilk.nl/mailings/1/spacer.gif" alt="" width="630" height="30" border="0" hspace="0" vspace="0" class="CToWUd"/></td>
        </tr>
      </tbody>
    </table>
  </body>
</html>

HTML;

$plain = 'Beste '.$name.',

Bedankt voor uw interesse in KPN WiFi HotSpots!

U heeft deze contactgegevens aan ons doorgegeven:

Bedrijf: '.$company.'
Telefoonnummer: '.$phone_number.'
E-mailadres: '.$email.'
Vraag: '.$question.'

We reageren zo snel mogelijk op uw vraag. Mocht u eerder contact met ons willen, dan staan we u graag te woord op 0800 0414.

Met vriendelijke groet,

Robert Bakker
Commercieel Manager
KPN WiFi HotSpots';

$mail = new PHPMailer;

$mail->From = 'verkoop-kpnhotspots@kpn.com';
$mail->FromName = 'KPN WiFi HotSpots';
$mail->addAddress($email, $name);
$mail->addReplyTo('verkoop-kpnhotspots@kpn.com', 'KPN WiFi HotSpots');
$mail->CharSet = 'UTF-8';
$mail->IsSMTP();
$mail->Host = "localhost";
$mail->isHTML(true);

$mail->Subject = 'Bevestiging van uw vraag aan KPN WiFi HotSpots';
$mail->Body = $html;
$mail->AltBody = $plain;

$mail->send();

$html = <<<HTML
<html>
  <body>
    <table style="font-family: 'Lucida Grande', 'Helvetica Neue', Arial; border-spacing: 15px 5px;">
      <tr>
        <td><strong>Naam</strong></td>
        <td>$name</td>
      </tr>
      <tr>
        <td><strong>Bedrijf</strong></td>
        <td>$company</td>
      </tr>
      <tr>
        <td><strong>Telefoon</strong></td>
        <td>$phone_number</td>
      </tr>
      <tr>
        <td><strong>E-mail</strong></td>
        <td>$email</td>
      </tr>
      <tr>
        <td valign="top"><strong>Mijn vraag</strong></td>
        <td>$question</td>
      </tr>
      </tr>
    </table>
  </body>
</html>
HTML;

//$plain = 'Dhr. '.$name.' van '.$company.' wilt dat u contact opneemt.';

$mail = new PHPMailer;

$mail->From = $email;
$mail->FromName = $name;
$mail->addAddress("verkoop-kpnhotspots@kpn.com", "KPN WiFi HotSpots");
$mail->addAddress("kpnhotspotvandezaak@gmail.com", "KPN WiFi HotSpots");
$mail->addReplyTo($email, $name);
$mail->CharSet = 'UTF-8';
$mail->IsSMTP();
$mail->Host = "localhost";
$mail->isHTML(true);

$mail->Subject = 'Inzending via contactformulier';
$mail->Body = $html;
//$mail->AltBody = $plain;

if(!$mail->send()) {
    $response['text'] = "Kon niet verzonden worden";
} else {
    $response['text'] = "Verzonden";
}

print json_encode($response);
