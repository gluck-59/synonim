<?php
    
// бот "synonim_bot"
// https://telegram.me/synonim_bot

mb_internal_encoding("UTF-8");
//@error_reporting(E_ALL);

define("TOKEN", '282576978:AAG4iW5xXo6LeG1oDc1CI408c344Q1d-pLU');
define("API_URL", 'https://api.telegram.org/bot'.TOKEN.'/');
define('WEBHOOK_URL', "https://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");
require('simple_html_dom.php');
require('telegram_api.php');
//require('emoticon_fuck.php');

apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));

//echo('<pre>');
//var_dump(($argv));
//echo('</pre>');

function processMessage($message) {
  // process incoming message
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if        ( $message['from']['first_name'])   $from = $message['from']['first_name'];
  else if   ( $message['from']['username']  )   $from = $message['from']['username'];
  else if   ( $message['from']['last_name'] )   $from = $message['from']['last_name'];
  else $from = 'мой друг';
  
  $out = '';
  $menu = '';
  
  // баны
  // 83561141 - это я
  require('banlist.php');

  if (in_array($message['from']['id'], $banlist)) 
  {
      apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => '<a href="http://natribu.org">Узнать ответ</a>', 'parse_mode' => 'HTML'));
      die;  
  }
  
  // если пришел текст, будем работать
  if (isset($message['text'])) 
  {
    $inputText = mb_strtolower($message['text']);
    $state = 1; // успешно
    
    // обработаем commands
    switch ($inputText)
    {
        case strpos($inputText, "/start") === 0:
        apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => "Привет, {$from}!\nНапишите мне слово по-русски и я подберу к нему синоним."));
        die; // в commands стандартный break не сработает, надо die/exit
        
        case strpos($inputText, "/help") === 0:
        //apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => "Напишите мне слово на русском языке, к которому Вы хотите найти синонимы и я постараюсь Вам помочь.\nТакже я умею исправлять ошибки и опечатки.\n\nЕсли я понравился Вам, пожалуйста проголосуйте за меня в <a href=\"https://storebot.me/bot/synonim_bot\">каталоге ботов</a>", 'disable_web_page_preview' => true, 'parse_mode' => 'HTML'));
        apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => "Напишите мне слово на русском языке, к которому Вы хотите найти синонимы и я постараюсь Вам помочь.\nТакже я умею исправлять ошибки и опечатки.", 'disable_web_page_preview' => true, 'parse_mode' => 'HTML'));
        die;

        case strpos($inputText, "/about") === 0:
        //apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => "Я робот Синоним и я знаю более 160 тысяч слов: существительных, прилагательных, глаголов... В своих делах я использую <a href=\"www.trishin.ru/left/dictionary\">словарь синонимов</a> В.Н.Тришина, а правописание проверяет <a href=\"http://api.yandex.ru/speller\">Яндекс.Спеллер</a>. \nЕсли Вы нашли баг, свяжитесь <a href=\"telegram.me/motokofr\">с моим разработчиком</a>. \nЕсли я понравился Вам, пожалуйста проголосуйте за меня в <a href=\"https://storebot.me/bot/synonim_bot\">каталоге ботов</a>", 'disable_web_page_preview' => true, 'parse_mode' => 'HTML'));
        apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => "Я робот Синоним и я знаю более 160 тысяч слов: существительных, прилагательных, глаголов... В своих делах я использую <a href=\"www.trishin.ru/left/dictionary\">словарь синонимов</a> В.Н.Тришина, а правописание проверяет <a href=\"http://api.yandex.ru/speller\">Яндекс.Спеллер</a>. \nЕсли Вы нашли баг, свяжитесь <a href=\"telegram.me/motokofr\">с моим разработчиком</a>.", 'disable_web_page_preview' => true, 'parse_mode' => 'HTML'));
        die;
        
        case strpos($inputText, "/stat") === 0:
        apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => getStat(), 'parse_mode' => 'HTML'));
        die;                        
        
        
        // если спросят ХУЙ, отдаем *name спросившего
        case strpos($inputText, "хуй") === 0:
        $hui = '';
        if ($message['from']['first_name']) 
            $hui .= ' '.$message['from']['first_name'];
        elseif ($message['from']['last_name'])
            $hui .= ' '.$message['from']['last_name'];
        elseif ($message['from']['username'])
            $hui .= ' '.$message['from']['username'];
        apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => $hui, 'parse_mode' => 'HTML'));
        die;                        
    }
    
    
    
    /*
    **  вся логика здесь
    */
    
    // идем в словарь без спеллинга
    $arr = getSyn($inputText);
//    error_log("ввод: $inputText\n", 3, "1test.log");
//    error_log('getSyn: '.print_r($arr, true)."\n", 3, '1test.log');


    // если ничего нет то возможно это опечатка
    // проспеллим ввод яндексом и пойдем в словарь с исправленным словом
    if($arr['state'] == 2)
    {    
        $text = mb_strtolower(checkSpell($inputText));
    //error_log("спелл: $text\n", 3, "1test.log");
        $arr = getSyn($text);
    }
    
    foreach ($arr['arr'] as $key => $value)
    {
        $out .= $value."\n";
        $len = mb_strlen($out);
        
        // нельзя посылать слишком длинный текст
        if ( $len > 150 ) break;    
    }

    $suggest = $arr['arr'];

    if ($key+1 < count($arr['arr']))
    {
        $menu = array('inline_keyboard' => 
            array(
                array(
                    //array('text' => 'Отмена', 'callback_data' => 'cancel'),
                    array('text' => 'Далее', 'callback_data' => '{ "text": "'.$inputText.'", "shift": "'.$key.'" }')
                ),
            ),
        );
    }        

    if (!$out) 
    {
        $out = array(
            "Увы, подходящего синонима для «{$text}» не нашлось. \nПопробуйте использовать единственное число или неопределенную форму.",
            "Простите, {$from}, я не в силах подобрать синоним к «{$text}». \nПопробуйте использовать единственное число или неопределенную форму.",
            "{$from}, мне очень жаль, но в моем словарном запасе слово «{$text}» отсуствует напрочь. \nПопробуйте использовать единственное число или неопределенную форму."
        );
        $rand_keys = array_rand($out);
        $out = $out[$rand_keys];    
        
        $state = 0; //'неудачно';
        
        if ( strpos($text, " ") OR strpos($text, "\n"))
            $out = "Попробуйте использовать одно слово, {$from}.";
    }
     
    
    if ( $text && $inputText != $text )
       $out = "<b>{$inputText} → {$text}</b>\n".$out;

    // отправляем
    send($chat_id, $out, $menu);



    // пишем статистику
    if ($state != 1) $suggest = NULL;
    setStat($message, $suggest, $state);
    
    
  } 
  else // если пришел не текст, выдадим отлуп
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Я понимаю только текст. Пишите, {$from}, пишите :)"));
    
}




function processQuery($query)
{
    $callback = json_decode($query['data']);
    $text = $callback->text;
    $chat_id = $query['message']['chat']['id'];
    $shift = $callback->shift;
    $menu = '';

    // теперь вызываем getSyn и выделяем заполнялку строки в функцию
    $arr = getSyn($text);
    
    foreach ($arr['arr'] as $key => $value)
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
    //error_log("из processQuery:     send({$chat_id}, {$out}, {$check});", 3, "1test.log");
    
    // отправляем
    send($chat_id, $out, $menu);
}



function send($chat_id, $out, $menu)
{
    apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => $out, 'parse_mode' => 'HTML',  'reply_markup' => $menu));
}



function getSyn($text)
{
    // сначала пойдем в кэш
    global $pdo;
    $html = $pdo->prepare('SELECT suggest FROM synonim_cache WHERE text like "'.$text.'" ');
    $html->execute();
    $arr = $html->fetchColumn();

    if ($arr) 
        return array('arr' => unserialize($arr));    
    
    // если в кэше слова нет
    unset($html);
    // http://simplehtmldom.sourceforge.net/manual.htm
    $html = file_get_html("http://slova.zkir.ru/dict/{$text}");

    if (!$html)
    {
        $state = 2; // === то ли slova.zkir.ru в дауне, то ли юзер прислал хуйню ===
        $arr = [0 => "Попробуйте ввести одно слово в единственном числе, именительном падеже или неопределенной форме. И да: слово должно существовать в русском языке."];
        return array('arr' => $arr, 'state' => $state);
    }
        
    foreach ( $html->find('a.synonim') as $el ) 
    {
        $arr[] = strip_tags(trim($el->innertext));
    }        
  
    return array('arr' => $arr, 'state' => $state);
}




/*
commands
start - Начать
help - Как пользоваться
about - О Синониме
stat - Статистика использования





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
?>

