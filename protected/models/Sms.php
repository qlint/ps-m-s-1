<?php

/**
 * This is the model class for table "sms".
 *
 * The followings are the available columns in table 'sms':
 * @property integer $id
 * @property integer $uid
 * @property string $phone_number
 * @property string $message
 * @property integer $status
 * @property string $time
 */
class Sms extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Sms the static model class
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
		return 'sms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('message, phone_number', 'required'),
			array('uid', 'safe'),
			array('status, phone_number', 'numerical', 'integerOnly'=>true),
			//array('', 'numerical', 'integerOnly'=>true, 'length', 'max'=>50),
			//array('phone_number', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, uid, phone_number, message, status, time', 'safe', 'on'=>'search'),
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
			'id' => Yii::t("app",'ID'),
			'uid' => Yii::t("app",'User List'),
			'phone_number' => Yii::t("app",'Phone Number'),
			'message' => Yii::t("app",'Message'),
			'status' => Yii::t("app",'Status'),
			'time' => Yii::t("app",'Time'),
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
		$criteria->compare('uid',$this->uid);
		$criteria->compare('phone_number',$this->phone_number,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('time',$this->time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}