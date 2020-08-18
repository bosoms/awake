<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $start_date
 * @property string $end_date
 *
 * @property BatchStudent[] $batchStudents
 * @property BatchSubject[] $batchSubjects
 * @property Subject[] $subjects
 * @property LogAttendance[] $logAttendances
 * @property StudentSubjectAttendance[] $studentSubjectAttendances
 */
class Batch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['start_date', 'end_date'], 'safe'],
            [['name', 'code'], 'string', 'max' => 255],
            [['code', 'year'], 'unique', 'targetAttribute' => ['code', 'year']],
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
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['code' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchStudents()
    {
        return $this->hasMany(BatchStudent::className(), ['batch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchSubjects()
    {
        return $this->hasMany(BatchSubject::className(), ['batch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjects()
    {
        return $this->hasMany(Subject::className(), ['id' => 'subject_id'])->viaTable('batch_subject', ['batch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogAttendances()
    {
        return $this->hasMany(LogAttendance::className(), ['batch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSubjectAttendances()
    {
        return $this->hasMany(StudentSubjectAttendance::className(), ['batch_id' => 'id']);
    }
}
