<?php

if(!function_exists("boolval")) {

    function boolval($value) {
        if(empty($value)) {
            return false;
        }
        return (bool) $value;
    }

}

if(!function_exists("sanitize")) {

    function sanitize($string) {
        return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
    }

}

$contact_fields = array(

    'company' => array(
        'required' => true,
        'name' => 'Bedrijfsnaam'
    ),

    'gender' => array(
        'required' => true,
        'options' => array("dhr", "mevr"),
        'name' => 'Aanhef',
        'pretty' => array("Dhr.", "Mevr.")
    ),

    'first_name' => array(
        'required' => true,
        'name' => 'Voornaam'
    ),

    'last_name' => array(
        'required' => true,
        'name' => 'Achternaam'
    ),

    'postal_code' => array(
        'required' => true,
        'regex' => '/^(\d)(\d)(\d)(\d)(\s?)([a-zA-Z])([a-zA-Z])$/',
        'name' => 'Postcode',
        'uppercase' => true
    ),

    'house_number' => array(
        'required' => true,
        'regex' => '/^[0-9]+$/',
        'name' => 'Huisnummer'
    ),

    'number_suffix' => array(
        'optional' => true,
        'regex' => '/^[a-zA-Z0-9]*$/',
        'name' => 'Toevoeging'
    ),

    'email' => array(
        'required' => true,
        'regex' => "/^(?!.{255,})(?!.{65,}@)([!#-'*+\\/-9=?^-~-]+)(?>\\.(?1))*@(?!.*[^.]{64,})(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?\\.){1,126}[a-z]{2,6}$/iD",
        'name' => 'E-mailadres'
    ),

    'phone_number' => array(
        'required' => true,
        'name' => 'Telefoonummer'
    ),

    'invoice_digital_or_printed' => array(
        'required' => true,
        'options' => array("email", "print"),
        'name' => 'Factuur type',
        'pretty' => array("Email", "Print")
    ),

    'iban' => array(
        'required' => true,
        'name' => 'IBAN'
    )

);

$invoice_fields = array(

    'postal_code_invoice' => array(
        'required' => true,
        'name' => 'Postcode',
        'uppercase' => true
    ),

    'house_number_invoice' => array(
        'required' => true,
        'name' => 'Huisnummer'
    ),

    'number_suffix_invoice' => array(
        'optional' => true,
        'name' => 'Toevoeging'
    ),

    'attn' => array(
        'required' => true,
        'name' => 'T.a.v.'
    ),

    'email_invoice' => array(
        'required' => true,
        'name' => 'E-mailadres'
    ),

    'reference' => array(
        'optional' => true,
        'name' => 'Referentie'
    ),

);

function post_value($name, $slashes = false) {
    if(@array_key_exists($name, $_POST)) {
        return $slashes ? addslashes(sanitize($_POST[$name])) : sanitize($_POST[$name]);
    }
    return null;
}

function validate($fields) {

    foreach($fields as $name => $field) {

        $value = post_value($name);

        if(boolval($field['required']) && empty($value)) {
            return false;
        }

        if(@array_key_exists("regex", $field) && !preg_match($field['regex'], $value)) {
            return false;
        }

        if(@array_key_exists("validate", $field) && !filter_var($value, $field['validate'])) {
            return false;
        }

        if(@array_key_exists("options", $field)) {

            $found = false;

            foreach($field['options'] as $option) {
                if($option == $value) {
                    $found = true;
                    break;
                }
            }

            if(!$found) {
                return false;
            }

        }

    }

    return true;

}

function return_order() {
    @header("Location: ../bestellen.html");
    exit;
}

function forward_thanks() {
    @header("Location: ../bedankt.html");
    exit;
}

function append_field($key, $field, $plain = false, $customer = false) {

    if(!@array_key_exists("name", $field)) {
        return '';
    }

    $value = post_value($key);

    if(@array_key_exists("optional", $field) && empty($value)) {
        return '';
    }

    if(@array_key_exists("pretty", $field)) {

        $index = 0;

        foreach($field['options'] as $option) {
            $index++;
            if($option == $value) {
                break;
            }
        }

        $value = $field['pretty'][$index - 1];

    }

    if(boolval($field['uppercase'])) {
        $value = strtoupper($value);
    }

    if($customer) {
        return !$plain ? '<tr>
        <td width="120">'.$field['name'].'</td>
        <td>'.$value.'</td>
    </tr>' : $field['name'].': '.$value.PHP_EOL;
    } else {
        return !$plain ? '<tr>
        <td><strong>'.$field['name'].'</strong></td>
        <td>'.$value.'</td>
    </tr>' : $field['name'].': '.$value.PHP_EOL;
    }

}

require_once("../captcha/captcha.php");

if(use_captcha(2, $_POST['email'], $_SERVER['REMOTE_ADDR'], true)) {
    if(!verify_captcha($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'])) {
        return_order();
    }
}

if(@boolval($_POST['agreement'])) {

    if (!validate($contact_fields)) {
        return_order();
    }

    if (@array_key_exists("factuur", $_POST)) {
        if (!validate($invoice_fields)) {
            return_order();
        }
    }

    require_once('../email/PHPMailerAutoload.php');

        $html = <<<HTML
    <html>
      <body>
        <table style="font-family: 'Lucida Grande', 'Helvetica Neue', Arial; border-spacing: 15px 5px;">
          <tr>
            <td colspan="2">
              <table>
                <tr>
                  <td height="10"></td>
                </tr>
              </table>
              <h2 style="font-family: 'Lucida Grande', 'Helvetica Neue', Arial; margin: 0 0 15px;">Contactgegevens</h2>
            </td>
          </tr>
HTML;

        foreach($contact_fields as $key => $field) {
            $html .= append_field($key, $field);
        }

    if(@array_key_exists("factuur", $_POST)) {

        $html .= <<<HTML
            <tr>
            <td>
              <table>
                <tr>
                  <td height="10"></td>
                </tr>
              </table>
              <h2 style="font-family: 'Lucida Grande', 'Helvetica Neue', Arial; margin: 0 0 15px;">Factuurgegevens</h2>
            </td>
          </tr>
HTML;

        foreach($invoice_fields as $key => $field) {
            $html .= append_field($key, $field);
        }

    }

        $html .= <<<HTML
    </table>
      </body>
    </html>
HTML;

        $plain = "#Contactgegevens".PHP_EOL;

        foreach($contact_fields as $key => $field) {
            $plain .= append_field($key, $field, true);
        }

        $plain .= PHP_EOL."#Factuurgegevens".PHP_EOL;

        foreach($invoice_fields as $key => $field) {
            $plain .= append_field($key, $field, true);
        }

        $mail = new PHPMailer;

        $mail->From = post_value("email", true);
        $mail->FromName = post_value("first_name", true)." ".post_value("last_name", true);
        $mail->addAddress("verkoop-kpnhotspots@kpn.com", "KPN WiFi HotSpots");
        $mail->addAddress("kpnhotspotvandezaak@gmail.com", "KPN WiFi HotSpots");
        $mail->addReplyTo($mail->From, $mail->FromName);
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->Host = "localhost";
        $mail->isHTML(true);

        $mail->Subject = 'Inzending via bestelformulier';
        $mail->Body = $html;
        $mail->AltBody = $plain;

        if(!$mail->send()) {
            return_order();
        }


    $first_name = post_value("first_name");
    $last_name = post_value("last_name");
    $email = post_value("email");
    $contact_details = '<br/><table style="font-family: Arial,Helvetica,sans-serif;color: #000000;font-size: 14px;line-height: 21px;border-spacing: 0;">';

    foreach($contact_fields as $key => $field) {
        $contact_details .= append_field($key, $field, false, true);
    }

    $contact_details .= '</table>';

    if(@array_key_exists("factuur", $_POST)) {

        $invoice_details = '<br/><strong>Uw facturatiegegevens:</strong><br/><br/><table style="font-family: Arial,Helvetica,sans-serif;color: #000000;font-size: 14px;line-height: 21px;border-spacing: 0;">';

        foreach($invoice_fields as $key => $field) {
            $invoice_details .= append_field($key, $field, false, true);
        }

        $invoice_details .= '</table>';

    }

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
                          <td style="width:630px" align="center" valign="top" width="630"><img style="display:block;border:0" src="http://kpnhotspots.welikemilk.nl/mailings/1/kpn-wifi-hotspots-email-header-bestelformulier.png" width="630" height="210" border="0" tabindex="0" class="CToWUd a6T"/>
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
                                          <td style="font-family:Arial,Helvetica,sans-serif;color:#000000;font-size:14px;line-height:21px" align="left" valign="top" width="496"><span style="font-family:Arial,Helvetica,sans-serif;color:#000000;font-size:14px;line-height:21px"><span class="im"><br/>Geachte $first_name $last_name,<br/><br/>Welkom bij KPN WiFi HotSpots! We bellen u binnen twee werkdagen om uw bestelling door te nemen. We maken dan meteen een afspraak voor de installatie van uw KPN WiFi HotSpot.<br/><br/><strong>Uw bestelling:</strong><br/>1 KPN WiFi HotSpot access point<br/>Contractduur: 36 maanden<br/>Maandelijkse kosten: &#8364; 50,- excl. BTW<br/><br/><strong>Uw korting:</strong><br/>Gratis apparatuur en installatie (normaal &#8364; 600,-)<br/>Eerste zes maanden 50% korting op maandelijkse kosten<br/><br/><strong>Uw contactgegevens:</strong><br/>$contact_details $invoice_details<br/>Heeft u nog vragen? Neem dan contact op met onze helpdesk op 0800 0414. We helpen u graag!<br/><br/>Met vriendelijke groet,<br/><br/>Robert Bakker<br/>Commercieel Manager<br/>KPN WiFi HotSpots<br/></span></span></td>
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

    $contact_details = '<br/><table style="font-family: Arial,Helvetica,sans-serif;color: #000000;font-size: 14px;line-height: 21px;border-spacing: 0;">';

    foreach($contact_fields as $key => $field) {
        $contact_details .= append_field($key, $field, true, true);
    }

    $contact_details .= '</table>';

    if(@array_key_exists("factuur", $_POST)) {

        $invoice_details = '<br/><strong>Uw facturatiegegevens:</strong><br/><br/><table style="font-family: Arial,Helvetica,sans-serif;color: #000000;font-size: 14px;line-height: 21px;border-spacing: 0;">';

        foreach($invoice_fields as $key => $field) {
            $invoice_details .= append_field($key, $field, true, true);
        }

        $invoice_details .= '</table>';

    }

    $plain = 'Geachte '.$first_name.' '.$last_name.',

Welkom bij KPN WiFi HotSpots! We bellen u binnen twee werkdagen om uw bestelling door te nemen. We maken dan meteen een afspraak voor de installatie van uw KPN WiFi HotSpot.

Uw bestelling:
1 KPN WiFi HotSpot access point
Contractduur: 36 maanden
Maandelijkse kosten: € 50,- excl. BTW

Uw korting:
Gratis apparatuur en installatie (normaal € 600,‑)
Eerste zes maanden 50% korting op maandelijkse kosten

Uw contactgegevens:
'.$contact_details.'

Uw facturatiegegevens:
'.$invoice_details.'

Heeft u nog vragen? Neem dan contact op met onze helpdesk op 0800 0414. We helpen u graag!


Met vriendelijke groet,

Robert Bakker
Commercieel Manager
KPN WiFi HotSpots';

    $mail = new PHPMailer;

    $mail->From = 'verkoop-kpnhotspots@kpn.com';
    $mail->FromName = 'KPN WiFi HotSpots';
    $mail->addAddress($email, $first_name." ".$last_name);
    $mail->addReplyTo('verkoop-kpnhotspots@kpn.com', 'KPN WiFi HotSpots');
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();
    $mail->Host = "localhost";
    $mail->isHTML(true);

    $mail->Subject = 'Bevestiging van uw bestelling bij KPN WiFi HotSpots';
    $mail->Body = $html;
    $mail->AltBody = $plain;

    if(!$mail->send()) {
       // echo 'Mailer Error: ' . $mail->ErrorInfo;
    }

    forward_thanks();

} else {
    return_order();
}