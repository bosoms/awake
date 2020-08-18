<?php

use yii\helpers\Url;

/* @var $this yii\web\View */

?>

<div class="row">
	<div class="col-sm-12 col-md-6">
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="<?= Url::to(['attendance/index']); ?>" class="btn btn-primary">Back</a>
			</div>
		</div>
        <div class="panel">
            <div class="panel-heading">
                <h3><b><?= $subject->code . ": " . $subject->name ?></b></h3>
				<?php $ordinal = array(1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth', 6 => 'Sixth'); ?>
				<?= $batch->name . ' ' . $ordinal[$batch->semester] . " Semester" ?><br>
				<p class=text-right><?= Yii::$app->formatter->asDate($date, 'long'); ?></p>
				<p class=text-right><?= $hour->name ?></p>
            </div>
            <div class="panel-body">
				<p class=text-left>Absents: <?= count($attendances) ?></p>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Roll No</th>
                                <th width="20%">Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( count($students) > 0 ): ?>
                            
                            <?php foreach ( $students as $student ): ?>
                            <tr>
                                <td><?= $student->student->name ?></td>
                                <td><?= $student->student->class_roll_no ?></td>
								<td>
								<?php $absent = false; ?>
								<?php foreach ($attendances as $attendance): ?>
									<?php
										if ($student->student_id == $attendance->student_id) {
											$absent = true;
											continue;
										}
									?>
								<?php endforeach; ?>
								<?= $absent ? 
										'<span class="label label-danger">Absent</span>' : 
										'<span class="label label-success">Present</span>' ?>
								</td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="<?= Url::to(['attendance/index']); ?>" class="btn btn-primary">Back</a>
			</div>
		</div>
	</div>
</div>
