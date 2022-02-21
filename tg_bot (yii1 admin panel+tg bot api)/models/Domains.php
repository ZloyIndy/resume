<?php

/**
 * This is the model class for table "deals".
 *
 * The followings are the available columns in table 'deals':
 * @property integer $partner_id
 * @property integer $site_id
 * @property string $site_domain
 * @property integer $show_user
 * @property integer $active
 */
class Domains extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public $partner_id;
    public $site_id;
    public $site_domain;
    public $show_user;
    public $active;
    public $replaced;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'domains';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('site_domain, site_id, show_user, active', 'required', 'message' => 'Необходимо заполнить поле'),
            array('partner_id, site_id, replaced', 'numerical', 'integerOnly'=>true,  'message' => 'Значение данного поля должно быть числом'),
            //array('name, tg_login', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('partner_id, site_id, site_domain, show_user, active, replaced', 'safe', 'on'=>'search'),
        );
    }

    public function searchDomain($id = null, $noTable = false)
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $this->site_domain != null ? $criteria->addCondition('t.site_domain LIKE "%' . $this->site_domain . '%"', 'OR') : '';


        //$id != null ? $criteria->addCondition('t.client_id='.$id) : '';

        $this->partner_id != null && $this->partner_id != 0 ? $criteria->addCondition('t.partner_id='.$this->partner_id) : '';
        $this->site_id != null && $this->site_id != 0 ? $criteria->addCondition('t.site_id='.$this->site_id) : '';
        //$this->show_user != null && $this->show_user != 0 ? $criteria->addCondition('t.show_user='.$this->show_user) : '';
        //$this->active != null && $this->active != 0 ? $criteria->addCondition('t.active='.$this->active) : '';

        if ($this->show_user != null){
            if ($this->show_user < 2){
                $criteria->addCondition('t.show_user='.$this->show_user);
            }
        }
        if ($this->active != null){
            if ($this->active < 2){
                $criteria->addCondition('t.active='.$this->active);
            }
        }
        if ($this->replaced != null){
            if ($this->replaced < 2){
                $criteria->addCondition('t.replaced='.$this->replaced);
            }
        }

        //$this->created_at != null ? $criteria->addCondition('UNIX_TIMESTAMP(t.created_at)>=' . strtotime($this->created_at)) : '';
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
            'site_id' => 'Сайт',
            'site_domain' => 'Домен',
        );
    }

}
