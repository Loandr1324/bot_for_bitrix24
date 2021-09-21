<?php
error_reporting(0);



#####################
### CONFIG OF BOT ###
#####################
define('DEBUG_FILE_NAME', 'log_openline_macardv'); // if you need read debug log, you should write unique log name
define('CLIENT_ID', 'local.6145e359ef3036.36413432'); // like 'app.67efrrt2990977.85678329' or 'local.57062d3061fc71.97850406' - This code should take in a partner's site, needed only if you want to write a message from Bot at any time without initialization by the user
define('CLIENT_SECRET', 'VHR1A31412QKVASXOsy9sGZ09IAajO07kqtyPxT0Y4jbz6E5CT'); // like '8bb00435c88aaa3028a0d44320d60339' - TThis code should take in a partner's site, needed only if you want to write a message from Bot at any time without initialization by the user
#####################




writeToLog($_REQUEST, 'ImBot Event Query');

$appsConfig = Array();
if (file_exists(__DIR__.'/config.php'))
	include(__DIR__.'/config.php');

// receive event "new message for bot"
if ($_REQUEST['event'] == 'ONIMBOTMESSAGEADD')
{
	// check the event - authorize this event or not
	if (!isset($appsConfig[$_REQUEST['auth']['application_token']]))
		return false;

	//Проверка, что обращение с открытой линии
	if ($_REQUEST['data']['PARAMS']['CHAT_ENTITY_TYPE'] != 'LINES')
		return false;

	return false;
	/*
	$result = restCommand('imbot.message.add', Array(
		"DIALOG_ID" => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
		"MESSAGE" => "Скорее всего Вы написали что-то гениальное, но я скромный бот и не могу поддержать такую беседу.\n Я смогу Вам помочь только если Вы нажмёте на кнопку с вопросом.\n Если Вы хотите пообщаться, то Вам надо дождаться ответа человека ;).",
		"KEYBOARD" => Array(
			Array(
				"TEXT" => "Показать вопросы",
				"COMMAND" => "help",
				"BG_COLOR" => "#29619b",
				"TEXT_COLOR" => "#fff",
				"DISPLAY" => "LINE",
			),
		),
	), $_REQUEST["auth"]);
	*/
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

	if ($_REQUEST['data']['PARAMS']['CHAT_ENTITY_TYPE'] != 'LINES')
		return false;
	
	//Передаём приветственное сообщение с кнопкой перехода в Главное меню
	$result = restCommand('imbot.message.add', Array(
		"BOT_ID" => $_REQUEST['data']['PARAMS']['BOT_ID'],
		"DIALOG_ID" => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
		"MESSAGE" => "Здравствуйте, я бот [B]MaCar[/B].\n Пока Вы ожидаете ответа консультанта, я могу ответить на стандартные вопросы.\n Нажмите на кнопку под сообщением.",
		"KEYBOARD" => Array(
			Array(
				"TEXT" => "Что ты умеешь?",
				"LINK" => "help",
				"BG_COLOR" => "#29619b",
				"TEXT_COLOR" => "#fff",
				"DISPLAY" => "LINE",
			),
		),
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
		'CODE' => 'MaCarBotItr',
		'TYPE' => 'O',
		'EVENT_MESSAGE_ADD' => $handlerBackUrl,
		'EVENT_WELCOME_MESSAGE' => $handlerBackUrl,
		'EVENT_BOT_DELETE' => $handlerBackUrl,
		'OPENLINE' => 'Y',
		'PROPERTIES' => Array(
			'NAME' => 'MaCar бот',
			'WORK_POSITION' => "Бот для открытой линии macardv.ru",
			'COLOR' => 'RED',
			'PERSONAL_PHOTO' => base64_encode(file_get_contents(__DIR__.'/MaCar avatar.jpg')),
		)
	), $_REQUEST["auth"]);
	writeToLog($result, 'Результат регистрации бота: ');
	
	$botId = $result['result']; //сохраняем идентификатор бота в переменную botId
	
	/*!!!!! Не зачем регистрировать новую ссылку для обработчика, т.к. у нас это один и тот же файл
	$result = restCommand('event.bind', Array(
		'EVENT' => 'OnAppUpdate',
		'HANDLER' => $handlerBackUrl
	), $_REQUEST["auth"]);
	*/
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
		'HIDDEN' => 'N',
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
		'HIDDEN' => 'N',
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
	writeToLog(Array($botId), 'ImBot register');
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

	writeToLog(Array('URL' => $queryUrl, 'PARAMS' => array_merge($params, array("auth" => $auth["access_token"]))), 'ImBot send data');

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