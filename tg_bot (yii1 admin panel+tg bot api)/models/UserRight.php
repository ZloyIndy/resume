<?php

/**
 * This is the model class for table "user_right".
 *
 * The followings are the available columns in table 'user_right':
 * @property integer $id
 * @property integer $user_id
 * @property integer $create_client
 * @property integer $delete_client
 * @property integer $create_action
 * @property integer $delete_action
 * @property integer $create_deals
 * @property integer $delete_deals
 * @property integer $create_field
 * @property integer $delete_field
 * @property integer $delete_section
 * @property integer $add_files_client
 * @property integer $add_files_action
 * @property integer $add_files_deal
 * @property integer $add_files_user
 * @property integer $delete_files_client
 * @property integer $delete_files_action
 * @property integer $delete_files_deal
 * @property integer $delete_files_user
 * @property integer $create_steps
 * @property integer $delete_steps
 * @property integer $create_partners
 * @property integer $delete_partners
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class UserRight extends MainUserRight
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_right';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id', 'required'),
            array('user_id, create_client, delete_client, create_action, delete_action, create_deals, delete_deals, create_field, delete_field, delete_section, create_label_clients, create_label_actions, create_label_deals, delete_label_clients, delete_label_actions, delete_label_deals, add_files_client, add_files_action, add_files_deal, add_files_user, delete_files_client, delete_files_action, delete_files_deal, delete_files_user, create_steps, delete_steps, create_partners, delete_partners, create_domains, delete_domains, check_applications, create_payments, confirm_payments, create_sites, edit_bot_text', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, create_client, delete_client, create_action, delete_action, create_deals, delete_deals, create_field, delete_field, delete_section, create_label_clients, create_label_actions, create_label_deals, delete_label_clients, delete_label_actions, delete_label_deals, add_files_client, add_files_action, add_files_deal, add_files_user, delete_files_client, delete_files_action, delete_files_deal, delete_files_user, create_steps, delete_steps, create_partners, delete_partners, create_domains, delete_domains, check_applications, create_payments, confirm_payments, create_sites, edit_bot_text', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'create_client' => 'Create Client',
            'delete_client' => 'Delete Client',
            'create_action' => 'Create Action',
            'delete_action' => 'Delete Action',
            'create_deals' => 'Create Deals',
            'delete_deals' => 'Delete Deals',
            'create_field' => 'Create Field',
            'delete_field' => 'Delete Field',
            'delete_section' => 'Delete Section',
            'create_label_clients' => 'Create Label Client',
            'create_label_actions' => 'Create Label Action',
            'create_label_deals' => 'Create Label Deal',
            'delete_label_clients' => 'Delete Label Client',
            'delete_label_actions' => 'Delete Label Action',
            'delete_label_deals' => 'Delete Label Deal',
            'add_files_client' => 'Add Files Client',
            'add_files_action' => 'Add Files Action',
            'add_files_deal' => 'Add Files Deal',
            'add_files_user' => 'Add Files User',
            'delete_files_client' => 'Delete Files Client',
            'delete_files_action' => 'Delete Files Action',
            'delete_files_deal' => 'Delete Files Deal',
            'delete_files_user' => 'Delete Files User',
            'create_steps' => 'Create Step',
            'delete_steps' => 'Delete Step',

            'create_partners' => 'Create Partners',
            'delete_partners' => 'Delete Partners',
            'create_domains' => 'Create Domains',
            'delete_domains' => 'Delete Domains',
            'check_applications' => 'Check Applications',
            'create_payments' => 'Create Payments',
            'confirm_payments' => 'Confirm Payments',
            'create_sites' => 'Create Sites',
            'edit_bot_text' => 'Edit Bot Texts'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('create_client',$this->create_client);
        $criteria->compare('delete_client',$this->delete_client);
        $criteria->compare('create_action',$this->create_action);
        $criteria->compare('delete_action',$this->delete_action);
        $criteria->compare('create_deals',$this->create_deals);
        $criteria->compare('delete_deals',$this->delete_deals);
        $criteria->compare('create_field',$this->create_field);
        $criteria->compare('delete_field',$this->delete_field);
        $criteria->compare('delete_section',$this->delete_section);
        $criteria->compare('add_files_client',$this->add_files_client);
        $criteria->compare('add_files_action',$this->add_files_action);
        $criteria->compare('add_files_deal',$this->add_files_deal);
        $criteria->compare('add_files_user',$this->add_files_user);
        $criteria->compare('delete_files_client',$this->delete_files_client);
        $criteria->compare('delete_files_action',$this->delete_files_action);
        $criteria->compare('delete_files_deal',$this->delete_files_deal);
        $criteria->compare('delete_files_user',$this->delete_files_user);
        $criteria->compare('create_steps',$this->create_steps);
        $criteria->compare('delete_steps',$this->delete_steps);

        $criteria->compare('create_partners',$this->create_partners);
        $criteria->compare('delete_partners',$this->delete_partners);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserRight the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
