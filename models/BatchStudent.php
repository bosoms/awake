<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch_student".
 *
 * @property int $id
 * @property int $roll_no
 * @property int $student_id
 * @property int $batch_id
 *
 * @property Batch $batch
 * @property Student $student
 */
class BatchStudent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batch_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['roll_no'], 'required'],
            [['roll_no', 'student_id', 'batch_id'], 'integer'],
            [['batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Batch::className(), 'targetAttribute' => ['batch_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'roll_no' => 'Roll No',
            'student_id' => 'Student ID',
            'batch_id' => 'Batch ID',
        ];
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
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }
}
