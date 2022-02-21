<?php

$this->pageTitle = 'Изменить домен';
if ($domain->isNewRecord)
    $this->pageTitle = 'Новый домен';

$correct_path = 'http://' . $_SERVER["HTTP_HOST"];
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'new-partner',
    'htmlOptions' => [
        'class' => 'page-form'
    ]
));
?>
<div class="clients-hat">
    <div class="client-name">

        <?php echo CHtml::link('Домены', array('page/domains_page')); ?>
        <img src="/img/right-arrow-button.svg" alt=""><?= $this->pageTitle ?>
    </div>
    <div class="goback-link pull-right">
        <input class="btn_close" type="button" onclick="history.back();" value="❮  Назад "/>
    </div>
</div>

<main class="content full2" role="main">
    <div class="content-edit-block">
        <div class="title_name_1">Данные домена</div>
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
                            <?php echo $form->labelEx($domain,'site_domain');
                            echo $form->textField($domain, 'site_domain', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Домен'));
                            echo $form->error($domain,'site_domain'); ?>
                        </div>

                        <div class="form-group">
                            <?
                            echo $form->labelEx($domain, 'site_id');
                            $selected = null;
                            if (!$domain->isNewRecord)
                                $selected = $domain->site_id;
                            $data = [];
                            $listOptions = $sites;
                            foreach ($listOptions as $k=>$option) {
                                $data[$k] = $option;
                            }
                            echo CHtml::dropDownList("Domains[site_id]", $selected, $data, ['class' => 'styled select']);
                            ?>
                        </div>

                        <div class="form-group">
                            <?
                            $disabled = true;
                            if ($domain->isNewRecord)
                                $disabled = false;
                            echo $form->labelEx($domain, 'partner_id');
                            $selected = null;
                            if (!$domain->isNewRecord)
                                $selected = $domain->partner_id;
                            $data = [];
                            $listOptions = $partners;
                            foreach ($listOptions as $k=>$option) {
                                $data[$k] = $option;
                            }
                            echo CHtml::dropDownList("Domains[partner_id]", $selected, $data, ['class' => 'styled select','disabled'=>$disabled]);
                            ?>
                        </div>

                        <div class="form-group">
                            <label class="label">Видим для партнеров:</label>
                            <?php echo $form->checkBox($domain,'show_user',array('value' => '1', 'uncheckValue'=>'0')); ?>
                        </div>
                        <div class="form-group">
                            <label class="label">Активен:</label>
                            <?php echo $form->checkBox($domain,'active',array('value' => '1', 'uncheckValue'=>'0')); ?>
                        </div>
                        <? if (!$domain->isNewRecord && $domain->replaced==1):?>
                        <div class="form-group">
                            <label class="label">Заменен:</label>
                            <?php echo $form->checkBox($domain,'replaced',array('value' => '1', 'uncheckValue'=>'0')); ?>
                        </div>
                        <? endif ?>

                        <div class="form-group">
                            <?php echo CHtml::submitButton($domain->isNewRecord ? 'Добавить' : 'Изменить', array('class' => 'btn')); ?>
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