<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subject".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BatchSubject[] $batchSubjects
 * @property Batch[] $batches
 * @property FacultySubject[] $facultySubjects
 * @property LogAttendance[] $logAttendances
 * @property StudentSubject[] $studentSubjects
 * @property StudentSubjectAttendance[] $studentSubjectAttendances
 */
class Subject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject';
    }

    /**
     * {@inheritdoc}
     */
	public function behaviors()
	{
		return [
			[
                'class' => TimestampBehavior::className(),
                // using datetime instead of UNIX timestamp
                'value' => new Expression('NOW()'),
            ],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'code'], 'string', 'max' => 255],
            [['code'], 'match', 'pattern' => '/^[a-zA-Z0-9()-]*$/'],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchSubjects()
    {
        return $this->hasMany(BatchSubject::className(), ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatches()
    {
        return $this->hasMany(Batch::className(), ['id' => 'batch_id'])->viaTable('batch_subject', ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultySubjects()
    {
        return $this->hasMany(FacultySubject::className(), ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogAttendances()
    {
        return $this->hasMany(LogAttendance::className(), ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSubjects()
    {
        return $this->hasMany(StudentSubject::className(), ['subject_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSubjectAttendances()
    {
        return $this->hasMany(StudentSubjectAttendance::className(), ['subject_id' => 'id']);
    }
}
