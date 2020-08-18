<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
?>

<div class="row">
	<div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <h3>Marked Attendances</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>First Hour</th>
                                <th>Second Hour</th>
                                <th>Third Hour</th>
                                <th>Fourth Hour</th>
                                <th>Fifth Hour</th>
                            </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $dates as $date => $logs ): ?>
							<tr>
								<td><?= Yii::$app->formatter->asDate($date) ?></td>
								<?php foreach ( [1,2,3,4,5] as $hour ): ?>
								<td>
								<?php foreach ( $logs as $log ): ?>
									<?php if ( $log->hour->id == $hour ): ?>
									<a href="<?= Url::to(['attendance/view', 'subject' => $log->subject->id, 'batch' => $log->batch->id, 'hour' => $log->hour->id, 'date' => $log->date]) ?>"><?= $log->batch->name . " " ?><?php $ordinal = array(1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth', 6 => 'Sixth'); ?><?= $ordinal[$log->batch->semester] . " Semester" ?></a><br>
									<?php endif; ?>
								<?php endforeach; ?>
								</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
	</div>
</div>
