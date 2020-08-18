<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use conquer\select2\Select2Widget;
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Assign Subjects to Faculty</h3>
	</div>

	<div class="panel-body">
	<?php
		$form = ActiveForm::begin([
			'id' => 'subject-map-form',
		]);
		
		echo '<div class="form-group">';
		echo Html::label('Faculty', 'faculty_id', ['class' => 'control-label']);
		echo Select2Widget::widget([
			'name' => 'faculty_id',
			// 'multiple' => false,
			'placeholder' => '-Select a Faculty-',
			'items'=>ArrayHelper::merge(['' => ''], ArrayHelper::map($departmentFaculties, 'id', 'profile.name')),
			'id' => 'faculty',
			'class' => 'form-control adjust',
			'settings' => [
				'allowClear' => true,
				'required' => true,
			],
		]);
		echo '</div>';
		
		echo '<input type="hidden" value="" name="subjects_data" id="subjects_data" />';
		
		ActiveForm::end();
	?>
	</div>
</div>

<div id="subjects" class="row">
</div>

<!-- Modal -->
<div class="modal fade" id="subjectSubmitModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Select Subject</h4>
      </div>
      <div class="modal-body">
		<div id="subjectMapWrapper" class="row">
			<div id="subjectMapWidget" class="col-xs-12">
			<?php
				echo '<div class="form-group">';
				echo Html::label('Assign a Subject', 'subject_map', ['class' => 'control-label']);
				echo Select2Widget::widget([
					'options' => [
						'placeholder' => '-Select a Subject-',
					],
					'name' => 'subject_map',
					'placeholder' => '-Select a Subject-',
					'id' => 'subject_map',
					'class' => 'form-control adjust',
					'ajax' => ['attendance/search-subject'],
					'events' => [
						'select2:open' => "function() { console.log('open'); }",
					],
					'settings' => [
						'ajax' => ['delay' => 250],
						'minimumInputLength' => 1,
						'minimumResultsForSearch' => -1,
					],
				]);
				echo '</div>';
			?>
			</div>
			<div id="subjectMapResult" class="col-xs-12">
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php
$js = '
	var jsn = [];
	$(document).on("click", "body .subject-remove-btn", function(){
		var subjectID = $(this).data("id");
		var subjectCode = $(this).data("code");
		var subjectName = $(this).data("name");
		var filtered = jsn.filter(function(value, index, arr){
			return value[0] != subjectID;
		});
		jsn = filtered;
		$(this).closest(".col-xs-6.col-sm-6.col-md-4").fadeOut();
	});
	$(document).on("change", "#subject_map", function(){
		var obj = $("#subject_map").select2("data");
		// $("#example").select2("data").element[0].attributes["data-name"].value
		var subjectID = obj[0].id;
		var subjectCode = obj[0].code;
		var subjectName = obj[0].text;
		
		// $( codetoadd ).insertAfter( "#subjectMapWrapper" );
		$( "div#subjectMapResult" ).empty().append( "<div class=\"panel panel-primary\"><div class=\"panel-heading\"><h3 class=\"panel-title\">"+subjectCode+"</h3></div><div class=\"panel-body\"><p>"+subjectName+"</p><div class=\"text-right\"><button type=\"button\" data-id=\""+subjectID+"\" data-code=\""+subjectCode+"\" data-name=\""+subjectName+"\" class=\"btn btn-primary subject-map-select-btn\"><i class=\"glyphicon glyphicon-plus\"></i></button></div></div></div>" );
	});
	$(document).on("click", "body .subject-map-select-btn", function(){
		var subjectID = $(this).data("id");
		var subjectCode = $(this).data("code");
		var subjectName = $(this).data("name");
		var codetoadd = "<div class=\"col-xs-6 col-sm-6 col-md-4\"><div class=\"subject text-center\"><div class=\"title\"><h3>"+subjectCode+"</h3></div><div class=\"text\"><span>"+subjectName+"</span></div><div class=\"button\"><button data-id=\""+subjectID+"\" data-code=\""+subjectCode+"\" data-name=\""+subjectName+"\" type=\"button\" class=\"btn btn-link btn-sm subject-remove-btn\"><i class=\"glyphicon glyphicon-remove\"></i></button></div></div></div>";
		// $( "div#subjects" ).append( codetoadd );
		$( codetoadd ).insertBefore( "#modalBtnWrapper" );
		jsn.push([subjectID, subjectCode, subjectName]);
		$("#subjectSubmitModal").modal("hide");
	});
	$(document).on("change", "#faculty", function(){
		// Ref: https://stackoverflow.com/questions/21648356/
		$.ajax({
			type: "POST",
			url: "' . Yii::$app->urlManager->createUrl('attendance/fetch-subject?facultyId=') . '"+$(this).val(),
			beforeSend: function() {
				$( "div#subjects" ).empty();
				jsn = [];
			},
			success: function(data) {
				var cards = $();
				// Store all the subject nodes
				JSON.parse(data).forEach(function(item, i) {
					cards = cards.add("<div class=\"col-xs-6 col-sm-6 col-md-4\"><div class=\"subject text-center\"><div class=\"title\"><h3>"+item[1]+"</h3></div><div class=\"text\"><span>"+item[2]+"</span></div><div class=\"button\"><button data-id=\""+item[0]+"\" data-code=\""+item[1]+"\" data-name=\""+item[2]+"\" type=\"button\" class=\"btn btn-link btn-sm subject-remove-btn\"><i class=\"glyphicon glyphicon-remove\"></i></button></div></div></div>");
					jsn.push([item[0], item[1], item[2]]);
				});
				cards = cards.add("<div id=\"modalBtnWrapper\" class=\"col-xs-12 col-sm-12 col-md-4\"><div class=\"subject text-center\"><div class=\"text\"><button type=\"button\" class=\"btn btn-success btn-lg\" data-toggle=\"modal\" data-target=\"#subjectSubmitModal\"><span class=\"glyphicon glyphicon-plus\"></span></button></div></div></div><div class=\"col-xs-12\"><div class=\"form-group\"><button id=\"subject-map-submit-btn\" type=\"submit\" class=\"btn btn-default btn-lg btn-block\">Save</button></div></div>");
				$(function() {
					// $("body").append(cards);
					$( "div#subjects" ).append( cards );
				});
			},
			error: function(xhr) { // if error occured
				alert("Error occured. please try again");
			},
			complete: function() {
			},
			dataType: "html"
		});
	});
	$(document).on("click", "#subject-map-submit-btn", function(event){
			
		event.preventDefault();
		event.stopPropagation();
		
		var form = $("#subject-map-form");
		
		var data = {
			"faculty" : $("#faculty").val(),
			"subjects" : jsn,
		}

		$("#subjects_data").val( JSON.stringify(data));
		
		form.submit();
	});
';

$this->registerJs($js, \yii\web\View::POS_READY);
?>
