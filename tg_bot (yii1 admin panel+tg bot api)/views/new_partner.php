<?php
$this->pageTitle = 'Изменить партнера';
if ($partner->isNewRecord)
    $this->pageTitle = 'Новый партнер';

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

        <?php echo CHtml::link('Партнеры', array('page/partners_page')); ?>
        <img src="/img/right-arrow-button.svg" alt=""><? echo $this->pageTitle?>
    </div>
    <div class="goback-link pull-right">
        <input class="btn_close" type="button" onclick="history.back();" value="❮  Назад "/>
    </div>
</div>

<main class="content full2" role="main">
    <div class="content-edit-block">
        <div class="title_name_1">Анкета</div>
        <div class="content-01">
            <? /* echo CHtml::dropDownList("Clients[additionalField][$value[table_name]]", $selected, $data, ['class' => 'styled select']);*/ ?>

            <?php
            if (!empty($errorAddFieldText)) { ?>
                <div class="errorAddField"><? echo $errorAddFieldText ?></div>
            <? } ?>

            <div class="client-content">
                <div class="block_client">
                    <div class="main-table row edit-row">
                        <div class="form-group">
                            <?php echo $form->textField($partner, 'tg_login', array('type' => 'text','disabled'=>'true', 'class' => 'form-control', 'placeholder' => 'Логин тг')); ?>
                        </div>
                        <div class="form-group">
                            <?
                            $selected = $partner->status;
                            $data = [];
                            $listOptions = Partners::getStatus();
                            foreach ($listOptions as $k => $option) {
                                $data[$k] = $option;
                            }
                            echo CHtml::dropDownList("Partners[status]", $selected, $data, ['class' => 'styled select']);
                            ?>
                        </div>

                        <div class="form-group">
                            <? foreach ($siteList as $k=>$s): ?>
                                <?
                                $data = [];
                                $listOptions = $s;
                                $data[0] = 'Удалить';
                                foreach ($listOptions as $kk => $ss) {
                                    $data[$kk] = $ss['site_domain'];
                                }
                                $selected = $selectedDomains[$ss['site_id']];
                                ?>
                                <h5><?= $siteNames[$ss['site_id']] ?></h5>
                                <?echo CHtml::dropDownList("Domains[".$k."][domain_id]", $selected, $data, ['class' => 'styled select']);?>
                                <hr/>
                            <? endforeach; ?>
                        </div>

                        <div class="form-group">
                            <?php echo CHtml::submitButton($partner->isNewRecord ? 'Добавить' : 'Изменить', array('class' => 'btn')); ?>
                            <div id="preloader"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>

    <div class="box-gray111 width-static">
        <div class="edit_user_1anketa">
            <div class="title_name_2">Параметры</div>

        </div>
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