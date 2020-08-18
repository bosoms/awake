<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

use Da\User\Model\User;

/**
 * This is the model class for table "log_attendance".
 *
 * @property int $id
 * @property int $date
 * @property int $hour_id
 * @property int $faculty_id
 * @property int $batch_id
 * @property int $subject_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Hour $hour
 * @property User $faculty
 * @property Batch $batch
 * @property Subject $subject
 */
class LogAttendance extends \yii\db\ActiveRecord
{
	public $whoMarked;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_attendance';
    }

    /**
     * {@inheritdoc}
     */
	public function behaviors()
	{
		return [
			[
                'class' => TimestampBehavior::className(),
                /*
				'createdAtAttribute' => 'created_at',
				'updatedAtAttribute' => 'updated_at',
				'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
				*/
                // using datetime instead of UNIX timestamp
                'value' => new Expression('NOW()'),
            ],
			[
				'class' => BlameableBehavior::className(),
				/*
				'createdByAttribute' => 'created_by',
				'updatedByAttribute' => 'updated_by',
				*/
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'hour_id', 'faculty_id', 'batch_id', 'subject_id'], 'required'],
            [['hour_id', 'faculty_id', 'batch_id', 'subject_id'], 'integer'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['hour_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hour::className(), 'targetAttribute' => ['hour_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Batch::className(), 'targetAttribute' => ['batch_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
			[['faculty_id'], 'checkIfMarked'],
        ];
    }
	
	public function checkIfMarked($attribute, $params, $validator) {
		$exists = LogAttendance::find()
					->where([
						'date' => $this->date,
						'hour_id' => $this->hour_id,
						'batch_id' => $this->batch_id,
						'subject_id' => $this->subject_id,
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
            'date' => 'Date',
            'hour_id' => 'Hour',
            'faculty_id' => 'Faculty',
            'batch_id' => 'Batch',
            'subject_id' => 'Subject',
            'created_at' => 'Created Time',
            'updated_at' => 'Updated Time',
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
					'subject_id' => $this->subject_id,])
				->all();
		
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
    public function getHour()
    {
        return $this->hasOne(Hour::className(), ['id' => 'hour_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(User::className(), ['id' => 'faculty_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne(Batch::className(), ['id' => 'batch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }
}
