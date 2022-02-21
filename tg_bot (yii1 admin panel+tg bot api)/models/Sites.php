<?php

/**
 * This is the model class for table "sites".
 *
 * The followings are the available columns in table 'sites':
 * @property string $name
 * @property string $description
 * @property string $img
 */
class Sites extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public $name;
    public $description;
    public $img;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'sites';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, description', 'required', 'message' => 'Необходимо заполнить поле'),
            //array('partner_id', 'numerical', 'integerOnly'=>true,  'message' => 'Значение данного поля должно быть числом'),
            //array('name, tg_login', 'max'=>255),
            // The following rule is used by search().
            array('img', 'file', 'types'=>'jpg, gif, png'),
            // @todo Please remove those attributes that should not be searched.
            array('name, description', 'safe', 'on'=>'search'),
        );
    }

    public function getImagePath(){
        if (!empty($this->img)){
            return getcwd().'/site_imgs/'.$this->img;
        } else {
            return getcwd().'/site_imgs/no-image.png';
        }
    }

    public function searchSite($id = null, $noTable = false)
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $this->name != null ? $criteria->addCondition('t.name LIKE "%' . $this->name . '%"', 'OR') : '';
        $this->description != null ? $criteria->addCondition('t.description LIKE "%' . $this->description . '%"', 'OR') : '';


        //$id != null ? $criteria->addCondition('t.client_id='.$id) : '';

        //$this->description != null && $this->description != 0 ? $criteria->addCondition('t.description='.$this->partner_id) : '';
        //$this->show_user != null && $this->show_user != 0 ? $criteria->addCondition('t.show_user='.$this->show_user) : '';
        //$this->active != null && $this->active != 0 ? $criteria->addCondition('t.active='.$this->active) : '';

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
            'name' => 'Название',
            'description' => 'Описание',
            'img' => 'Изображение'
        );
    }

}
