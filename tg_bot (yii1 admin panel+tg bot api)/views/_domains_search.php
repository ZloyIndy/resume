<aside class="left-sidebar">
    <div class="box-gray__head">
        Поиск доменов
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
                <?php echo $form->textField($domains, 'site_domain', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Домен')); ?>
            </div>

            <?php $role = UsersRoles::model()->find('user_id=' . Yii::app()->user->id)->itemname; ?>

            <div class="form-group">
                <label class="label">Видим для партнеров:</label>
                <?echo CHtml::dropDownList("Domains[show_user]", $domains->show_user, [2=>'Все',1=>'Да',0=>'Нет'], ['class' => 'styled select']);?>
                <?php /*echo $form->checkBox($domains,'show_user',array('value' => '1', 'uncheckValue'=>'0')); */?>
            </div>
            <div class="form-group">
                <label class="label">Активен:</label>
                <?echo CHtml::dropDownList("Domains[active]", $domains->active, [2=>'Все',1=>'Да',0=>'Нет'], ['class' => 'styled select']);?>
                <?php /*echo $form->checkBox($domains,'active',array('value' => '1', 'uncheckValue'=>'0')); */?>
            </div>
            <div class="form-group">
                <label class="label">Заменен:</label>
                <?echo CHtml::dropDownList("Domains[replaced]", $domains->replaced, [2=>'Все',1=>'Да',0=>'Нет'], ['class' => 'styled select']);?>
                <?php /*echo $form->checkBox($domains,'active',array('value' => '1', 'uncheckValue'=>'0')); */?>
            </div>

            <div class="form-group form-group-btn">
                <?php echo CHtml::hiddenField('Domains[search]', 'true'); ?>
                <?php echo CHtml::submitButton('Найти', array('class' => 'btn white')); ?>
            </div>
            <?php $this->endWidget(); ?>

        </div>
    </div>
</aside><!--.left-sidebar -->

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>