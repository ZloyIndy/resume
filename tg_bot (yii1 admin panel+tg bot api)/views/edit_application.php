<?php

$this->pageTitle = 'Просмотр заявки';

$correct_path = 'http://' . $_SERVER["HTTP_HOST"];
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'view-application',
    'htmlOptions' => [
        'class' => 'page-form'
    ]
));
?>
<div class="clients-hat">
    <div class="client-name">
        <?php echo CHtml::link('Заявки', array('page/applications_page')); ?>
        <img src="/img/right-arrow-button.svg" alt=""><?= $this->pageTitle ?>
    </div>
    <div class="goback-link pull-right">
        <input class="btn_close" type="button" onclick="history.back();" value="❮  Назад "/>
    </div>
</div>

<main class="content full2" role="main">
    <div class="content-edit-block">
        <div class="title_name_1">Данные заявки</div>
        <div class="content-01">
            <? /* echo CHtml::dropDownList("Clients[additionalField][$value[table_name]]", $selected, $data, ['class' => 'styled select']);*/ ?>

            <?php
            if (!empty($errorAddFieldText)) { ?>
                <div class="errorAddField"><? echo $errorAddFieldText ?></div>
            <? } ?>

            <div class="client-content">
                <div class="block_client box-edit-application">
                    <div class="main-table row edit-row">
                        <div class="form-group">
                            <?php echo $form->labelEx($application, 'tg_login'); ?>
                            <div><? echo $application->tg_login; ?></div>
                            <? echo $form->error($application, 'tg_login'); ?>
                        </div>

                        <?php echo $form->labelEx($application, 'description'); ?>
                        <div><? echo $application->description; ?></div>
                        <? echo $form->error($application, 'description'); ?>

                        <div class="form-group">
                            <?php echo CHtml::submitButton('Сделать партнером', array('class' => 'btn', 'name' => 'Partners[new]')); ?>
                            <div id="preloader"></div>
                        </div>
                        <div class="form-group">
                            <?php echo CHtml::submitButton('Отказать', array('class' => 'btn', 'name' => 'Partners[decline]')); ?>
                            <div id="preloader"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>

    <div class="box-gray111 width-static">

    </div>
</main>

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
    $("#new-client").submit(function () {
        $("#preloader").addClass('preloader');
        $("#save_and_create").hide();
        $("#save").hide();
    });
</script>