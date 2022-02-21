<aside class="left-sidebar">
    <div class="box-gray__head">
        Поиск выплат
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
                <?php
                echo $form->labelEx($payment, 'sum');
                echo $form->textField($payment, 'sum', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Сумма')); ?>
            </div>

            <?php $role = UsersRoles::model()->find('user_id=' . Yii::app()->user->id)->itemname; ?>

            <div class="form-group">
                <?
                echo $form->labelEx($payment, 'partner_id');
                $selected = $payment->partner_id;
                $data = [];
                $listOptions = $partnerList;
                foreach ($listOptions as $k=>$option) {
                    $data[$k] = $option;
                }
                echo CHtml::dropDownList("Payments[partner_id]", $selected, $data, ['class' => 'styled select']);
                ?>
            </div>

            <div class="form-group">
                <label class="label">Выплачено:</label>
                <?echo CHtml::dropDownList("Payments[paid]", $payment->paid, [2=>'Все',1=>'Да',0=>'Нет'], ['class' => 'styled select']);?>
                <?php /*echo $form->checkBox($payment,'paid',array('value' => '1', 'uncheckValue'=>'0')); */?>
            </div>

            <div class="form-group form-group-btn">
                <?php echo CHtml::hiddenField('Payments[search]', 'true'); ?>
                <?php echo CHtml::submitButton('Найти', array('class' => 'btn white')); ?>
            </div>
            <?php $this->endWidget(); ?>

        </div>
    </div>
</aside><!--.left-sidebar -->

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>