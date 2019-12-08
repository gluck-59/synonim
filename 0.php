<?php
    namespace myfunc;
    
mb_internal_encoding("UTF-8");
@error_reporting(E_ALL);
define("TOKEN",'276486860:AAGQTjJWQZkBhQl0hEneffJmyV7AAABv_zY');
define("API_URL",'https://api.telegram.org/bot'.TOKEN.'/');
define('WEBHOOK_URL',"https://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");
require('simple_html_dom.php');
require('telegram_api.php');

echo '<pre>';

function print_r($expr)
{
    echo 'jopa<br>';
    print_r($expr);
    return;
}

$track[0] = '596141654776797';

print_r($track);

die;
$sections = array(
    array(
            'name' => 'persona',
            'desc' => 'ИНФОРМАЦИЯ О ЗАЕМЩИКЕ',
            'block' => array(
                'personalInfo' => array(
                    'name' => 'Данные заемщика',
                ),
                
                'addressRegistration' => 'Адрес прописки/регистрации',
                'addressResidential' => 'Адрес проживания',
                'contactInfo' => 'Контактные данные',
                'employment' => 'Трудоустройство'
            ),
            
        )
);








$section = array(
    'persona' => mb_strtolower('ИНФОРМАЦИЯ О ЗАЕМЩИКЕ'),
    'info' => mb_strtolower('ИНФОРМАЦИЯ О ЗАЙМЕ'),
    'loanReceivingMethod' => mb_strtolower('ИНФОРМАЦИЯ О КОНТЕЙНЕРЕ ВЫДАЧИ ЗАЙМА'),
    'truthQuestions' => mb_strtolower('ВОПРОСЫ ПРАВДЫ')
);

    $persona = array(
        'personalInfo' => 'Данные заемщика',
        'addressRegistration' => 'Адрес прописки/регистрации',
        'addressResidential' => 'Адрес проживания',
        'contactInfo' => 'Контактные данные',
        'employment' => 'Трудоустройство',
    );
       
        $personalInfo = array(
            'personaID' => 'ID заемщика',
            'lastName' => 'Фамилия',
            'firstName' => 'Имя',
            'patronimic' => 'Отчество',
            'gender' => 'Пол',
            'birthDate' => 'Дата рождения',
            'placeOfBirth' => 'Место рождения',
            'passportSN' => 'Серия и номер паспорта',
            'issueDate' => 'Дата выдачи паспорта',
            'subCode' => 'Код подразделения',
            'issueAuthority' => 'Кем выдан паспорт',
            'maritalStatus' => 'Семейный статус',
            'dependents' => 'Количество иждивенцев',
            'INN' => 'ИНН',
            'OMS' => 'ОМС',
            'SNILS' => 'СНИЛС',
            'drivingLicense' => 'Водительское удостоверение',
            'carOwning' => 'Наличие автомобиля в собственности',
            'houseOwning' => 'Наличие жилья в собственности'
        );
        
        $addressRegistration = array(
            'postIndex' => 'Индекс',
            'region' => 'Регион',
            'city' => 'Город или населённый пункт',
            'street' => 'Улица',
            'house' => 'Дом',
            'building' => 'Строение',
            'flat' => 'Квартира',
            'kladrID' => 'Идентификатор КЛАДР (ФИАС)'
        );
        
        $addressResidential = array(
            'postIndex' => 'Индекс',
            'region' => 'Регион',
            'city' => 'Город или населённый пункт',
            'street' => 'Улица',
            'house' => 'Дом',
            'building' => 'Строение',
            'flat' => 'Квартира',
            'kladrID' => 'Идентификатор КЛАДР (ФИАС)'            
        );
        
        $contactInfo = array(
            'cellular' => 'Номер мобильного телефона',
            'cellularState' => 'Статус подтверждения мобильного телефона',
            'cellularMethod' => 'Способ подтверждения ',
            'phone' => 'Номер домашнего телефона',
            'phoneState' => 'Статус подтверждения домашнего телефона',
            'phoneMethod' => 'Способ подтверждения домашнего телефона',
            'email' => 'Адрес личного Email',
            'emailState' => 'Статус подтверждения Email',
            'emailMethod' => 'Способ подтверждения Email',
            'relativePhone' => 'Номер телефона родственника',
            'relativeLastName' => 'Фамилия родственника',
            'relativeFirstName' => 'Имя родственника',
            'relativePatronimic' => 'Отчество родственника',
            'relativePhoneState' => 'Статус подтверждения телефона родственника',
            'relativePhoneMethod' => 'Способ подтверждения телефона родственника',
            'spousePhone' => 'Номер телефона супруга',
            'spouseLastName' => 'Фамилия супруга',
            'spouseFirstName' => 'Имя супруга',
            'spousePatronimic' => 'Отчество супруга',
            'spousePhoneState' => 'Статус подтверждения телефона супруга',
            'spousePhoneMethod' => 'Способ подтверждения телефона супруга'           
        );
        
        $employment = array(
            'jobCategory' => 'Категория занятости',
            'employer' => 'Наименование работодателя',
            'employerSite' => 'Сайт работодателя',
            'employerPhone' => 'Официальный телефон бухгалтерии',
            'workPhone' => 'Номер рабочего телефона',
            'workPhoneState' => 'Статус подтверждения рабочего телефона',
            'workPhoneMethod' => 'Способ подтверждения рабочего телефона',
            'workEmail' => 'Адрес рабочего Email',
            'workEmailState' => 'Статус подтверждения рабочего Email',
            'workEmailMethod' => 'Способ подтверждения рабочего электронного почтового ящика',
            'salaryOfficial' => 'Официальный доход в рублях',
            'salaryActual' => 'Фактический доход в рублях',
            'occupation' => 'Должность',
            'employmentType' => 'Тип занятости',
            'employmentTime' => 'Стаж на текущем рабочем месте',
            'jobExpirience' => 'Общий стаж в профессии',
            'previousEmployment' => 'Последние два места работы'
        );
        
        





    


//foreach ( $contactInfo as $key => $value )
{
    echo($key.' - ');
    echo($value.'<br>');
    
    $stmt = $pdo->query('INSERT INTO `api_doc`
    (`name`,       `variable`,    `mandratory`,  `type`,`reference`,   `level`) 
    VALUES 
    ("'.$value.'", "'.$key.'",    10,             10,     1,             2)'
    );

    //$stmt = $pdo->query('DELETE FROM `api_doc` WHERE name like "'.$value.'"');
}




//print_r($persona); 




