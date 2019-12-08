<?php

/*
    * бот "@scoribot"
    * https://telegram.me/scoribot
    * отправляет уведомления о тикетах 
    *
    * документация 
    * https://telegram.me/synonim_bot
    * https://tlgrm.ru/docs/bots/api
*/

mb_internal_encoding("UTF-8");
//@error_reporting(E_ALL);

define("TOKEN", '322748457:AAHzMGKjIufhqq23hqr6f5vyFWdp1EJtglQ');
define("API_URL", 'https://api.telegram.org/bot'.TOKEN.'/');
define('WEBHOOK_URL', "https://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");
//require('telegram_api.php');

$content = file_get_contents("php://input");
$update = json_decode($content, true);
$date = date('d-m-Y G:i:s');

apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));

function checkUser($userId)
{
    $staff = array();   // список Telegram ID из osTicket, кому позволено получать мессаги от бота
    if (!in_array($userId, $staff)) 
    die;      
}

function processMessage($message) 
{
    $recipient = $message['chat']['id'];
    $my_id = $message['from']['id'];
    $out = '';
    $menu = '';
   
    if (in_array($message['from']['id'], $banlist)) 
    die;  
    
    // если пришел текст, будем работать
    if (isset($message['text'])) 
    {
        $inputText = mb_strtolower($message['text']);
        
        // обработаем commands
        switch ($inputText)
        {
            case strpos($inputText, "/start") === 0: // надо отправить юзеру его Telegram ID для внесения в кабинет
            apiRequestJson("sendMessage", array('chat_id' => $recipient, "text" => "<b>{$message['from']['first_name']} {$message['from']['last_name']} {$message['from']['username']}</b>, Ваш Telegram ID —  <b>{$my_id}</b>. \r\nЧтобы получать оповещения в Telegram, внесите этот ID в свой профиль в системе тикетов.", "parse_mode" => "HTML"));
            die; // в commands стандартный break не сработает, надо die/exit

            case strpos($inputText, "/ping") === 0:
            apiRequestJson("sendMessage", array('chat_id' => $recipient, "text" => 'Pong. Сервер ОК.'));
            die;                        
                    
            case strpos($inputText, "/stop") === 0:
            apiRequestJson("sendMessage", array('chat_id' => $recipient, "text" => 'жопа', 'parse_mode' => 'HTML'));
            die;                        
        }
        
        // отправляем ответ
        sendMessage($recipient, $out, $menu);
    } 
}


/**************************
function processQuery($query)
{
    $callback = json_decode($query['data']);
    $text = $callback->text;
    $recipient = $query['message']['chat']['id'];
    $shift = $callback->shift;
    $menu = '';

    // теперь вызываем getSyn и выделяем заполнялку строки в функцию
    $arr = getSyn($text);
    
    foreach ($arr as $key => $value)
    {
        if ($key > $shift)
        {
            $out .= $value."\n";
            $len = mb_strlen($out);
            if ( $len > 150 ) 
            {
                $menu = array('inline_keyboard' => 
                    array(
                        array(
                            //array('text' => 'Отмена', 'callback_data' => 'cancel'),
                            array('text' => 'Далее', 'callback_data' => '{ "text": "'.$text.'", "shift": "'.$key.'" }')
                        ),
                    ),
                );
                break;    
            }
        }
    }
    
    //$check = print_r( $menu, true );
    //error_log("из processQuery:     sendMessage({$recipient}, {$out}, {$check});", 3, "1test.log");
    
    // отправляем
    sendMessage($recipient, $out, $menu);
}
******************/



function sendMessage($recipient, $out, $menu)
{
    apiRequestJson("sendMessage", array('chat_id' => $recipient, "text" => $out, 'parse_mode' => 'HTML',  'reply_markup' => $menu));
}



/*
commands
start - Начать
ping - Пинг сервера
stop - Отключить оповещения



[
    {
        "message_id":1584,
        "from":
        {
            "id":83561141,
            "first_name":"\u0413\u043b\u044e\u043a\u044a",
            "username":"motokofr"
        },
        "chat":
        {
            "id":83561141,
            "first_name":"\u0413\u043b\u044e\u043a\u044a",
            "username":"motokofr",
            "type":"private"
            },
        "date":1476436169,
        "text":"\u0442\u0435\u0441\u0442"
    }
]    
*/




function exec_curl_request($handle) 
{
  $response = curl_exec($handle);

  //error_log("--> exec_curl_request {$response} \n\n", 3, "1test.log");

  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    global $date;    
    error_log("{$date} Curl returned error $errno: $error\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");
    curl_close($handle);
    return false;
  }

  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);

  if ($http_code >= 500) 
  {
    // do not wat to DDOS server if something goes wrong
    global $date;    
    error_log("{$date} ошибка CURL, HTTP={$http_code}\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");    
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    global $date;    
    error_log("{$date} Request has failed with error {$response['error_code']}: {$response['description']}, HTTP={$http_code}\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");
    if ($http_code == 401) {
      global $date;    
      error_log("{$date} Invalid access token provided, HTTP={$http_code}\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");        
      throw new Exception('Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      global $date;
      //error_log("\n{$date} Request was successfull: {$response['description']}, HTTP={$http_code}\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");
    }
    $response = $response['result'];
  }

  return $response;
}



function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    global $date;
    error_log("{$date} Method name must be a string\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    global $date;      
    error_log("{$date} Parameters must be an array\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");
    return false;
  }

  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);
  
  //error_log("--> apiRequest {$url} \n\n", 3, "1test.log");  

  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);

  return exec_curl_request($handle);
}



function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    global $date;      
    error_log("{$date} Method name must be a string\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    global $date;      
    error_log("{$date} Parameters must be an array\n", 3, __DIR__."/{$_SERVER['PHP_SELF']}.log");
    return false;
  }

  $parameters["method"] = $method;
  $parameters = json_encode($parameters);
  
  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
//curl_setopt($handle, CURLOPT_POST, 1); // 
  curl_setopt($handle, CURLOPT_POSTFIELDS, $parameters);
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

  //$log = print_r($parameters, true);
  //error_log("--> apiRequestJson {$log} \n\n", 3, "1test.log");  
  
  return exec_curl_request($handle);
}

//error_log("<-- {$content} \n\n", 3, "1test.log");

if (!$update) {
  // receive wrong update, must not happen
}



// если прилетела мессага
if (isset($update["message"])) {
    processMessage($update["message"]);
}



// если прилетел online query
if ( isset($update["callback_query"]) ) {
    processQuery($update["callback_query"]);
}



?>

