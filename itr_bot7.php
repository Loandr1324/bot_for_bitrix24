<?php
error_reporting(0);



#####################
### CONFIG OF BOT ###
#####################
define('DEBUG_FILE_NAME', 'log_openline_macardv7'); // if you need read debug log, you should write unique log name
define('CLIENT_ID', 'local.6145e359ef3036.36413432'); // like 'app.67efrrt2990977.85678329' or 'local.57062d3061fc71.97850406' - This code should take in a partner's site, needed only if you want to write a message from Bot at any time without initialization by the user
define('CLIENT_SECRET', 'VHR1A31412QKVASXOsy9sGZ09IAajO07kqtyPxT0Y4jbz6E5CT'); // like '8bb00435c88aaa3028a0d44320d60339' - TThis code should take in a partner's site, needed only if you want to write a message from Bot at any time without initialization by the user
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
	/*
    [event] => ONIMCOMMANDADD
    [data] => Array(
		[COMMAND] => Array
			(
				[66] => Array
					(
						[domain] => balance.bitrix24.ru
						[member_id] => ce3bbb9f2afd491ddbd3d73361d67ba8
						[application_token] => 67bcfebf221cab9c73b6daddd69cda1a
						[AUTH] => Array
							(
								[domain] => balance.bitrix24.ru
								[member_id] => ce3bbb9f2afd491ddbd3d73361d67ba8
								[application_token] => 67bcfebf221cab9c73b6daddd69cda1a
							)

						[BOT_ID] => 248
						[BOT_CODE] => newbot
						[COMMAND] => help
						[COMMAND_ID] => 66
						[COMMAND_PARAMS] => 
						[COMMAND_CONTEXT] => KEYBOARD
						[MESSAGE_ID] => 43428
					)

			)

            [PARAMS] => Array
                (
                    [FROM_USER_ID] => 1
                    [TO_CHAT_ID] => 1072
                    [MESSAGE] => /help 
                    [MESSAGE_TYPE] => C
                    [AUTHOR_ID] => 1
                    [CHAT_ENTITY_TYPE] => 
                    [CHAT_ENTITY_ID] => 
                    [DIALOG_ID] => chat1072
                    [MESSAGE_ID] => 43428
                    [CHAT_TYPE] => C
                    [LANGUAGE] => ru
                )

            [USER] => Array
                (
                    [ID] => 1
                    [NAME] => Лоик Андрей
                    [FIRST_NAME] => Андрей
                    [LAST_NAME] => Лоик
                    [WORK_POSITION] => Бизнес-аналитик
                    [GENDER] => M
                )

        )

    [ts] => 1631542555
    [auth] => Array
        (
            [access_token] => 2c6b3f6100573cac0046a09000000001000003284deba9ab1860a9e4928305840fd25f
            [expires] => 1631546156
            [expires_in] => 3600
            [scope] => im,imbot
            [domain] => balance.bitrix24.ru
            [server_endpoint] => https://oauth.bitrix.info/rest/
            [status] => L
            [client_endpoint] => https://balance.bitrix24.ru/rest/
            [member_id] => ce3bbb9f2afd491ddbd3d73361d67ba8
            [user_id] => 1
            [refresh_token] => 1cea666100573cac0046a090000000010000030b6a155d2cce02f857fbeab333113d8a
            [application_token] => 67bcfebf221cab9c73b6daddd69cda1a
        )
	*/
	
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
	/*
	if ($_REQUEST['data']['PARAMS']['CHAT_ENTITY_TYPE'] != 'LINES')
		return false;
	*/
	/*
	$result = restCommand('imbot.message.add', Array(

    'BOT_ID' => 39, // Идентификатор чат-бота, от которого идет запрос, можно не указывать, если чат-бот всего один
    'DIALOG_ID' => 1, // Идентификатор диалога, это либо USER_ID пользователя, либо chatXX - где XX идентификатор чата, передается в событии ONIMBOTMESSAGEADD и ONIMJOINCHAT
    'MESSAGE' => 'answer text', // Тест сообщения
    'ATTACH' => '', // Вложение, необязательное поле
    'KEYBOARD' => '', // Клавиатура, необязательное поле
    'MENU' => '', // Контекстное меню, необязательное поле 
    'SYSTEM' => 'N', // Отображать сообщения в виде системного сообщения, необязательное поле, по умолчанию 'N'
    'URL_PREVIEW' => 'Y' // Преобразовывать ссылки в rich-ссылки, необязательное поле, по умолчанию 'Y'

), $_REQUEST["auth"]);
	*/
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
		'CODE' => 'MaCarBotItr6',
		'TYPE' => 'O',
		'EVENT_HANDLER' => $handlerBackUrl,
		'OPENLINE' => 'Y',
		'PROPERTIES' => Array(
			'NAME' => 'MaCar бот6',
			'WORK_POSITION' => "Бот для открытой линии macardv.ru",
			'COLOR' => 'BLUE',
			'PERSONAL_PHOTO' => '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',
		)
	), $_REQUEST["auth"]);
	writeToLog($result, 'Результат регистрации бота: ');
	
	$botId = $result['result']; //сохраняем идентификатор бота в переменную botId
	

	//Регистрируем команды
		/*
	$result = restCommand('imbot.command.register', Array(
		'BOT_ID' => 62, // Идентификатор чат-бота владельца команды
		'COMMAND' => 'echo', // Текст команды, которую пользователь будет вводить в чатах
		'COMMON' => 'Y', // Если указан Y, то команда доступна во всех чатах, если N - то доступна только в тех, где присутствует чат-бот
		'HIDDEN' => 'N', // Скрытая команда или нет - по умолчанию N
		'EXTRANET_SUPPORT' => 'N', // Доступна ли команда пользователям Экстранет, по умолчанию N
		'CLIENT_ID' => '', // строковый идентификатор чат-бота, используется только в режиме Вебхуков
		'LANG' => Array( // Массив переводов, обязательно указывать, как минимум, для RU и EN
			Array('LANGUAGE_ID' => 'en', 'TITLE' => 'Get echo message', 'PARAMS' => 'some text'), // Язык, описание команды, какие данные после команды нужно вводить.
		),
		'EVENT_COMMAND_ADD' => 'http://www.hazz/chatApi/bot.php', // Ссылка на обработчик для команд

		), $_REQUEST["auth"]);
	*/
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
	/*!!!!! Не зачем регистрировать новую ссылку для обработчика, т.к. у нас это один и тот же файл
	$result = restCommand('event.bind', Array(
		'EVENT' => 'OnAppUpdate',
		'HANDLER' => $handlerBackUrl
	), $_REQUEST["auth"]);
	*/
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
else if ($_REQUEST['event'] == 'ONAPPUPDATE') // При этом событии пока ничего не происходит, можно что-то изменить
{
	// check the event - authorize this event or not
	if (!isset($appsConfig[$_REQUEST['auth']['application_token']]))
		return false;

	if ($_REQUEST['data']['VERSION'] == 2)
	{
		// Some logic in update event for VERSION 2
		// You can execute any method RestAPI, BotAPI or ChatAPI, for example delete or add a new command to the bot
		/*
		$result = restCommand('...', Array(
			'...' => '...',
		), $_REQUEST["auth"]);
		*/

		/*
		For example delete "Echo" command:

		$result = restCommand('imbot.command.unregister', Array(
			'COMMAND_ID' => $appsConfig[$_REQUEST['auth']['application_token']]['COMMAND_ECHO'],
		), $_REQUEST["auth"]);
		*/
	}
	else
	{
		// send answer message
		$result = restCommand('app.info', array(), $_REQUEST["auth"]);
	}

	// write debug log
	writeToLog($result, 'ImBot update event');
}

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