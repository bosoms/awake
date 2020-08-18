<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

?>

<div class="row">
	<div class="col-sm-12 col-md-6">
		<div class="panel">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-8">
						<h3><b>Absentees</b></h3>
					</div>
				</div>
				<div class="row">
					<div class="col-md-8 pull-left">
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
					<table class="table table-striped table-bordered table-condensed table-hover">
						<thead>
							<tr>
								<th>Student Name</th>
								<th>Roll No</th>
								<th>Attendance</th>
							</tr>
						</thead>
						<tbody>
							<?php if ( count($absentees) > 0 ): ?>
							<?php foreach ( $absentees as $student ): ?>
							<tr>
								<td><?= $student->student->name ?></td>
								<td><?= $student->student->class_roll_no ?></td>
								<td>
									<span class="label label-danger">Absent</span>
									<!--
									<?= Html::a('Mark Present', ['attendance/delete', 'id' => $student->id], [
										'class' => 'btn btn-success btn-xs',
										'data-confirm' => 'Are you sure?',
										'data-method' => 'post',
									]) ?>
									-->
								</td>
							</tr>
							<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td colspan="3" class="text-center">All students are Present</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
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

$js .= '$(".alert").animate({opacity: 1.0}, 3000).fadeOut("slow");';

$this->registerJs($js, \yii\web\View::POS_READY);
?>
