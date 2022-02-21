<?php

/**
 * This is the model class for table "deals".
 *
 * The followings are the available columns in table 'partners':
 * @property integer $id
 * @property string $name
 * @property string $tg_login
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $chat_id
 * @property string $address
 */
class Partners extends CActiveRecord
{
    const STATUS_PARTNER = 1;
    const STATUS_TRUSTED = 2;
    const STATUS_BANNED = 3;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getStatus()
    {
        return [
            //self::STATUS_MOD => 'На модерации',
            self::STATUS_PARTNER => 'Партнер',
            self::STATUS_TRUSTED => 'Проверенный партнер',
            self::STATUS_BANNED => 'Забаненный партнер',
        ];
    }
    public static function getStatusText($key)
    {
        $data = self::getStatus();
        return $data[$key];

    }

    public $name;
    public $tg_login;
    public $status;
    public $created_at;
    public $updated_at;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'partners';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('tg_login, status', 'required', 'message' => 'Необходимо заполнить поле'),
            array('status', 'numerical', 'integerOnly'=>true,  'message' => 'Значение данного поля должно быть числом'),
            //array('name, tg_login', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('name, tg_login, status, created_at, updated_at', 'safe', 'on'=>'search'),
        );
    }

    public function searchPartners($id = null, $noTable = false, $partnersStatusFilter = 0)
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $this->name != null ? $criteria->addCondition('t.name LIKE "%' . $this->name . '%"', 'OR') : '';
        $this->tg_login != null ? $criteria->addCondition('t.tg_login LIKE "%' . $this->tg_login . '%"', 'OR') : '';

        //$id != null ? $criteria->addCondition('t.client_id='.$id) : '';

        //$this->status != null && $this->status != 0 ? $criteria->addCondition('t.status='.$this->status) : '';

        $this->created_at != null ? $criteria->addCondition('UNIX_TIMESTAMP(t.created_at)>=' . strtotime($this->created_at)) : '';
        $this->updated_at != null ? $criteria->addCondition('UNIX_TIMESTAMP(t.updated_at)<=' . strtotime($this->updated_at . ':59')) : '';

        $partnersStatusFilter != 0 ? $criteria->addCondition('t.status='.$partnersStatusFilter) : '';

        if ($noTable) {
            $criteria->order = 't.id DESC';
            return Partners::model()->findAll($criteria);
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

}
