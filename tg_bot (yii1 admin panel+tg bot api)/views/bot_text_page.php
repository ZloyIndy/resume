<?php $this->pageTitle = 'Тексты сообщений бота'; ?>
<div class="clients-hat">
    <div class="goback-link pull-right">
        <?php
/*        if ($user->roles[0]->name == 'admin' || $userRight->create_site) {
            echo CHtml::button('Новый сайт', array('onClick' => 'window.location.href= "' . Yii::app()->createUrl("page/new_site") . '"',
                'class' => 'btn_100 popup-open popup-open', 'id' => 'popup_new_domain_button'));
        }
        $showCheckboxes = $user->roles[0]->name == 'admin' || $userRight->create_site || $userRight->create_action;
        */?>
        <!--<nav class="clients-nav navbar">
            <ul class="nav navbar-nav">
                <?/* foreach ($statuses as $k => $s): */?>
                    <li <?php /*echo $partnersStatusFilter == $k ? 'class="active"' : '' */?> >
                        <?php /*echo CHtml::link($s, Yii::app()->createUrl("page/partners_page", array("partnersStatusFilter" => $k))) .
                            '<span class="">' . $statusCount[$k] . '</span>'; */?>
                    </li>
                <?/* endforeach; */?>
            </ul>
        </nav>-->
    </div>
</div>


<main class="content full2" role="main">
    <?php $this->renderPartial('_bot_text_search', array(
        'botText' => $botText,
        'user' => $user,
    )); ?>

    <div class="box-gray box-new-table">
        <div class="box-gray__body no-border bottom_margin">
            <?php
            if (count($botTextTableData->data) == 0) { ?>
                <div class="info_client_001"><p>Сайтов нет</p></div>
                <?
            }
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $botTextTableData,
                'cssFile' => '',
                'emptyText' => '',
                'htmlOptions' => array('class' => 'new-table-main'),
                'columns' => array(
                    array(
                        'name' => 'name',
                        'header' => 'Название',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                CHtml::link($data->name, Yii::app()->createUrl("page/update_bot_text", array("id" => $data->id, "render_page" => 'bot_text_page')));
                        }
                    ),
                    array(
                        'name' => 'descr',
                        'header' => 'Описание',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                CHtml::link($data->descr, Yii::app()->createUrl("page/update_bot_text", array("id" => $data->id, "render_page" => 'bot_text_page')));
                        }
                    ),
                    array(
                        'name' => 'text',
                        'header' => 'Текст',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_1'),
                        'value' => function ($data) {
                            return
                                CHtml::link($data->text, Yii::app()->createUrl("page/update_bot_text", array("id" => $data->id, "render_page" => 'bot_text_page')));
                        }
                    ),/*
                    array(
                        'name' => 'active',
                        'header' => 'Актвен',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_1'),
                        'value' => function ($data) {
                            return
                                ($data->active == 1 ? 'Да': 'Нет');
                        }
                    ),*/
                )));
            ?>
        </div>
    </div>
</main><!--.content-->
<script>
    $("table").removeClass("items");
    $("table").addClass("main-table");
</script>
