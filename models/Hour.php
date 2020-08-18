<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hour".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $start_time
 * @property string $end_time
 *
 * @property LogAttendance[] $logAttendances
 * @property StudentSubjectAttendance[] $studentSubjectAttendances
 */
class Hour extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hour';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'start_time', 'end_time'], 'required'],
            [['start_time', 'end_time'], 'safe'],
            [['name', 'description'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function afterFind()
	{
		parent::afterFind();
		
		$reduce = 6; // number of minutes (6) to be reduced from each hour on friday
		
		// isFriday ?
		// classes start 30 minutes early & ends 1 hour early
		if (date('w') == 5) {
			$start_time = \DateTime::createFromFormat('H:i:s', $this->start_time);
			$end_time = \DateTime::createFromFormat('H:i:s', $this->end_time);
			
			// subtract 30 minutes on each hour
			$time_difference = new \DateInterval('PT30M');
			$start_time->sub($time_difference);
			$end_time->sub($time_difference);
			// subtract 6 minutes from each hour
			$reduceAtStart = ($this->id * $reduce) - $reduce;
			$reduceAtEnd = ($this->id * $reduce);
			$tosubStart = new \DateInterval('PT'.$reduceAtStart.'M');
			$start_time->sub($tosubStart);
			$tosubEnd = new \DateInterval('PT'.$reduceAtEnd.'M');
			$end_time->sub($tosubEnd);
			
			$this->start_time = $start_time->format('H:i:s');
			$this->end_time = $end_time->format('H:i:s');
		}
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogAttendances()
    {
        return $this->hasMany(LogAttendance::className(), ['hour_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentSubjectAttendances()
    {
        return $this->hasMany(StudentSubjectAttendance::className(), ['hour_id' => 'id']);
    }
}
