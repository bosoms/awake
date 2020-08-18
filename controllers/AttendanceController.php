<?php

namespace app\controllers;

use Yii;
use yii\base\Model;
use \yii\db\ActiveQuery;
use app\models\StudentSubjectAttendance;
use app\models\FacultySubject;
use app\models\BatchSubject;
use app\models\BatchStudent;
use app\models\StudentSubject;
use app\models\Student;
use app\models\Subject;
use app\models\Batch;
use app\models\Hour;
use app\models\LogAttendance;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use conquer\select2\Select2Action;

/**
 * AttendanceController implements the CRUD actions for StudentSubjectAttendance model.
 */
class AttendanceController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
			'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'mark', 'view', 'edit', 'list', 'monitor', 'fetch-subject', 'subject-map'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'mark', 'view', 'edit', 'list', 'monitor', 'fetch-subject', 'subject-map'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
	
	public function actions()
    {
        return [
            'ajax' => [
                'class' => Select2Action::className(),
                'dataCallback' => [$this, 'searchSubject'],
            ],
        ];
    }

    /**
     * Creates a new StudentSubjectAttendance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionMark($subject, $batch)
    {
		$subjectModel = Subject::find()->where(['id' => $subject])->one();
		
		$batchModel = Batch::find()->where(['id' => $batch])->one();
		
		$students = StudentSubject::find()
					->joinWith(['student'])
					->where(['student_subject.subject_id' => $subject, 'student_subject.batch_id' => $batch, 'student.is_active' => 1])
					->orderBy(['class_roll_no' => SORT_ASC])
					->all();
		
		$hours = Hour::find()->all();
		
		foreach ($hours as $hourModel) {
			$date1 = new \DateTime();
			$date2 = \DateTime::createFromFormat('H:i:s', $hourModel->start_time);
			$date3 = \DateTime::createFromFormat('H:i:s', $hourModel->end_time);
			if ($date1 > $date2 && $date1 < $date3) {
				$hour = $hourModel->id;
				$start_time = $hourModel->start_time;
			}
		}
		
		if (YII_ENV_DEV) {
			if (!isset($hour) || $hour === null) {
				$hour = 1;
			}
		}
		
		if (!isset($hour) || $hour === null) { // current time is beyond the working hours
			Yii::$app->session->setFlash('danger', 'Please mark attendance during the class time.');
			return $this->redirect(\Yii::$app->request->getReferrer());
		}
		
		$to_time = time();
		$from_time = strtotime($start_time);
		$diff_minute = round(abs($to_time - $from_time) / 60,2);
		
		if ($diff_minute > 15) {
            Yii::$app->session->setFlash('danger', 'Attendance is locked. Please contact the administrator for any changes.');
			return $this->redirect(\Yii::$app->request->getReferrer());
		}
		
		/* check if attendance already marked
		 * for the batch
		 * with the provided subject
		 * on the provided hour
		 * by any faculty
		 */
		$log = LogAttendance::find()
				->select('faculty_id')
				->where([
					'date' => date('Y-m-d'),
					'hour_id' => $hour,
					'batch_id' => $batch,
					'subject_id' => $subject,
				])->all();
		
		
		if (!empty($log)) {
			
			$faculty = [];
			
			foreach ($log as $entry) {
				$faculty[] = $entry->faculty->profile->name;
			}
			
			if (in_array(Yii::$app->user->identity->profile->name, $faculty)) {
				Yii::$app->session->setFlash('danger', 'You have already marked attendance for this hour.');
			} else {
				$message = '<strong>' . 
							implode(' & ', $faculty) . 
							'</strong> ' . 
							(count($faculty)>1?'have':'has') . 
							' already marked attendance for this hour in this class.';
				Yii::$app->session->setFlash('danger', $message);
			}
			
			return $this->redirect(\Yii::$app->request->getReferrer());
		}
		
		if (Yii::$app->request->post()) {
			
			$data = Yii::$app->request->post();
			
			// Validate POST data
			if (empty($data['attn_data'])) {
				Yii::$app->session->setFlash('danger', 'There has been an error while processing the attendance. Please check your internet connection and try again. If the issue persists, please try later.');
				return $this->redirect(\Yii::$app->request->getReferrer());
			}
			
			$data = json_decode($data['attn_data']);
			foreach($data->attn as $student) {
				$rows[] = [
					'id' => NULL,
					'student_id' => $student[0],
					'batch_id' => $batch,
					'subject_id' => $subject,
					'hour_id' => $hour,
					'date' => date('Y-m-d'),
					'reason' => '',
					'leavetype_id' => 1
				];
			}
			
			$log = new LogAttendance;
			
			$log->id = NULL;
			$log->date = date('Y-m-d');
			$log->hour_id = $hour;
			$log->faculty_id = Yii::$app->user->id;
			$log->batch_id = $batch;
			$log->subject_id = $subject;
			
			if ($log->validate()){
				$log->save();
			} else {
				if($log->hasErrors()){
					$errors = $log->getFirstErrors();
					Yii::$app->session->setFlash('danger', $errors);
					return $this->redirect(\Yii::$app->request->getReferrer());
				}
			}
			
			if (!empty($rows)) {
				$attendanceModel = new StudentSubjectAttendance;
				
				$rowModels = [];
				foreach ($rows as $row) {
					$rowModels[] = new StudentSubjectAttendance();
				}
				
				if (Model::loadMultiple($rowModels, $rows, false) && Model::validateMultiple($rowModels)){
					Yii::$app->db->createCommand()->batchInsert(StudentSubjectAttendance::tableName(), $attendanceModel->attributes(), $rows)->execute();
				} else {
					foreach($rowModels as $model){
						if($model->hasErrors()){
							$errors = $model->getFirstErrors();
							Yii::$app->session->setFlash('danger', $errors);
							return $this->redirect(\Yii::$app->request->getReferrer());
						}
					}
				}
			}
			
			Yii::$app->session->setFlash('success', "Attendance marked successfully.");
			
			return $this->redirect(['edit', 'faculty' => Yii::$app->user->id, 'subject' => $subject, 'batch' => $batch, 'hour' => $hour, 'date' => date('Y-m-d')]);
		}

        return $this->render('mark', [
            'subject' => $subjectModel,
			'batch' => $batchModel,
            'students' => $students,
        ]);
    }

    /**
     * Lists all FacultySubject models.
     * @return mixed
     */
    public function actionIndex()
    {
		$hour = '';
		
		$hours = Hour::find()->all();
		
		foreach ($hours as $hourModel) {
			$date1 = new \DateTime();
			$date2 = \DateTime::createFromFormat('H:i:s', $hourModel->start_time);
			$date3 = \DateTime::createFromFormat('H:i:s', $hourModel->end_time);
			if ($date1 > $date2 && $date1 < $date3) {
				$hour = $hourModel;
			}
		}
		
		$subjectList = Subject::find()
							->joinWith('facultySubjects', 'batchSubjects')
							->where(['faculty_id' => Yii::$app->user->id])
							->all();
		
		$subjects = [];
		
		/* Grouping by the subjects */
		foreach($subjectList as $subject){
            $subjects[$subject->name][] = $subject;
        }
		
        return $this->render('index', [
            'subjects' => $subjects,
            'hour' => $hour,
        ]);
    }
	
	public function actionEdit($date, $hour, $faculty, $subject, $batch)
	{
		// TODO
		// Absentees from split classes are not separated.
		// Faculty will be able to view and edit all absentees of a batch irrespective of the faculty who marked the attendance.
		// Most probable case: Malayalam/Hindi separation
		
		$absentees = StudentSubjectAttendance::find()
						->joinWith('student')
						->where([
							'student_subject_attendance.batch_id' => $batch, 
							'student_subject_attendance.subject_id' => $subject, 
							'student_subject_attendance.hour_id' => $hour, 
							'student_subject_attendance.date' => $date
						])
						->orderBy(['student.class_roll_no' => SORT_ASC])
						->all();
		
        return $this->render('edit', [
            'absentees' => $absentees,
        ]);
	}
	
	public function actionList()
	{
		$logs = LogAttendance::find()
						->with('hour', 'batch', 'subject')
						->where(['>=', 'date', date('Y-m-d', mktime(0, 0, 0, date('n')-1, 1, date('Y')))])
						->andWhere(['faculty_id' => Yii::$app->user->id])
						->all();
		
		$logsByDate = [];
		
		/* Grouping the logs by the dates */
		foreach($logs as $log){
            $logsByDate[$log->date][] = $log;
        }
		
		return $this->render('list', [
            'dates' => $logsByDate,
        ]);
	}
	
    public function actionMonitor($date = null, $semester = null)
    {
		if ( empty($date) )
			$date = date('Y-m-d');
		
		if ( empty($semester) )
			$batches = Batch::find()->all();
		else
			$batches = Batch::find()->where(['semester' => $semester])->all();
		
		$hours = Hour::find()->all();
								
		$attendancesMarked = Batch::find()
							->joinWith(['logAttendances' => function (ActiveQuery $query) use ($date) {
								return $query
									->andWhere(['=', 'log_attendance.date', $date]);
							}])
							->all();
		
        return $this->render('monitor', [
            'date' => $date,
            'hours' => $hours,
            'batches' => $batches,
            'semester' => $semester,
			'attendancesMarked' => $attendancesMarked,
        ]);
    }
	
	public function actionFetchSubject($facultyId)
	{
		$subjects = FacultySubject::find()->where(['faculty_id' => $facultyId])->all();
		
		$subjectJson = [];
		
		if (!empty($subjects)) {
			foreach($subjects as $subjectModel) {
				$subjectJson[] = [$subjectModel->subject->id, $subjectModel->subject->code, $subjectModel->subject->name];
			}
		}
		
		return json_encode($subjectJson);
	}
	
	public function actionSearchSubject($q)
	{
		$query = new ActiveQuery(Subject::className());
		
        $return =  [
            'results' =>  $query->select([
                    'id as id',
					'CONCAT(code, " - ", name) as text',
                    'code as code',
                ])
                ->filterWhere(['like', 'name', $q])
				->orFilterWhere(['like', 'code', $q]) 
                ->asArray()
                ->limit(20)
                ->all(),
        ];
		
		return $this->asJson($return);
	}
	
    /**
     * Displays a single StudentSubjectAttendance model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($subject, $batch, $hour, $date)
    {
		/* check if the attendance log exists */
		$exists = LogAttendance::find()->where(['subject_id' => $subject, 'batch_id' => $batch, 'hour_id' => $hour, 'date' => $date])->exists();
		
		$hourModel = Hour::find()->where(['id' => $hour])->one();
		
		$batchModel = Batch::find()->where(['id' => $batch])->one();
		
		if ( $exists ) {
			
			$subjectModel = Subject::find()->where(['id' => $subject])->one();
			
			$students = StudentSubject::find()
						->joinWith(['student'])
						->where(['student_subject.subject_id' => $subject, 'student_subject.batch_id' => $batch, 'student.is_active' => 1])
						->orderBy(['class_roll_no' => SORT_ASC])
						->all();
			
			$attendances = StudentSubjectAttendance::find()
							->where(['batch_id' => $batch, 'subject_id' => $subject, 'hour_id' => $hour, 'date' => $date])
							->all();
			
			return $this->render('view', [
				'subject' => $subjectModel,
				'date' => $date,
				'hour' => $hourModel,
				'batch' => $batchModel,
				'students' => $students,
				'attendances' => $attendances,
			]);
		} else {
			// ToDo
		}
    }
	
	public function actionSubjectMap()
	{
		$department = Yii::$app->user->identity->profile->department;
		
		$departmentFaculties = \Da\User\Model\User::find()
									->joinWith(['profile'])
									->where(['department' => $department, 'status' => 1])
									->asArray()->all();
		
		if (Yii::$app->request->post()) {
			$data = Yii::$app->request->post();
			$dataObj = json_decode($data['subjects_data']);
			$rows = [];
			foreach ($dataObj->subjects as $subjectMap) {
				$rows[] = [
					'id' => NULL,
					'faculty_id' => $data['faculty_id'],
					'subject_id' => $subjectMap[0]
				];
			}
			
			$rows = array_map("unserialize", array_unique(array_map("serialize", $rows)));
			
			FacultySubject::deleteAll(['faculty_id' => $data['faculty_id']]);
			
			$model = new FacultySubject();
			
			if (!empty($rows)) {
				Yii::$app->db->createCommand()->batchInsert(FacultySubject::tableName(), $model->attributes(), $rows)->execute();
				Yii::$app->session->setFlash('success', "Successfully assigned the subjects.");
				return $this->refresh();
			}
		}
		
        return $this->render('subject-map', [
            'departmentFaculties' => $departmentFaculties,
        ]);
	}
}
