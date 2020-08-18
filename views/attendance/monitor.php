<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use conquer\select2\Select2Widget;

/* @var $this yii\web\View */

?>

<div class="panel">
	<div class="panel-heading">
		<div class="row">
			<div class="col-sm-8">
				<h3><b>Attendance Monitor</b></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 pull-left">
				<?= Yii::$app->formatter->asDate($date, 'long'); ?>
			</div>
			<div class="col-md-4 text-right">
				<div id="digital-clock">
					<?php
						date_default_timezone_set('Asia/Kolkata');
						echo $timestamp = date('h:i:s a');
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr>
					<th scope="col">
						<?php
						$form = ActiveForm::begin([
							'id' => 'batch-form',
							'method' => 'get',
							'action' => Url::to(['attendance/monitor']),
							'options' => ['class' => 'form-horizontal'],
						]) ?>
							<?php
							echo Select2Widget::widget([
								'name' => 'semester',
								'placeholder' => ' - Select Semester - ',
								'items' => [
									'' => '',
									1 => 'First Semester',
									2 => 'Second Semester',
									3 => 'Third Semester',
									4 => 'Fourth Semester',
									5 => 'Fifth Semester',
									6 => 'Sixth Semester',
								],
								'value' => $semester,
								'class' => 'form-control',
								'settings' => [
									'allowClear' => true,
									'required' => true,
								],
								'options' => [
									'onchange' => 'this.form.submit()',
									'style' => 'width:200px;',
								],
							]); ?>
							<?= Html::hiddenInput('date', $date); ?>
						<?php ActiveForm::end() ?>
					</th>
					<?php foreach ($hours as $hour): ?>
					<th scope="col"><?= $hour->name ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($batches as $batch): ?>
				<?= '<tr>' ?>
				<?php $ordinal = array(1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth', 6 => 'Sixth'); ?>
				<?= '<th scope="row">' . $batch->name . ' ' . $ordinal[$batch->semester] . " Semester" . '</th>' ?>
				<?php foreach ($hours as $hour): ?>
					<?php $content = []; ?>
					<?php foreach ($attendancesMarked as $batchLog): ?>
						<?php if ($batch->id == $batchLog->id): ?>
						<?php foreach ($batchLog->logAttendances as $log): ?>
						<?php
							if ($hour->id == $log->hour_id) {
								$content[$log->faculty->id] = $log;
							}
						?>
						<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php if (!empty($content)): ?>
						<td class="bg-success">
						<?php foreach ($content as $log): ?>
							<a href="<?= Url::to(['attendance/view', 'subject' => $log->subject->id, 'batch' => $log->batch->id, 'hour' => $log->hour->id, 'date' => $log->date]) ?>"><?= $log->faculty->profile->name . '<br>(' . date('h:i a', strtotime($log->created_at)) . ')' ?></a><br>
						<?php endforeach; ?>
						</td>
					<?php else: ?>
						<td class="bg-danger"></td>
					<?php endif; ?>
				<?php endforeach; ?>
				<?= '</tr>' ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		</div>
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
