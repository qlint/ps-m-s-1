<?php

/**
 * This is the model class for table "cbsc_exam_scores_split_17".
 *
 * The followings are the available columns in table 'cbsc_exam_scores_split_17':
 * @property integer $id
 * @property integer $student_id
 * @property integer $exam_scores_id
 * @property integer $mark
 */
class CbscExamScoresSplit17 extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CbscExamScoresSplit17 the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cbsc_exam_scores_split_17';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, exam_scores_id, mark', 'required'),
			array('student_id, exam_scores_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, student_id, exam_scores_id, mark', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'student_id' => 'Student',
			'exam_scores_id' => 'Exam Scores',
			'mark' => 'Mark',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('exam_scores_id',$this->exam_scores_id);
		$criteria->compare('mark',$this->mark);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
}