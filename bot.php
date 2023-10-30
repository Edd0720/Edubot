<?php
$token = "6975591580:AAFi-Ly9rcGMWRx6oTcx5DARqRvt6EbOH94";
$website = "https://api.telegram.org/bot".$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];
$words = explode(' ', $message);
if(count($words) == 1){
    $word1 = $words[0];
    if($word1 == 'help'){
        $help = [
            'clima'=> [
                'ciudad'=> ['temperatura', 'sensacion', 'humedad'],
            ],
        ];
        
        $response = Help($help);
        sendMessage($chatId, $response);
    }else{
        $response = 'Comando no reconocido';
        sendMessage($chatId, $response);
    }
    
}else if(count($words) == 2){
    $word1 = $words[0];
    $word2 = $words[1];
    if($word1 == 'clima' || $word1 == 'Clima'){
        $clima = obtenerClima($word2);
        foreach ($clima as $item) {
            $res .= $item . PHP_EOL;
        }
        $response = 'Tiempo en '. $word2 . PHP_EOL . $res;
        sendMessage($chatId, $response);
    }else{
        $response = 'Comando no reconocido';
        sendMessage($chatId, $response);
    }
}else if((count($words) == 3)){
    $word1 = $words[0];
    $word2 = $words[1];
    $word3 = $words[2];
    if($word1 == 'clima' || $word1 == 'Clima'){
        if($word3 == 'temperatura' || $word3 == 'Temperatura'){
            $clima = obtenerClima($word2);
            $res = $clima[0];
            $response = 'Tiempo en '. $word2 . PHP_EOL . $res;
            sendMessage($chatId, $response);
        }else if($word3 == 'sensacion' || $word3 == 'Sensacion'){
            $clima = obtenerClima($word2);
            $res = $clima[1];
            $response = 'Tiempo en '. $word2 . PHP_EOL . $res;
            sendMessage($chatId, $response);
        }else if($word3 == 'humedad' || $word3 == 'Humedad'){
            $clima = obtenerClima($word2);
            $res = $clima[2];
            $response = 'Tiempo en '. $word2 . PHP_EOL . $res;
            sendMessage($chatId, $response);
        }            
    }else{
        $response = 'Comando no reconocido';
        sendMessage($chatId, $response);
    }
}

function  obtenerClima($city){
    $url= "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=e403dcc055caf6a3b4e629b066cce4da";
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    $result=curl_exec($ch);
    curl_close($ch);
    
    $result=json_decode($result,true);
    if($result['cod']==200){
        $temp= $result['main']['temp'] - 271.15. " C°";
        $temp = 'Temperatura: ' . $temp;
        $feels= $result['main']['feels_like'] - 271.15. " C°";
        $feels = 'Sensacion termica: ' . $feels;
        $humidity= $result['main']['humidity'];
        $humidity = 'Humedad: ' . $humidity;
        return [$temp, $feels, $humidity];

    } else {
        $msg=$result['No existe esa ciudad'];
        return [$msg];
    }

}

function Help(array $help, string $sep = ',', $prefix = '') {
    $res = "";

    foreach ($help as $key => $value) {
        if (is_array($value)) {
            $res .= Help($value, $sep, "$prefix$key ");
        } else {
            $res .= "$prefix $value" . PHP_EOL;
        }
    }

    return $res;
}

function sendMessage($chatId, $response) {
    $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}
    
?>
