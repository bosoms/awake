<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student_subject_attendance".
 *
 * @property int $id
 * @property int $student_id
 * @property int $batch_id
 * @property int $subject_id
 * @property int $hour_id
 * @property int $date
 * @property string $reason
 * @property int $leavetype_id
 *
 * @property Student $student
 * @property Subject $subject
 * @property Hour $hour
 */
class StudentSubjectAttendance extends \yii\db\ActiveRecord
{
	public $whoMarked;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_subject_attendance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'batch_id', 'subject_id', 'hour_id', 'date', 'leavetype_id'], 'required'],
            [['student_id', 'batch_id', 'subject_id', 'hour_id', 'leavetype_id'], 'integer'],
            [['reason'], 'string'],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['hour_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hour::className(), 'targetAttribute' => ['hour_id' => 'id']],
			[['student_id'], 'checkIfMarked'],
        ];
    }
	
	public function checkIfMarked($attribute, $params, $validator) {
		$exists = StudentSubjectAttendance::find()
					->where([
						'student_id' => $this->student_id,
						'hour_id' => $this->hour_id,
						'date' => $this->date,
					])->exists();

		if($exists) {
			$count = count($this->getWhoMarked());
			$message = '<strong>' . 
						implode(' & ', $this->getWhoMarked()) . 
						'</strong> ' . 
						($count>1?'have':'has') . 
						' already marked attendance for this hour in this class.';
			$this->addError($attribute, $message);
		}
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student ID',
            'batch_id' => 'Batch ID',
            'subject_id' => 'Subject ID',
            'hour_id' => 'Hour ID',
            'date' => 'Date',
            'reason' => 'Reason',
            'leavetype_id' => 'Leavetype ID',
        ];
    }
	
	public function getWhoMarked()
	{
		$log = LogAttendance::find()
				->select('faculty_id')
				->where([
					'date' => $this->date,
					'hour_id' => $this->hour_id,
					'batch_id' => $this->batch_id,
				])->all();
		
		$faculty = [];
		if ($log !== null) {
			foreach ($log as $entry) {
				$faculty[] = $entry->faculty->profile->name;
			}
		}
		
		return $this->whoMarked = $faculty;
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHour()
    {
        return $this->hasOne(Hour::className(), ['id' => 'hour_id']);
    }
}
