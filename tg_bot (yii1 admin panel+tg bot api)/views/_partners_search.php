<aside class="left-sidebar">
    <div class="box-gray__head">
        Поиск партнеров
    </div>

    <div class="box-gray__body">
        <div class="box-gray__form">
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'enableAjaxValidation' => false,
                'method' => 'get',
            ));
            ?>
            <div class="form-group">
                <?php echo $form->textField($partners, 'tg_login', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Логин тг')); ?>
            </div>

            <?php $role = UsersRoles::model()->find('user_id=' . Yii::app()->user->id)->itemname; ?>

            <div class="form-group">
                <label class="label">Дата создания:</label>
                <?php echo $this->widget('ext.CJuiDateTimePicker.CJuiDateTimePicker', array(
                    'name' => 'Partners[created_at]',
                    'model' => $partners,
                    'attribute' => 'created_at',
                    'language' => 'ru',
                    'options' => array(
                        'dateFormat' => 'dd.mm.yy',
                        'changeMonth' => 'true',
                        'changeYear' => 'true',
                        'showButtonPanel' => true,
                        'beforeShow' => new CJavaScriptExpression('function(element){dataPickerFocus = $(element).attr(\'id\').trim();}')
                    ),
                    'htmlOptions' => array(
                        'class' => 'form-control',
                        'placeholder' => 'От'
                    ),
                ), true); ?>
            </div>
            <div class="form-group">
                <div class="solid_an_client">
                    <?php echo $this->widget('ext.CJuiDateTimePicker.CJuiDateTimePicker', array(
                        'name' => 'Partners[updated_at]',
                        'model' => $partners,
                        'attribute' => 'updated_at',
                        'language' => 'ru',
                        'options' => array(
                            'dateFormat' => 'dd.mm.yy',
                            'changeMonth' => 'true',
                            'changeYear' => 'true',
                            'showButtonPanel' => true,
                            'beforeShow' => new CJavaScriptExpression('function(element){dataPickerFocus = $(element).attr(\'id\').trim();}')
                        ),
                        'htmlOptions' => array(
                            'class' => 'form-control',
                            'placeholder' => 'До'
                        ),
                    ), true); ?>
                </div>
            </div>

            <div class="form-group form-group-btn">
                <?php echo CHtml::hiddenField('Partners[search]', 'true'); ?>
                <?php echo CHtml::submitButton('Найти', array('class' => 'btn white')); ?>
            </div>
            <?php $this->endWidget(); ?>

        </div>
    </div>
</aside><!--.left-sidebar -->

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>