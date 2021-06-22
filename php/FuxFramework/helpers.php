<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Fux\Request;
use Fux\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function request()
{
    return new Request();
}

/* Performs an echo operations of $data after converting it to JSON format */
function json($data)
{
    echo json_encode($data);
    return "";
}


$__FUX_SERVICE_ARE_BOOTSTRAPPED = false;
function bootstrapServiceProviders()
{
    global $__FUX_SERVICE_ARE_BOOTSTRAPPED;
    $files = rglob(__DIR__ . "/../../services/*.php");
    foreach ($files as $fileName) {
        include_once $fileName;
    }
    if (!$__FUX_SERVICE_ARE_BOOTSTRAPPED) {
        $classes = get_declared_classes();
        foreach ($classes as $className) {
            if (strpos($className, "Service")) {
                $implementations = class_implements($className);
                if (isset($implementations['IServiceProvider'])) {
                    $className::bootstrap();
                    /*(new LogModel())->save([
                        "method" => "SERVICE BS",
                        "url" => (new Request())->requestUri,
                        "body" => "$className",
                        "session" => DB::ref()->real_escape_string(json_encode($_SESSION ?? [])),
                        "ip" => isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR']
                    ]);*/
                }
            }
        }
        $__FUX_SERVICE_ARE_BOOTSTRAPPED = true;
    }
}


$__FUX_SERVICE_ARE_DISPOSED = false;
function disposeServiceProviders()
{
    global $__FUX_SERVICE_ARE_DISPOSED, $__FUX_SERVICE_ARE_BOOTSTRAPPED;
    if (!$__FUX_SERVICE_ARE_DISPOSED && $__FUX_SERVICE_ARE_BOOTSTRAPPED) {
        $classes = get_declared_classes();
        foreach ($classes as $className) {
            if (strpos($className, "Service")) {
                $implementations = class_implements($className);
                if (isset($implementations['IServiceProvider'])) {
                    $className::dispose();
                }
            }
        }
        $__FUX_SERVICE_ARE_DISPOSED = true;
    }
}


// Does not support flag GLOB_BRACE
function rglob($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}

function view($viewName, $viewData = [])
{
    global $mysqli, $lang, $lang_id;
    //$lang = LanguageService::getCurrentLanguageCode();
    if (!preg_match("/(.*)\.php/", $viewName)) $viewName .= ".php";

    foreach ($viewData as $varName => $value) {
        ${$varName} = $value;
    }

    include(__DIR__ . "/../../views/$viewName");

    return "";
}

function viewCompose($viewAlias, $ovverideData = [], $params = [])
{
    $fuxView = FuxViewComposerManager::getView($viewAlias);
    if ($fuxView) {
        return view(
            $fuxView->getPath(),
            array_merge($fuxView->getData($params), $ovverideData)
        );
    }
    return '';
}

function asset($asset)
{
    if (substr($asset, 0, 1) === "/") {
        $asset = substr($asset, 1);
    }
    return PROJECT_HTTP_SCHEMA . "://" . DOMAIN_NAME . PROJECT_DIR . "/public/" . $asset;
}


$__FUX_INCLUDED_ASSETS = [];
function assetOnce($asset, $type)
{
    global $__FUX_INCLUDED_ASSETS;
    if (!isset($__FUX_INCLUDED_ASSETS[$asset . '_' . $type])) {
        $assetURL = asset($asset);
        $__FUX_INCLUDED_ASSETS[$asset . '_' . $type] = true;
        switch ($type) {
            case 'script':
                return "<script src='$assetURL'></script>";
                break;
            case 'CSS':
                return "<link rel='stylesheet' type='text/css' href='$assetURL'>";
                break;
            case 'dynamicCSS':
                return "
                    <script>
                        (function(){
                            var file = document.createElement('link');
                            file.setAttribute('rel', 'stylesheet');
                            file.setAttribute('type', 'text/css');
                            file.setAttribute('href', '$assetURL');
                            document.head.appendChild(file);
                        })();
                    </script>
                ";
                break;
        }
    }
    return '';
}

$__FUX_INCLUDED_EXTERNAL_ASSETS = [];
function assetExternalOnce($assetURL, $type)
{
    global $__FUX_INCLUDED_EXTERNAL_ASSETS;
    if (!isset($__FUX_INCLUDED_EXTERNAL_ASSETS[$assetURL . '_' . $type])) {
        switch ($type) {
            case 'script':
                return "<script src='$assetURL'></script>";
                break;
            case 'CSS':
                return "<link rel='stylesheet' type='text/css' href='$assetURL'>";
                break;
        }
        $__FUX_INCLUDED_EXTERNAL_ASSETS[$assetURL . '_' . $type] = true;
    }
    return '';
}

function addCssToHead($css)
{
    $css = str_replace("\n", "", $css);
    return "
        <script>
            (function(){
                var head = document.head || document.getElementsByTagName('head')[0];
                var style = document.createElement('style');
                head.appendChild(style);
                
                style.type = 'text/css';
                if (style.styleSheet){
                  // This is required for IE8 and below.
                  style.styleSheet.cssText = '$css';
                } else {
                  style.appendChild(document.createTextNode('$css'));
                }
            })();
        </script>
    ";
}


$__FUX_INCLUDED_JSX_COMPONENTS = [];
function importJSXComponent($publicPath)
{
    global $__FUX_INCLUDED_JSX_COMPONENTS;
    if (in_array($publicPath, $__FUX_INCLUDED_JSX_COMPONENTS)) return;
    $__FUX_INCLUDED_JSX_COMPONENTS[] = $publicPath;
    return file_get_contents(PROJECT_ROOT_DIR . "/public/$publicPath");
}

function redirect($route)
{
    (new LogModel())->save([
        "method" => "REDIRECT",
        "url" => (new Request())->requestUri,
        "body" => "Redirect to $route",
        "session" => DB::ref()->real_escape_string(json_encode($_SESSION ?? [])),
        "ip" => isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR']
    ]);
    header("Location: " . PROJECT_HTTP_SCHEMA . "://" . DOMAIN_NAME . PROJECT_DIR . $route);
    exit;
}

function routeFullUrl($route)
{
    return PROJECT_HTTP_SCHEMA . "://" . DOMAIN_NAME . PROJECT_DIR . $route;
}

if (!function_exists('sanitize_post')) {
    function sanitize_post()
    {
        global $_POST_SANITIZED;
        if ($_POST_SANITIZED) return;
        array_walk_recursive($_POST, function (&$leaf) {
            if (is_string($leaf))
                $leaf = mysqli_real_escape_string(DB::ref(), $leaf);
        });
        $_POST_SANITIZED = true;
    }
}

if (!function_exists('sanitize_get')) {
    function sanitize_get()
    {
        global $_GET_SANITIZED;
        if ($_GET_SANITIZED) return;
        array_walk_recursive($_GET, function (&$leaf) {
            if (is_string($leaf))
                $leaf = mysqli_real_escape_string(DB::ref(), $leaf);
        });
        $_GET_SANITIZED = true;
    }
}
if (!function_exists('sanitize_request')) {
    function sanitize_request()
    {
        global $_REQUEST_SANITIZED;
        if ($_REQUEST_SANITIZED) return;
        array_walk_recursive($_REQUEST, function (&$leaf) {
            if (is_string($leaf))
                $leaf = mysqli_real_escape_string(DB::ref(), $leaf);
        });
        $_REQUEST_SANITIZED = true;
    }
}

function sanitize_object(&$object)
{
    array_walk_recursive($object, function (&$leaf) {
        if (is_string($leaf))
            $leaf = mysqli_real_escape_string(DB::ref(), $leaf);
    });
}

function db_escape_string($str)
{
    return DB::ref()->real_escape_string($str);
}

function send_email($to, $sub, $mes, $from)
{
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'mail.bookizon.it';
    $mail->Port = 25;
    $mail->SMTPAuth = true;
    $mail->Username = 'noreply@bookizon.it';
    $mail->Password = '}njEl3Dh@H9G';
    $mail->setFrom('noreply@bookizon.it', $from);
    $mail->addReplyTo('', '');
    $mail->addAddress($to);
    $mail->Subject = $sub;
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->Body = $mes;
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        return false;
    } else {
        //echo 'The email message was sent.';
        return true;
    }
}

function send_email_self($sub, $message)
{
    send_email(ALERT_EMAIL, $sub, $message);
}

if (!function_exists("issetDef")) {
    function issetDef(&$check, $default)
    {
        return isset($check) ? $check : $default;
    }
}


if (!function_exists('HTMLToRGB')) {
    function HTMLToRGB($htmlCode)
    {
        if ($htmlCode[0] == '#')
            $htmlCode = substr($htmlCode, 1);

        if (strlen($htmlCode) == 3) {
            $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
        }

        $r = hexdec($htmlCode[0] . $htmlCode[1]);
        $g = hexdec($htmlCode[2] . $htmlCode[3]);
        $b = hexdec($htmlCode[4] . $htmlCode[5]);

        return $b + ($g << 0x8) + ($r << 0x10);
    }
}
function HTMLToRGBArray($htmlCode)
{
    $RGB = HTMLToRGB($htmlCode);
    $r = 0xFF & ($RGB >> 0x10);
    $g = 0xFF & ($RGB >> 0x8);
    $b = 0xFF & $RGB;
    $r = ((float)$r);
    $g = ((float)$g);
    $b = ((float)$b);
    return ["r" => $r, "g" => $g, "b" => $b];
}

if (!function_exists('RGBToHSL')) {
    function RGBToHSL($RGB)
    {
        $r = 0xFF & ($RGB >> 0x10);
        $g = 0xFF & ($RGB >> 0x8);
        $b = 0xFF & $RGB;

        $r = ((float)$r) / 255.0;
        $g = ((float)$g) / 255.0;
        $b = ((float)$b) / 255.0;

        $maxC = max($r, $g, $b);
        $minC = min($r, $g, $b);

        $l = ($maxC + $minC) / 2.0;

        if ($maxC == $minC) {
            $s = 0;
            $h = 0;
        } else {
            if ($l < .5) {
                $s = ($maxC - $minC) / ($maxC + $minC);
            } else {
                $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
            }
            if ($r == $maxC)
                $h = ($g - $b) / ($maxC - $minC);
            if ($g == $maxC)
                $h = 2.0 + ($b - $r) / ($maxC - $minC);
            if ($b == $maxC)
                $h = 4.0 + ($r - $g) / ($maxC - $minC);

            $h = $h / 6.0;
        }

        $h = (int)round(255.0 * $h);
        $s = (int)round(255.0 * $s);
        $l = (int)round(255.0 * $l);

        return (object)array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
    }
}

if (!function_exists('adjustBrightness')) {
    function adjustBrightness($hex, $steps)
    {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color = hexdec($color); // Convert to decimal
            $color = max(0, min(255, $color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }
}

if (!function_exists("mime_content_type")) {
    function mime_content_type($filename)
    {
        $result = new finfo();

        if (is_resource($result) === true) {
            return $result->file($filename, FILEINFO_MIME_TYPE);
        }

        return false;
    }
}

function mime2ext($mime)
{
    $all_mimes = '{"webp":["image\/webp"],"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg","image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg","video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application","application\/x-jar"],"zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],"pdf":["application\/pdf","application\/octet-stream"],"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office","application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo","video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop","image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar","application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40","application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
    $all_mimes = json_decode($all_mimes, true);
    foreach ($all_mimes as $key => $value)
        if (array_search($mime, $value) !== false) return $key;
    return null;
}

function array_combine_keep_copy($keys, $values)
{
    $result = array();
    foreach ($keys as $i => $k) {
        $result[$k][] = $values[$i];
    }
    //array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
    return $result;
}


function my_array_unique($array)
{
    $a = [];
    foreach ($array as $k => $v)
        $a[$v] = true;
    return array_keys($a);
}
