<?php
error_reporting(0);



#####################
### CONFIG OF BOT ###
#####################
define('DEBUG_FILE_NAME', 'log_openline_macardv7'); // if you need read debug log, you should write unique log name
define('CLIENT_ID', 'local.57062d3061fc71.97850406'); // like 'app.67efrrt2990977.85678329' or 'local.57062d3061fc71.97850406' - This code should take in a partner's site, needed only if you want to write a message from Bot at any time without initialization by the user
define('CLIENT_SECRET', '8bb00435c88aaa3028a0d44320d60339'); // like '8bb00435c88aaa3028a0d44320d60339' - TThis code should take in a partner's site, needed only if you want to write a message from Bot at any time without initialization by the user
#####################




writeToLog($_REQUEST, 'Поступил запрос от Битрикс для ImBot');

$appsConfig = Array();
if (file_exists(__DIR__.'/config.php'))
	include(__DIR__.'/config.php');

// Обработка события при получении сообщения в чат)
if ($_REQUEST['event'] == 'ONIMBOTMESSAGEADD')
{
	// Проверка, что есть такой код авторизации у нас в конфиге
	if (!isset($appsConfig[$_REQUEST['auth']['application_token']]))
		return false;

	// Проверка, что обращение из открытой линии
	if ($_REQUEST['data']['PARAMS']['CHAT_ENTITY_TYPE'] != 'LINES')
		return false;
	
	if ($_REQUEST['data']['PARAMS']['MESSAGE'] == '/help')
	{
		$result = restCommand('imbot.message.add', Array(
			"DIALOG_ID" => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
			"MESSAGE" => "Выберете ваш вопрос:",
			"ATTACH" => Array(
				Array("MESSAGE" => "[URL=https://macardv.ru/all_catalog] Найти и купить запчасть[/URL]"),
				Array("MESSAGE" => "[URL=https://macardv.ru/how_buy#buy] Как оплатить?[/URL]"),
				Array("MESSAGE" => "[URL=https://macardv.ru/feedback] Написать письмо на эл.почту[/URL]"),
				Array("MESSAGE" => "[URL=https://macardv.ru/delivery] Доставка[/URL]"),
				Array("MESSAGE" => "[URL=https://macardv.ru/vacancies_macar] Вакансии[/URL]"),
				Array("MESSAGE" => "[URL=https://macardv.ru/store_addresses] График работы и контакты магазинов Макар[/URL]"),
				Array("MESSAGE" => "[URL=https://macardv.ru/service_adresses] График работы и контакты партнёрских СТО Автодок[/URL]"),
				Array("MESSAGE" => "[send=/NoAnswer]Нет моего вопроса[/send]"),
			),
		), $_REQUEST["auth"]);
	}
	else if ($_REQUEST['data']['PARAMS']['MESSAGE'] == '/NoAnswer')
	{
		// Выводим меню, если клиент не нашёл своего вопроса
		$result = restCommand('imbot.message.add', Array(
			"DIALOG_ID" => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
			"MESSAGE" => "Нажмите на ваш вопрос:",
			"ATTACH" => Array(
				Array("MESSAGE" => "[send=/ConnectOperator]Переключить на оператора[/send]"),
				Array("MESSAGE" => "[send=/ConnectСonsultant]Переключить на консультанта[/send]"),
				Array("MESSAGE" => "[send=/help]Главное меню[/send]"),
			),
		), $_REQUEST["auth"]);
	}
	else if ($_REQUEST['data']['PARAMS']['MESSAGE'] == '/ConnectOperator')
	{
		//Переводим на оператора по общим вопросам. Надо изменить USER_ID если изменится ответсвенный
		$result = restCommand('imopenlines.bot.session.transfer', Array(
			'CHAT_ID' => $_REQUEST['data']['PARAMS']['CHAT_ID'],
			'USER_ID' => 1,
			'LEAVE' => 'N'
		), $_REQUEST["auth"]);
	}
	else if ($_REQUEST['data']['PARAMS']['MESSAGE'] == '/ConnectСonsultant')
	{
		// Переводим чат в очередь
		$result = restCommand('imopenlines.bot.session.operator', Array(
		
			'CHAT_ID' => $_REQUEST['data']['PARAMS']['CHAT_ID'],
		
		), $_REQUEST["auth"]);
		
		
		// Отправляем сообщение о переводе в очередь
		$result = restCommand('imbot.message.add', Array(
		
			"DIALOG_ID" => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
			"MESSAGE" => "Вам ответит первый освободившийся консультант. Ориентировочное время ожидания ответа до 10 минут.",
		
		), $_REQUEST["auth"]);
	}	
	else false;
}

if ($_REQUEST['event'] == 'ONIMCOMMANDADD')
{
	// проверка, что есть авторизация
	if (!isset($appsConfig[$_REQUEST['auth']['application_token']]))
		return false;
	//обнуление переменной $result
	$result = false;
		
	foreach ($_REQUEST['data']['COMMAND'] as $command)
	{
		writeToLog($command, 'Состав массива $command: ');
		if ($command['COMMAND'] == 'help')
		{
			// Создаём массив с кнопками выводимых на команду help
			$keyboard = Array(
				Array(
					"TEXT" => "Найти и купить запчасть",
					"LINK" => "https://macardv.ru/all_catalog",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "BLOCK",
				),
				Array(
					"TEXT" => "Как оплатить?", 
					"LINK" => "https://macardv.ru/how_buy#buy",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "BLOCK",
				),
				Array(
					"TEXT" => "Написать письмо на эл.почту", 
					"LINK" => "https://macardv.ru/feedback",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "BLOCK",
				),
				Array(
					"TEXT" => "Доставка", 
					"LINK" => "https://macardv.ru/delivery",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "LINE",
					"WIDTH" => "100",
				),
				Array(
					"TEXT" => "Вакансии", 
					"LINK" => "https://macardv.ru/vacancies_macar",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "LINE",
					"WIDTH" => "100",
				),
				Array(
					"TEXT" => "График работы и контакты магазинов Макар",
					"LINK" => "https://macardv.ru/store_addresses",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "BLOCK",
				),
				//Array("TYPE" => "NEWLINE"), // размещать кнопки на следующей строке
				Array(
					"TEXT" => 'График работы и контакты СТО "Автодок"', 
					"LINK" => "https://macardv.ru/service_adresses",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "BLOCK",
				),
				Array(
					"TEXT" => "Нет моего вопроса", 
					"COMMAND" => "NoAnswer",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "BLOCK",
				),
			);

			$result = restCommand('imbot.command.answer', Array(
				"COMMAND_ID" => $command['COMMAND_ID'],
				"MESSAGE_ID" => $command['MESSAGE_ID'],
				"MESSAGE" => "Нажмите на кнопку с вашим вопросом",
				"KEYBOARD" => $keyboard
			), $_REQUEST["auth"]);
		}
		else if(($command['COMMAND'] == 'NoAnswer'))
		{
			$keyboard = Array(
				Array(
					"TEXT" => "Соединить с оператором",
					"COMMAND" => "NoAnswer", // разобраться как переключить на оператора в Битрикс24
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "LINE",
				),
				Array(
					"TEXT" => "Главное меню",
					"COMMAND" => "help",
					"BG_COLOR" => "#29619b",
					"TEXT_COLOR" => "#fff",
					"DISPLAY" => "LINE",
				),
			);
			$result = restCommand('imbot.command.answer', Array(
				"COMMAND_ID" => $command['COMMAND_ID'],
				"MESSAGE_ID" => $command['MESSAGE_ID'],
				"MESSAGE" => "Соединить Вас с оператором?",
				"KEYBOARD" => $keyboard
			), $_REQUEST["auth"]);
		}
	}
	
	writeToLog($result, 'Ответ после отправки команды: ');
}

if ($_REQUEST['event'] == 'ONIMBOTJOINCHAT')
{
	// check the event - authorize this event or not
	if (!isset($appsConfig[$_REQUEST['auth']['application_token']]))
		return false;
	
	if ($_REQUEST['data']['PARAMS']['CHAT_ENTITY_TYPE'] != 'LINES')
		return false;
	

	//Передаём приветственное сообщение с кнопкой перехода в Главное меню
	$result = restCommand('imbot.message.add', Array(
		"BOT_ID" => $_REQUEST['data']['PARAMS']['BOT_ID'],
		"DIALOG_ID" => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
		"MESSAGE" => "Здравствуйте, я бот [B]MaCar[/B].\n Пока Вы ожидаете ответа консультанта, я могу ответить на стандартные вопросы.\n Нажмите на кнопку под сообщением.",
		"ATTACH" => Array(
			Array("MESSAGE" => "[send=/help]Главное меню[/send]"),
		)
	), $_REQUEST["auth"]);
}

// receive event "delete chat-bot"
else if ($_REQUEST['event'] == 'ONIMBOTDELETE')
{
	// check the event - authorize this event or not
	if (!isset($appsConfig[$_REQUEST['auth']['application_token']]))
		return false;

	// unset application variables
	unset($appsConfig[$_REQUEST['auth']['application_token']]);

	// save params
	saveParams($appsConfig);

	// write debug log
	writeToLog($_REQUEST['event'], 'ImBot удалён');
}

// receive event "Application install"
else if ($_REQUEST['event'] == 'ONAPPINSTALL')
{
	// handler for events
	$handlerBackUrl = ($_SERVER['SERVER_PORT']==443||$_SERVER["HTTPS"]=="on"? 'https': 'http')."://".$_SERVER['SERVER_NAME'].(in_array($_SERVER['SERVER_PORT'], Array(80, 443))?'':':'.$_SERVER['SERVER_PORT']).$_SERVER['SCRIPT_NAME'];
	
	writeToLog( $handlerBackUrl, 'Ссылка handlerBackUrl' ); // Запись в лог получившейся ссылки
	
	// If your application supports different localizations
	// use $_REQUEST['data']['LANGUAGE_ID'] to load correct localization

	// register new bot
	$result = restCommand('imbot.register', Array(
		'CODE' => 'BotItr7',
		'TYPE' => 'O',
		'EVENT_HANDLER' => $handlerBackUrl,
		'OPENLINE' => 'Y',
		'PROPERTIES' => Array(
			'NAME' => 'ItrBot7',
			'WORK_POSITION' => "Бот для открытой линии macardv.ru",
			'COLOR' => 'BLUE',
			'PERSONAL_PHOTO' => '',
		)
	), $_REQUEST["auth"]);
	writeToLog($result, 'Результат регистрации бота: ');
	
	$botId = $result['result']; //сохраняем идентификатор бота в переменную botId
	

	//Регистрируем команды

	//регистрация команды ConnectOperator для кнопки "Соединить с оператором"
	$result = restCommand('imbot.command.register', Array(
		'BOT_ID' => $botId,
		'COMMAND' => 'ConnectOperator',
		'COMMON' => 'N',
		'HIDDEN' => 'Y',
		'EXTRANET_SUPPORT' => 'N',
		'LANG' => Array(
			Array('LANGUAGE_ID' => 'ru', 'TITLE' => 'Соединить с оператором', 'PARAMS' => ''),
		),
		'EVENT_COMMAND_ADD' => $handlerBackUrl,
	), $_REQUEST["auth"]);
	$commandСonnectOperator = $result['result'];
	
	//регистрация команды NoAnswer для кнопки "Не нашел ответ на вопрос?"
	$result = restCommand('imbot.command.register', Array(
		'BOT_ID' => $botId,
		'COMMAND' => 'NoAnswer',
		'COMMON' => 'N',
		'HIDDEN' => 'Y',
		'EXTRANET_SUPPORT' => 'N',
		'LANG' => Array(
			Array('LANGUAGE_ID' => 'ru', 'TITLE' => '', 'PARAMS' => ''),
		),
		'EVENT_COMMAND_ADD' => $handlerBackUrl,
	), $_REQUEST["auth"]);
	$commandNoAnswer = $result['result'];
	
	//регистрация команды help для вызова первого сообщения с кнопками
	$result = restCommand('imbot.command.register', Array(
		'BOT_ID' => $botId,
		'COMMAND' => 'help',
		'COMMON' => 'N',
		'HIDDEN' => 'Y',
		'EXTRANET_SUPPORT' => 'N',
		'LANG' => Array(
			Array('LANGUAGE_ID' => 'ru', 'TITLE' => 'Вывести первое сообщение с кнопками', 'PARAMS' => ''),
		),
		'EVENT_COMMAND_ADD' => $handlerBackUrl,
	), $_REQUEST["auth"]);
	$commandHelp = $result['result'];
	//Закончили регистрацию команд

	// сохранение параметров бота и авторизации
	$appsConfig[$_REQUEST['auth']['application_token']] = Array(
		'BOT_ID' => $botId,
		'COMMAND_CONNECTOPERATOR' => $commandСonnectOperator,
		'COMMAND_NOANSWER' => $commandNoAnswer,
		'COMMAND_HELP' => $commandHelp,
		'LANGUAGE_ID' => $_REQUEST['data']['LANGUAGE_ID'],
		'AUTH' => $_REQUEST['auth'],
	);
	
	saveParams($appsConfig);

	// write debug log
	writeToLog($appsConfig[$_REQUEST['auth']['application_token']], 'Команды ImBot зарегистрированы:');
}
// receive event "Application install"


/**
 * Save application configuration.
 * WARNING: this method is only created for demonstration, never store config like this
 *
 * @param $params
 * @return bool
 */
function saveParams($params)
{
	$config = "<?php\n";
	$config .= "\$appsConfig = ".var_export($params, true).";\n";
	$config .= "?>";

	file_put_contents(__DIR__."/config.php", $config);

	return true;
}

/**
 * Send rest query to Bitrix24.
 *
 * @param $method - Rest method, ex: methods
 * @param array $params - Method params, ex: Array()
 * @param array $auth - Authorize data, received from event
 * @param boolean $authRefresh - If authorize is expired, refresh token
 * @return mixed
 */
function restCommand($method, array $params = Array(), array $auth = Array(), $authRefresh = true)
{
	$queryUrl = $auth["client_endpoint"].$method;
	$queryData = http_build_query(array_merge($params, array("auth" => $auth["access_token"])));

	writeToLog(Array('URL' => $queryUrl, 'PARAMS' => array_merge($params, array("auth" => $auth["access_token"]))), 'ImBot отправил сообщение:');

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_SSL_VERIFYPEER => 1,
		CURLOPT_URL => $queryUrl,
		CURLOPT_POSTFIELDS => $queryData,
	));

	$result = curl_exec($curl);
	curl_close($curl);

	$result = json_decode($result, 1);

	if ($authRefresh && isset($result['error']) && in_array($result['error'], array('expired_token', 'invalid_token')))
	{
		$auth = restAuth($auth);
		if ($auth)
		{
			$result = restCommand($method, $params, $auth, false);
		}
	}

	return $result;
}

/**
 * Get new authorize data if you authorize is expire.
 *
 * @param array $auth - Authorize data, received from event
 * @return bool|mixed
 */
function restAuth($auth)
{
	if (!CLIENT_ID || !CLIENT_SECRET)
		return false;

	if(!isset($auth['refresh_token']))
		return false;

	$queryUrl = 'https://oauth.bitrix.info/oauth/token/';
	$queryData = http_build_query($queryParams = array(
		'grant_type' => 'refresh_token',
		'client_id' => CLIENT_ID,
		'client_secret' => CLIENT_SECRET,
		'refresh_token' => $auth['refresh_token'],
	));

	writeToLog(Array('URL' => $queryUrl, 'PARAMS' => $queryParams), 'ImBot request auth data');

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $queryUrl.'?'.$queryData,
	));

	$result = curl_exec($curl);
	curl_close($curl);

	$result = json_decode($result, 1);
	if (!isset($result['error']))
	{
		$appsConfig = Array();
		if (file_exists(__DIR__.'/config.php'))
			include(__DIR__.'/config.php');

		$result['application_token'] = $auth['application_token'];
		$appsConfig[$auth['application_token']]['AUTH'] = $result;
		saveParams($appsConfig);
	}
	else
	{
		$result = false;
	}

	return $result;
}

/**
 * Write data to log file. (by default disabled)
 * WARNING: this method is only created for demonstration, never store log file in public folder
 *
 * @param mixed $data
 * @param string $title
 * @return bool
 */
function writeToLog($data, $title = '')
{
	if (!DEBUG_FILE_NAME)
		return false;

	$log = "\n------------------------\n";
	$log .= date("Y.m.d G:i:s")."\n";
	$log .= (strlen($title) > 0 ? $title : 'DEBUG')."\n";
	$log .= print_r($data, 1);
	$log .= "\n------------------------\n";

	file_put_contents(__DIR__."/".DEBUG_FILE_NAME, $log, FILE_APPEND);

	return true;
}
