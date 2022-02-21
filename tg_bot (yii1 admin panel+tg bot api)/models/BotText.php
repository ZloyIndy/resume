<?php

/**
 * This is the model class for table "deals".
 *
 * The followings are the available columns in table 'partners':
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property string $descr
 */
class BotText extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /*public function getBotText($name)
    {
        $n = BotText::model()->find('t.name = '.$name);
        return $n->text;
    }*/

    public $name;
    public $text;
    public $descr;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'bot_text';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, text', 'required', 'message' => 'Необходимо заполнить поле'),
            //descr
            // @todo Please remove those attributes that should not be searched.
            array('name, text, descr', 'safe', 'on'=>'search'),
        );
    }

    public function searchBotText($id = null, $noTable = false, $partnersStatusFilter = 0)
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $this->name != null ? $criteria->addCondition('t.name LIKE "%' . $this->name . '%"', 'OR') : '';
        $this->text != null ? $criteria->addCondition('t.text LIKE "%' . $this->text . '%"', 'OR') : '';
        $this->descr != null ? $criteria->addCondition('t.descr LIKE "%' . $this->descr . '%"', 'OR') : '';

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

    public function attributeLabels()
    {
        return array(
            'name' => 'Название',
            'descr' => 'Описание',
            'text' => 'Текст',
        );
    }

}
