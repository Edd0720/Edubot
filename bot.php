<?php
$token = "6975591580:AAFi-Ly9rcGMWRx6oTcx5DARqRvt6EbOH94";
$website = "https://api.telegram.org/bot".$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];

$clima=0;

switch($message) {
    case '/start':
        $response = 'Me has iniciado';
        sendMessage($chatId, $response);
        break;
    case '/info':
        $response = 'Hola! Soy @trecno_bot';
        sendMessage($chatId, $response);
        break;
    case 'Edu':
        $response ="Hola Edu, bienvenido, te quiero mucho";
        sendMessage($chatId,$response);
        break;
    case 'clima':
        $_SESSION['clima'] = 1;
        $response = "Escribe un lugar del que quieras conocer el clima $clima";
        sendMessage($chatId, $response);;
        break;
    default:    
        if ($clima == 1) {
            $cityMessage = waitForUserMessage();
            $city = $cityMessage['text'];
            $response = "La temperatura de $city es " . obtenerClima($city);
            sendMessage($chatId, $response);
        } else{
            $response= "No te he entendido $clima";
            sendMessage($chatId,$response);
        }
        break;

}



function sendMessage($chatId, $response) {
    $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
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
        return $temp;
    } else {
        $msg=$result['message'];
        return $msg;
    }
}

function Clima(){
    if ($clima == 1){
        $clima = 0;
        return $clima;
    } else {
        $clima = 1;
        return $clima;
    }

}

function desactivarClima(){
    $clima=FALSE;
    return $clima;
}

function waitForUserMessage() {
    $update = getTelegramUpdate();
    while (!isset($update['message']['text'])) {
        // No hay mensaje de texto, esperar y volver a intentar
        sleep(1);
        $update = getTelegramUpdate();
    }
    return $update['message'];
}

function getTelegramUpdate() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}
    
?>
