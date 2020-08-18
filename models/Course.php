<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "course".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $department_id
 * @property int $self_aided
 * @property int $semesters
 * @property string $course_type
 * @property string $subject_ids
 * @property int $status
 */
class Course extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'department_id', 'self_aided'], 'required'],
            [['department_id', 'self_aided', 'semesters', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 5],
            [['course_type'], 'string', 'max' => 10],
            [['subject_ids'], 'string', 'max' => 500],
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
            'department_id' => 'Department ID',
            'self_aided' => 'Self Aided',
            'semesters' => 'Semesters',
            'course_type' => 'Course Type',
            'subject_ids' => 'Subject Ids',
            'status' => 'Status',
        ];
    }
}
