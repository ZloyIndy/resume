<?php

/**
 * This is the model class for table "deals".
 *
 * The followings are the available columns in table 'deals':
 * @property integer $partner_id
 * @property float $sum
 * @property string $date
 * @property integer $paid
 */
class Payments extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public $partner_id;
    public $sum;
    public $date;
    public $paid;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'payments';
    }

    public static function getPaymentPartners()
    {
        $sql = "SELECT DISTINCT(partner_id) FROM payments";
        $results = parent::model(__CLASS__)->findAllBySql(
            $sql
        );
        return $results;
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('partner_id, sum', 'required', 'message' => 'Необходимо заполнить поле'),
            array('paid', 'numerical', 'integerOnly'=>true,  'message' => 'Значение данного поля должно быть числом'),
            //array('name, tg_login', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('partner_id, paid', 'safe', 'on'=>'search'),
        );
    }

    public function searchPayment($id = null, $noTable = false)
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        //$this->site_domain != null ? $criteria->addCondition('t.site_domain LIKE "%' . $this->site_domain . '%"', 'OR') : '';


        //$id != null ? $criteria->addCondition('t.client_id='.$id) : '';

        $this->sum != null ? $criteria->addCondition('t.sum='.$this->sum) : '';
        $this->partner_id != null && $this->partner_id != 0 ? $criteria->addCondition('t.partner_id='.$this->partner_id) : '';
        //$this->paid != null && $this->paid != 0 ? $criteria->addCondition('t.paid='.$this->paid) : '';


        if ($this->paid != null){
            if ($this->paid < 2){
                $criteria->addCondition('t.paid='.$this->paid);
            }
        }

        //$this->date != null ? $criteria->addCondition('UNIX_TIMESTAMP(t.date)>=' . strtotime($this->date)) : '';

        //$this->show_user != null && $this->show_user != 0 ? $criteria->addCondition('t.show_user='.$this->show_user) : '';
        //$this->updated_at != null ? $criteria->addCondition('UNIX_TIMESTAMP(t.updated_at)<=' . strtotime($this->updated_at . ':59')) : '';
        //$partnersStatusFilter != 999 ? $criteria->addCondition('t.status='.$partnersStatusFilter) : '';

        if ($noTable) {
            $criteria->order = 't.id DESC';
            return Domains::model()->findAll($criteria);
        } else {
            return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
                'sort' => array(
                    'defaultOrder' => 't.id DESC',),
                'pagination' => array(
                    'pageSize'=> 30,
                ),
            ));
        }

    }

    public function attributeLabels()
    {
        return array(
            'partner_id' => 'Партнер',
            'sum' => 'Сумма',
            //'site_domain' => 'Домен',
        );
    }

}
