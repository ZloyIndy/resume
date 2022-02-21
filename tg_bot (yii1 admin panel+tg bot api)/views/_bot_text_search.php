<aside class="left-sidebar">
    <div class="box-gray__head">
        Поиск текстов бота
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
                <?php echo $form->textField($botText, 'name', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Название')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->textField($botText, 'text', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Текст')); ?>
            </div>
            <div class="form-group">
                <?php echo $form->textField($botText, 'descr', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Описание')); ?>
            </div>

            <?php $role = UsersRoles::model()->find('user_id=' . Yii::app()->user->id)->itemname; ?>

            <div class="form-group form-group-btn">
                <?php echo CHtml::hiddenField('BotText[search]', 'true'); ?>
                <?php echo CHtml::submitButton('Найти', array('class' => 'btn white')); ?>
            </div>
            <?php $this->endWidget(); ?>

        </div>
    </div>
</aside><!--.left-sidebar -->

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>