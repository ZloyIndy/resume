<?php $this->pageTitle = 'Заявки'; ?>
<div class="clients-hat">
    <div class="goback-link pull-right">
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
    <?php $this->renderPartial('_applications_search', array(
        'application' => $application,
        'user' => $user,
    )); ?>

    <div class="box-gray box-new-table">
        <div class="box-gray__body no-border bottom_margin">
            <?php
            if (count($applicationTableData->data) == 0) { ?>
                <div class="info_client_001"><p>Заявок нет</p></div>
                <?
            }
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $applicationTableData,
                'cssFile' => '',
                'emptyText' => '',
                'htmlOptions' => array('class' => 'new-table-main'),
                'columns' => array(
                    array(
                        'name' => 'tg_login',
                        'header' => 'Логин телеграмм',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                CHtml::link($data->tg_login, Yii::app()->createUrl("page/edit_application", array("id" => $data->id, "render_page" => 'edit_application')));
                        }
                    ),
                    array(
                        'name' => 'description',
                        'header' => 'Текст заявки',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_1'),
                    ),
                )));
            ?>
        </div>
    </div>
</main><!--.content-->
<script>
    $("table").removeClass("items");
    $("table").addClass("main-table");
</script>
