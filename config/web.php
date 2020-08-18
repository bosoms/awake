<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db2 = require __DIR__ . '/db2.php';

$config = [
    'id' => 'basic',
	'name' => 'Awake',
	'timeZone' => 'Asia/Kolkata',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
		'log',
		'queue',
	],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
	'defaultRoute' => 'attendance',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'HAB_PIHblwOP1knCh8CzMB4OX0iwgWEI',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
			'transport' => [
				 'class' => 'Swift_SmtpTransport',
				 'host' => 'smtp.mailtrap.io',
				 'port' => '2525',
				 'encryption' => 'tls',
				 'username' => 'null',
				 'password' => 'null',
			 ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
		'session' => [
			'name' => 'PHPSESSID',
			'savePath' => __DIR__ . '/../runtime', // 'savePath' => '@runtime/session',
		],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
		'queue' => [
            'class' => \yii\queue\db\Queue::class,
			'as log' => \yii\queue\LogBehavior::class,
            'db' => 'db', // DB connection component or its config 
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex used to sync queries
        ],
    ],
	'modules' => [
		'gridview' => [
			'class' => 'kartik\grid\Module',
			// other module settings
		],
		'user' => [
			'class' => Da\User\Module::class,
			'administrators' => ['admin'],
			'mailParams' => [
				'fromEmail' => function() {
                    return [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
                }
			],
		],
	],
    'params' => $params,
];

if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'      => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.1'],
    ];
}

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.1'],
    ];
}

return $config;
