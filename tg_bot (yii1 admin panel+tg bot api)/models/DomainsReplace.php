<?php

/**
 * This is the model class for table "deals".
 *
 * The followings are the available columns in table 'deals':
 * @property integer $tg_login
 * @property string $description
 * @property string $created_at
 */
class DomainsReplace extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public $partner_id;
    public $domain_id;
    public $domain_old_id;
    public $comment;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'domainsReplace';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('partner_id, domain_id, domain_old_id', 'required', 'message' => 'Необходимо заполнить поле'),
            //array('partner_id', 'numerical', 'integerOnly'=>true,  'message' => 'Значение данного поля должно быть числом'),
            //array('name, tg_login', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('partner_id, domain_id, domain_old_id', 'safe', 'on'=>'search'),
        );
    }

    public function searchDomainsReplace($id = null, $noTable = false)
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        //$this->tg_login != null ? $criteria->addCondition('t.tg_login LIKE "%' . $this->tg_login . '%"', 'OR') : '';
        $this->comment != null ? $criteria->addCondition('t.comment LIKE "%' . $this->comment . '%"', 'OR') : '';


        //$id != null ? $criteria->addCondition('t.client_id='.$id) : '';
        $this->partner_id != null && $this->partner_id != 0 ? $criteria->addCondition('t.partner_id='.$this->partner_id) : '';
        $this->domain_id != null && $this->domain_id != 0 ? $criteria->addCondition('t.domain_id='.$this->domain_id) : '';
        $this->domain_old_id != null && $this->domain_old_id != 0 ? $criteria->addCondition('t.domain_old_id='.$this->domain_old_id) : '';
        //$this->show_user != null && $this->show_user != 0 ? $criteria->addCondition('t.show_user='.$this->show_user) : '';
        //$this->active != null && $this->active != 0 ? $criteria->addCondition('t.active='.$this->active) : '';
        //$this->updated_at != null ? $criteria->addCondition('UNIX_TIMESTAMP(t.updated_at)<=' . strtotime($this->updated_at . ':59')) : '';
        //$this->created_at != null ? $criteria->addCondition('UNIX_TIMESTAMP(t.created_at)>=' . strtotime($this->created_at)) : '';

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
            'comment' => 'Комментарий',
            'domain_id' => 'Новый домен',
            'domain_old_id' => 'Старый домен',
        );
    }

}
