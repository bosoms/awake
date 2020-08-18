<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
	<link rel="icon" href="<?= Url::to('@web/favicon.ico') ?>" type="image/x-icon" />
	<script src="https://cdn.rawgit.com/HubSpot/pace/v1.0.0/pace.min.js"></script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
	$module = Yii::$app->getModule('user');
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            [
				'label' => 'Switch to Admin',
				'url' => ['/user/admin/switch-identity'],
				'linkOptions' => ['data-method' => 'post'],
				'visible' => Yii::$app->session->has($module->switchIdentitySessionKey)
			],
            ['label' => 'Mark Attendance', 'url' => ['/attendance/index'], 'visible' => !Yii::$app->user->isGuest],
			['label' => 'View Attendance', 'url' => ['/attendance/list'], 'visible' => !Yii::$app->user->isGuest],
			['label' => 'Assign Subjects', 'url' => ['/attendance/subject-map'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin],
            ['label' => 'Monitor', 'url' => ['/attendance/monitor'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/user/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/user/logout'], 'post')
                . Html::submitButton(
                    'Logout',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
		<noscript>
			<style type="text/css">
				.page-content {display:none;}
			</style>
			<div class="noscriptmsg">
				<div class="alert alert-danger" role="alert">
					Sorry, your browser does not support JavaScript!
				</div>
			</div>
		</noscript>
		
        <?= Alert::widget() ?>
		
        <div class="page-content">
			<?= $content ?>
		</div>
    </div>
</div>

<?php
$this->registerJs('
	var AttendanceSubmitButton = document.getElementById("attndnc-submit-btn");
	window.onload = function() {
		if (window.jQuery) {
			// jQuery is loaded
			console.log("jQuery is loaded");
		} else {
			// jQuery is not loaded
			// Check if attendance submit button exists
			if (AttendanceSubmitButton!=null) {
				// Disable the attendance submit button if present
				AttendanceSubmitButton.disabled = true;
			}
		}
	}
	window.onerror = function(error) {
		// JavaScript or jQuery Error Occurred
		// Check if attendance submit button exists
		if (AttendanceSubmitButton!=null) {
			// Disable the attendance submit button if present
			AttendanceSubmitButton.disabled = true;
		}
	}',
    \yii\web\View::POS_END,
    'jQueryLoadedError'
);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>