<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $admission_no
 * @property string $class_roll_no
 * @property string $registration_no
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property int $batch_id
 * @property string $date_of_birth
 * @property string $gender
 * @property string $blood_group
 * @property string $phone1
 * @property string $phone2
 * @property string $email
 * @property int $is_sms_enabled
 * @property string $photo_file_name
 * @property int $photo_file_size
 * @property string $photo_content_type
 * @property resource $photo_data
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BatchStudent[] $batchStudents
 * @property StudentAttendance[] $studentAttendances
 * @property StudentSubject[] $studentSubjects
 * @property StudentSubjectAttendance[] $studentSubjectAttendances
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batch_id', 'is_sms_enabled', 'photo_file_size', 'is_active', 'is_deleted'], 'integer'],
            [['date_of_birth', 'created_at', 'updated_at'], 'safe'],
            [['photo_data'], 'string'],
            [['admission_no', 'class_roll_no', 'registration_no', 'first_name', 'middle_name', 'last_name', 'gender', 'blood_group', 'phone1', 'phone2', 'email', 'photo_file_name', 'photo_content_type'], 'string', 'max' => 255],
            [['class_roll_no'], 'unique'],
            [['registration_no'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admission_no' => 'Admission No',
            'class_roll_no' => 'Class Roll No',
            'registration_no' => 'Registration No',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'batch_id' => 'Batch ID',
            'date_of_birth' => 'Date Of Birth',
            'gender' => 'Gender',
            'blood_group' => 'Blood Group',
            'phone1' => 'Phone1',
            'phone2' => 'Phone2',
            'email' => 'Email',
            'is_sms_enabled' => 'Is Sms Enabled',
            'photo_file_name' => 'Photo File Name',
            'photo_file_size' => 'Photo File Size',
            'photo_content_type' => 'Photo Content Type',
            'photo_data' => 'Photo Data',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Finds student by rollNumber
     *
     * @param string $rollNumber
     * @return static|null
     */
    public static function findByRollNumber($rollNumber)
    {
        foreach (self::find()->all() as $student) {
            if (strcasecmp($student['class_roll_no'], $rollNumber) === 0) {
                return new static($student);
            }
        }

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchStudents()
    {
        return $this->hasMany(BatchStudent::className(), ['student_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentAttendances()
    {
        return $this->hasMany(StudentAttendance::className(), ['student_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSubjects()
    {
        return $this->hasMany(StudentSubject::className(), ['student_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSubjectAttendances()
    {
        return $this->hasMany(StudentSubjectAttendance::className(), ['student_id' => 'id']);
    }

    /**
     * Verifies DOB
     *
     * @param string $dob dob to validate
     * @return bool if dob provided is valid for current student
     */
    public function validateDOB($dob)
    {
        return $this->date_of_birth === $dob;
    }
}
