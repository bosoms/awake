<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
	'timeZone' => 'Asia/Kolkata',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
		'log',
		'queue',
	],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
		'authManager'  => [
			'class' => 'yii\rbac\DbManager',
			// 'defaultRoles' => ['guest'],
		],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'db' => $db,
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
		'user' => [
			'class' => Da\User\Module::class,
			'enableEmailConfirmation' => false,
			'enableRegistration' => false,
		],
	],
    'params' => $params,
	'controllerMap' => [
		/*
		'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
		*/
		'migrate' => [
			'class' => 'yii\console\controllers\MigrateController',
			'migrationPath' => null,
			'migrationNamespaces' => [
				// ...
				'yii\queue\db\migrations',
			],
		],
	],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
