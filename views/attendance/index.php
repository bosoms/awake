<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Student Attendance';
?>

<!--
<div id="notification-panel" class="panel panel-primary">
	<div class="panel-heading">Notification
		<button type="button" class="close" data-target="#notification-panel" data-dismiss="alert">
			<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
		</button>
	</div>
	<div class="panel-body">
		In order to comply with the government order, attendance marking time slot has been adjusted for <strong>Friday</strong>. Please note the change in time slots.
	</div>
</div>
-->

<div class="page-header">
<?php if ( !empty($hour) ): ?>
	<h2><?= $hour->name ?> <small><?= date('g:i A', strtotime($hour->start_time)) ?> - <?= date('g:i A', strtotime($hour->end_time)) ?></small></h2>
<?php endif; ?>
	<?php date_default_timezone_set('Asia/Kolkata'); ?>
	<small><?= date('l jS \of F Y ') ?><span id="digital-clock"><?= date('h:i:s a') ?></span></small>
</div>

<div class="box">
	<div class="row">
		<?php foreach ( $subjects as $subjectName => $subjectModels ): ?>
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<div class="box-part text-center">
				<div class="title">
					<h3><?= $subjectName ?></h3>
				</div>
				<div class="text">
					<span><?php // $subjectModel->subject->code ?></span>
				</div>
				
				<div class="batches buttons-wrap">
				<?php foreach ($subjectModels as $subjectModel): ?>
					
					<?php foreach ($subjectModel->batchSubjects as $batch): ?>
					
						<a href="<?= Url::toRoute(['attendance/mark', 'subject' => $subjectModel->id, 'batch' => $batch->batch_id]); ?>" class="btn btn-primary btn-round">
							<?php $ordinal = array(1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth', 6 => 'Sixth'); ?>
							<div class="select-class-btn">
								<span class="courseText"><?= $batch->batch->name ?></span>
								<span class="batchText"><?= $ordinal[$batch->batch->semester] . " Semester" ?></span>
							</div>
							
						</a>
					
					<?php endforeach; ?>
					
				<?php endforeach; ?>
				</div>
				
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<?php
$js = '$(document).ready(function() {
		  clockUpdate();
		  setInterval(clockUpdate, 1000);
		})

		function clockUpdate() {
		  var date = new Date();
		  
		  function addZero(x) {
			if (x < 10) {
			  return x = "0" + x;
			} else {
			  return x;
			}
		  }

		  function twelveHour(x) {
			if (x > 12) {
			  return x = x - 12;
			} else if (x == 0) {
			  return x = 12;
			} else {
			  return x;
			}
		  }

		  var h = addZero(twelveHour(date.getHours()));
		  var m = addZero(date.getMinutes());
		  var s = addZero(date.getSeconds());
		  var a = date.getHours() >= 12 ? "pm" : "am";

		  $("#digital-clock").text(h + ":" + m + ":" + s + " " + a)
		}';

$this->registerJs($js, \yii\web\View::POS_READY);
?>
