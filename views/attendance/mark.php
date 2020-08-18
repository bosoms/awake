<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

?>

<div class="row">
	<div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-8">
                        <h3><b><?= $subject->code . ": " . $subject->name ?></b></h3>
						<span>
							<b>
								<?= $batch->name . ' ' ?>
								<?php $ordinal = array(1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth', 6 => 'Sixth'); ?>
								<?= $ordinal[$batch->semester] . " Semester" ?>
							</b>
						</span>
                    </div>
                    <br>
                    <div class="col-sm-4">
                        <?= Yii::$app->formatter->asDate('now', 'long'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4">
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
                <div class="">
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Roll No</th>
                                <th>Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( count($students) > 0 ): ?>
                            <?php $i = 0; ?>
                            <?php foreach ( $students as $student ): ?>
                            <tr>
                                <td><?= $student->student->name ?></td>
                                <td><?= $student->student->class_roll_no ?></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-block btn-mark-attendance idx<?= $i ?>" 
                                        title="Mark For Attendance" 
										id="stud-<?= $student->student_id ?>" 
										data-idx="<?= $i ?>" 
										data-id="<?= $student->student_id ?>" 
										data-student-name="<?= $student->student->name ?>" 
										data-student-roll-number="<?= $student->student->class_roll_no ?>" 
                                        data-toggle="button" aria-pressed="false" autocomplete="off">
                                        Present
                                    </button>
                                </td>
                            </tr>
							<?php $i++; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-4" >
                        
						<?php
							$form = ActiveForm::begin(
								[
									'options' => [
										'id' => 'mark-attendance',
										'class' => 'form-horizontal',
									 ],
									 'fieldConfig' => [
										'options' => [
											'tag' => false,
										],
									],
								]
							);
						?>
							<fieldset>
							
								<input type="hidden" value="" name="attn_data" id="attn_data" />

								<div class="form-group">
                                    <div class="col-md-12">
										<?= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-block', 'id' => 'attndnc-submit-btn']) ?>
                                    </div>
                                </div>
							</fieldset>

						<?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<?php $this->registerJsFile('@web/js/sweetalert.min.js'); ?>

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

$studentIds = '';
foreach ($students as $student) {
	$studentIds .= $student->student_id . " : false, ";
}

$studentIndex = '';
for ( $i=0; $i<count($students); $i++ ) {
	$studentIndex .= $i . " : false, ";
}

$js .= 'var ids = {
			' . $studentIds . '
		};
		
		var index = {
			' . $studentIndex . '
		};
		
		var head_count = 0;
		
		$(document).on("click", "body .btn-mark-attendance", function(){
			toggle_present($(this).data("idx"), $(this).data("id"));
		});
		
		$(document).on("click", "#attndnc-submit-btn", function(event){
			
			event.preventDefault();
			event.stopPropagation();
			
			var form = $(this).parents("form");
			
			var i;

			var jsn = [];
			
			var list = "<ul class=\"list-unstyled\">";
			
			for(var i in ids) {
				if ( ids[i] == true ) {
					jsn.push([i, ids[i]]);
				}
			}
			
			for(var i in index) {
				if ( index[i] == true ) {
					var btn = $("button.idx"+i);
					// var btn = $("button[data-idx=\""+i+"\"]");
					var studName = btn.data("student-name");
					var studRollNumber = btn.data("student-roll-number").toString();
					list += "<li>" + studName + " (" + studRollNumber + ")</li>";
				}
			}
			
			list += "</ul>";
			
			const wrapper = document.createElement("div");
			wrapper.innerHTML = "Number of absentees: " + head_count + "<br><br>" + list;
			
			swal({
				title: "Absentees",
				content: wrapper,
				buttons: true,
			})
			.then((confirm) => {
				if (confirm) {
					// form is submitting
					var data = {
						"date" : $("#attn_date").val(),
						"hour" : $("#mark_count").val(),
						"attn": jsn,
						"code" : "{{$sub_code}}",
						"batch_no":"{{$batch_no}}"
					}

					$("#attn_data").val( JSON.stringify(data) );
					
					// disable the submit button on form submit
					// (to prevent multiple submit causing the error in backend)
					$("#attndnc-submit-btn").prop("disabled", true);
					
					form.submit();
				} else {
					return false;
				}
			});
		});
		
		function toggle_present(idx, id) {
			var btn = "#stud-"+id;
			// var idx = $("#stud-"+id).data("idx");
			if( ids[id] == true ) {
				$(btn).html("Present");
				$(btn).button("toggle");
				ids[id] = false;
				index[idx] = false;
				$(btn).toggleClass("btn-danger");
				$(btn).toggleClass("btn-success");
				head_count--;
			} else {
				$(btn).button("toggle");
				$(btn).html("Absent");
				ids[id] = true;
				index[idx] = true;
				$(btn).toggleClass("btn-danger");
				$(btn).toggleClass("btn-success");
				head_count++;
			}
		}';

$this->registerJs($js, \yii\web\View::POS_READY);
?>
