<?php
use App\Models\Etemplate;
use App\Models\Settings;
use Illuminate\Http\Request;
use Twilio\Rest\Client;


if (! function_exists('send_email')) {

    function send_email( $to, $name, $subject, $message)
    {
        $temp = Etemplate::first();
        $gnl = Settings::first();
        $template = $temp->emessage;
        $from = $temp->esender;
		if($gnl->email_notify == 1)
		{
			$headers = "From: $gnl->site_name <$from> \r\n";
			$headers .= "Reply-To: $gnl->site_name <$from> \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$mm = str_replace("{{name}}",$name,$template);
			$message = str_replace("{{message}}",$message,$mm);

			if (mail($to, $subject, $message, $headers)) {
			  // echo 'Your message has been sent.';
			} else {
			 //echo 'There was a problem sending the email.';
			}
		}
    }
}


if (! function_exists('send_email_reset')) {

    function send_email_reset( $to, $name, $subject, $link)
    {
        $temp = Etemplate::first();
        $gnl = Settings::first();
        $template = $temp->remessage;
        $from = $temp->esender;
		if($gnl->email_notify == 1)
		{
			$headers = "From: $gnl->site_name <$from> \r\n";
			$headers .= "Reply-To: $gnl->site_name <$from> \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$mm = str_replace("{{name}}",$name,$template);
			$message = str_replace("{{link}}",$link,$mm);

			if (mail($to, $subject, $message, $headers)) {
			  // echo 'Your message has been sent.';
			} else {
			 //echo 'There was a problem sending the email.';
			}
		}
    }
}


if (! function_exists('send_email_transfer_request')) {

    function send_email_transfer_request( $amount, $link)
    {
        $temp = Etemplate::first();
        $gnl = Settings::first();
        $template = $temp->tremessage;
        $from = $temp->esender;
        $to = "varspaytechnology@gmail.com";
        
		if($gnl->email_notify == 1)
		{
			$headers = "From: $gnl->site_name <$from> \r\n";
			$headers .= "Reply-To: $gnl->site_name <$from> \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$mm = str_replace("{{amount}}",$amount,$template);
			$message = str_replace("{{link}}",$link,$mm);

			if (mail($to, "Confirm Transfer Request", $message, $headers)) {
			  // echo 'Your message has been sent.';
			} else {
			 //echo 'There was a problem sending the email.';
			}
		}
    }
}

if (! function_exists('send_email_welcome')) {

    function send_email_welcome( $to, $name, $subject)
    {
        $temp = Etemplate::first();
        $gnl = Settings::first();
        $template = $temp->wemessage;
        $from = $temp->esender;
		if($gnl->email_notify == 1)
		{
			$headers = "From: $gnl->site_name <$from> \r\n";
			$headers .= "Reply-To: $gnl->site_name <$from> \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


			if (mail($to, $subject, $template, $headers)) {
			  // echo 'Your message has been sent.';
			} else {
			 //echo 'There was a problem sending the email.';
			}
		}
    }
}

if (! function_exists('send_alert_email')) {

    function send_alert_email( $to, $name, $subject, $type, $account, $ref, $amount, $desc, $balance, $date)
    {
        $temp = Etemplate::first();
        $gnl = Settings::first();
        $template = $temp->aemessage;
        $from = $temp->esender;
		if($gnl->email_notify == 1)
		{
			$headers = "From: $gnl->site_name <$from> \r\n";
			$headers .= "Reply-To: $gnl->site_name <$from> \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$mm = str_replace("{{name}}",$name,$template);
			$type = str_replace("{{type}}",$type,$mm);
			$ref = str_replace("{{ref}}",$ref,$type);
			$account = str_replace("{{account}}",$account,$ref);
			$amount = str_replace("{{amount}}",$amount,$account);
			$desc = str_replace("{{desc}}",$desc,$amount);
			$balance = str_replace("{{balance}}",$balance,$desc);
			$date = str_replace("{{date}}",$date,$balance);

			if (mail($to, $subject, $date, $headers)) {
			  // echo 'Your message has been sent.';
			} else {
			 //echo 'There was a problem sending the email.';
			}
		}
    }
}

if (! function_exists('user_ip')) {
    function user_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}


if (! function_exists('send_sms')) {
    function send_sms($recipients, $message)
    {
        $temp = Etemplate::first();
        $account_sid = $temp->twilio_sid;
        $auth_token = $temp->twilio_auth;
        $twilio_number = $temp->twilio_number;
        $client = new Client($account_sid, $auth_token);
        try{
            $client->messages->create($recipients, 
                [
                    'from' => $twilio_number,
                    'body' => $message
                ] );
            }catch (TwilioException $e){

            }catch (Exception $e){
    
            }
    }
}


if (! function_exists('notify'))
{
    function notify( $user, $subject, $message)
    {
        send_email($user->email, $user->name, $subject, $message);
        send_sms($user->mobile, strip_tags($message));
    }
}




if (!function_exists('send_email_verification')) {
    function send_email_verification($to, $name, $subject, $message)
    {
        $temp = Etemplate::first();
        $gnl = Settings::first();
        $template = $temp->emessage;
        $from = $temp->esender;

        $headers = "From: $gnl->site_name <$from> \r\n";
        $headers .= "Reply-To: $gnl->site_name <$from> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $mm = str_replace("{{name}}", $name, $template);
        $message = str_replace("{{message}}", $message, $mm);

        if (mail($to, $subject, $message, $headers)) {
            // echo 'Your message has been sent.';
        } else {
            //echo 'There was a problem sending the email.';
        }
    }
}


if (!function_exists('send_sms_verification')) {

    function send_sms_verification($to, $message)
    {
        $temp = Etemplate::first();
        $gnl = Settings::first();
        if ($gnl->sms_verification == 1) {
            $sendtext = urlencode($message);
            $appi = $temp->smsapi;
            $appi = str_replace("{{number}}", $to, $appi);
            $appi = str_replace("{{message}}", $sendtext, $appi);
            $result = file_get_contents($appi);
        }
    }
}

if (!function_exists('castrotime')) {

    function castrotime($timestamp)
    {
        $datetime1=new DateTime("now");
        $datetime2=date_create($timestamp);
        $diff=date_diff($datetime1, $datetime2);
        $timemsg='';
        if($diff->y > 0){
            $timemsg = $diff->y * 12;
        }
        else if($diff->m > 0){
            $timemsg = $diff->m *30;
        }
        else if($diff->d > 0){
            $timemsg = $diff->d *1;
        }    
        if($timemsg == "")
            $timemsg = 0;
        else
            $timemsg = $timemsg;
    
        return $timemsg;
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($timestamp){
        //$time_now = mktime(date('h')+0,date('i')+30,date('s'));
        $datetime1=new DateTime("now");
        $datetime2=date_create($timestamp);
        $diff=date_diff($datetime1, $datetime2);
        $timemsg='';
        if($diff->y > 0){
            $timemsg = $diff->y .' year'. ($diff->y > 1?"s":'');
    
        }
        else if($diff->m > 0){
            $timemsg = $diff->m . ' month'. ($diff->m > 1?"s":'');
        }
        else if($diff->d > 0){
            $timemsg = $diff->d .' day'. ($diff->d > 1?"s":'');
        }
        else if($diff->h > 0){
            $timemsg = $diff->h .' hour'.($diff->h > 1 ? "s":'');
        }
        else if($diff->i > 0){
            $timemsg = $diff->i .' minute'. ($diff->i > 1?"s":'');
        }
        else if($diff->s > 0){
            $timemsg = $diff->s .' second'. ($diff->s > 1?"s":'');
        }
        if($timemsg == "")
            $timemsg = "Just now";
        else
            $timemsg = $timemsg.' ago';
    
        return $timemsg;
    }
}


if (! function_exists('convertCurrency'))
{

    function convertCurrency($amount,$from_currency,$to_currency){
        $gnl = Settings::first();
        $apikey = $gnl->api;
        $from_Currency = urlencode($from_currency);
        $to_Currency = urlencode($to_currency);
        $query =  "{$from_Currency}_{$to_Currency}";
        // change to the free URL if you're using the free version
        $json = file_get_contents("https://free.currconv.com/api/v7/convert?q={$query}&compact=ultra&apiKey={$apikey}");
        $obj = json_decode($json, true);
        $val = floatval($obj["$query"]);
        $total = 10 * $amount;
        return $total;
    }
}


if (! function_exists('boomtime'))
{
    function boomtime($timestamp){
        //$time_now = mktime(date('h')+0,date('i')+30,date('s'));
        $datetime1=new DateTime("now");
        $datetime2=date_create($timestamp);
        $diff=date_diff($datetime1, $datetime2);
        $timemsg='';
        if($diff->h > 0){
            $timemsg = $diff->h * 1;
        }    
        if($timemsg == "")
            $timemsg = 0;
        else
            $timemsg = $timemsg;

        return $timemsg;
    }

}

if( !function_exists('statement_invoice') )
{
    function statement_invoice( $param ){
        $desc = explode(":", $param);

        return $desc[8];
    }
}

if(!function_exists('get_browsers')){
    function get_browsers(){

        $user_agent= $_SERVER['HTTP_USER_AGENT'];

        $browser = "Unknown Browser";

        $browser_array = array(
            '/msie/i'  => 'Internet Explorer',
            '/Trident/i'  => 'Internet Explorer',
            '/firefox/i'  => 'Firefox',
            '/safari/i'  => 'Safari',
            '/chrome/i'  => 'Chrome',
            '/edge/i'  => 'Edge',
            '/opera/i'  => 'Opera',
            '/netscape/'  => 'Netscape',
            '/maxthon/i'  => 'Maxthon',
            '/knoqueror/i'  => 'Konqueror',
            '/ubrowser/i'  => 'UC Browser',
            '/mobile/i'  => 'Safari Browser',
        );

        foreach($browser_array as $regex => $value){
            if(preg_match($regex, $user_agent)){
                $browser = $value;
            }
        }
        return $browser;
    }
}

if(!function_exists('get_os')){
    function get_os(){

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = "Unknown OS Platform";
        $os_array = array(
            '/windows nt 10/i'  => 'Windows 10',
            '/windows nt 6.3/i'  => 'Windows 8.1',
            '/windows nt 6.2/i'  => 'Windows 8',
            '/windows nt 6.1/i'  => 'Windows 7',
            '/windows nt 6.0/i'  => 'Windows Vista',
            '/windows nt 5.2/i'  => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'  => 'Windows XP',
            '/windows xp/i'  => 'Windows XP',
            '/windows nt 5.0/i'  => 'Windows 2000',
            '/windows me/i'  => 'Windows ME',
            '/win98/i'  => 'Windows 98',
            '/win95/i'  => 'Windows 95',
            '/win16/i'  => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i'  => 'Mac OS 9',
            '/linux/i'  => 'Linux',
            '/ubuntu/i'  => 'Ubuntu',
            '/iphone/i'  => 'iPhone',
            '/ipod/i'  => 'iPod',
            '/ipad/i'  => 'iPad',
            '/android/i'  => 'Android',
            '/blackberry/i'  => 'BlackBerry',
            '/webos/i'  => 'Mobile',
        );

        foreach ($os_array as $regex => $value){
            if(preg_match($regex, $user_agent)){
                $os_platform = $value;
            }
        }

        return $os_platform;

    }
}

if(!function_exists('get_device')){
    function get_device(){

        $tablet_browser = 0;
        $mobile_browser = 0;

        if(preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))){
            $tablet_browser++;
        }

        if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))){
            $mobile_browser++;
        }

        if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),
        'application/vnd.wap.xhtml+xml')> 0) or
            ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or
                isset($_SERVER['HTTP_PROFILE'])))){
                    $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c','acs-','alav','alca','amoi','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',

            'newt','noki','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',

            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda','xda-');

        if(in_array($mobile_ua,$mobile_agents)){
            $mobile_browser++;
        }

                if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0){
                    $mobile_browser++;

                    //Check for tables on opera mini alternative headers

                    $stock_ua =
                    strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?
                    $_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:
                    (isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?
                    $_SERVER['HTTP_DEVICE_STOCK_UA']:''));

                    if(preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)){
                        $tablet_browser++;
                    }
                }

                if($tablet_browser > 0){
                    //do something for tablet devices

                    return 'Tablet';
                }
                else if($mobile_browser > 0){
                    //do something for mobile devices

                    return 'Mobile';
                }
                else{
                    //do something for everything else
                        return 'Computer';
                }

    }
}