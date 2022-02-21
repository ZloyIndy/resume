<?php

$this->pageTitle = 'Изменить сайт';
if ($site->isNewRecord)
    $this->pageTitle = 'Новый сайт';

$correct_path = 'http://' . $_SERVER["HTTP_HOST"];
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'new-site',
    'htmlOptions' => [
        'class' => 'page-form',
        'enctype'=>'multipart/form-data',
    ]
));
?>
<div class="clients-hat">
    <div class="client-name">

        <?php echo CHtml::link('Домены', array('page/sites_page')); ?>
        <img src="/img/right-arrow-button.svg" alt=""><?= $this->pageTitle ?>
    </div>
    <div class="goback-link pull-right">
        <input class="btn_close" type="button" onclick="history.back();" value="❮  Назад "/>
    </div>
</div>

<main class="content full2" role="main">
    <div class="content-edit-block">
        <div class="title_name_1">Данные сайта</div>
        <div class="content-01">
            <? /* echo CHtml::dropDownList("Clients[additionalField][$value[table_name]]", $selected, $data, ['class' => 'styled select']);*/ ?>

            <?php
            if ($errorAddField) { ?>
                <div class="errorAddField"><? echo $errorAddFieldText ?></div>
                <?
            }
            ?>


            <div class="client-content">
                <div class="block_client">
                    <div class="main-table row edit-row">
                        <div class="form-group">
                            <?php echo $form->labelEx($site,'name');
                            echo $form->textField($site, 'name', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Название'));
                            echo $form->error($site,'name'); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $form->labelEx($site,'description');
                            echo $form->textArea($site, 'description', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Описание'));
                            echo $form->error($site,'description'); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $form->labelEx($site,'img');
                            echo '<br>';
                            echo CHtml::activeFileField($site, 'img');;
                            echo $form->error($site,'img');
                            ?>
                        </div>
                        <?php
                        if (!empty($site->img))
                            echo CHtml::image('/site_imgs/'.$site->img.'?t='.time());
                        else
                            echo CHtml::image('/site_imgs/no-image.png');
                        ?>


                        <div class="form-group">
                            <?php echo CHtml::submitButton($site->isNewRecord ? 'Добавить' : 'Изменить', array('class' => 'btn')); ?>
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