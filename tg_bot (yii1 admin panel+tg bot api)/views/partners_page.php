<?php $this->pageTitle = 'Партнеры'; ?>
<div class="clients-hat">
    <div class="goback-link pull-right">
        <?php
        $showCheckboxes = $user->roles[0]->name == 'admin' || $userRight->create_client || $userRight->create_action;
        ?>
        <nav class="clients-nav navbar">
            <ul class="nav navbar-nav">
                <? foreach ($statuses as $k => $s): ?>
                    <li <?php echo $partnersStatusFilter == $k ? 'class="active"' : '' ?> >
                        <?php echo CHtml::link($s, Yii::app()->createUrl("page/partners_page", array("partnersStatusFilter" => $k))) .
                            '<span class="">' . $statusCount[$k] . '</span>'; ?>
                    </li>
                <? endforeach; ?>
            </ul>
        </nav>
    </div>
</div>


<main class="content full2" role="main">
    <?php $this->renderPartial('_partners_search', array(
        'partners' => $partners,
        'user' => $user,
    )); ?>

    <div class="box-gray box-new-table">
        <div class="box-gray__body no-border bottom_margin">
            <?php
            if (count($partners_table_data->data) == 0) { ?>
                <div class="info_client_001"><p>Партнеров нет</p></div>
                <?
            }
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $partners_table_data,
                'cssFile' => '',
                'emptyText' => '',
                'htmlOptions' => array('class' => 'new-table-main'),
                'columns' => array(
                    array(
                        'name' => 'tg_login',
                        'header' => 'Партнеры',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_2'),
                        'value' => function ($data) {
                            return
                                CHtml::link($data->tg_login, Yii::app()->createUrl("page/new_partner", array("id" => $data->id, "render_page" => 'partners_page')));
                        }
                    ),
                    array(
                        'name' => 'status',
                        'header' => 'Статусы',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                CHtml::link(Partners::getStatusText($data->status), Yii::app()->createUrl("page/new_partner", array("id" => $data->id, "render_page" => 'partners_page')));
                        }
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
