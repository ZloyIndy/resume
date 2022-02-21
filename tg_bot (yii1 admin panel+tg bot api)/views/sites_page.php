<?php $this->pageTitle = 'Домены'; ?>
<div class="clients-hat">
    <div class="goback-link pull-right">
        <?php
        if ($user->roles[0]->name == 'admin' || $userRight->create_site) {
            echo CHtml::button('Новый сайт', array('onClick' => 'window.location.href= "' . Yii::app()->createUrl("page/new_site") . '"',
                'class' => 'btn_100 popup-open popup-open', 'id' => 'popup_new_domain_button'));
        }
        $showCheckboxes = $user->roles[0]->name == 'admin' || $userRight->create_site || $userRight->create_action;
        ?>
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
    <?php $this->renderPartial('_sites_search', array(
        'site' => $site,
        'user' => $user,
    )); ?>

    <div class="box-gray box-new-table">
        <div class="box-gray__body no-border bottom_margin">
            <?php
            if (count($siteTableData->data) == 0) { ?>
                <div class="info_client_001"><p>Сайтов нет</p></div>
                <?
            }
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $siteTableData,
                'cssFile' => '',
                'emptyText' => '',
                'htmlOptions' => array('class' => 'new-table-main'),
                'columns' => array(
                    array(
                        'name' => 'site',
                        'header' => 'Сайты',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_1'),
                        'value' => function ($data) {
                            return
                                CHtml::link($data->name, Yii::app()->createUrl("page/update_site", array("id" => $data->id, "render_page" => 'sites_page')));
                        }
                    ),
                    /*array(
                        'name' => 'partner_id',
                        'header' => 'Партнеры',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_1'),
                        'value' => function ($data) use ($partners) {
                            return
                               CHtml::link($partners[$data->partner_id]->tg_login, Yii::app()->createUrl("page/new_partner", array("id" => $partners[$data->partner_id]->id, "render_page" => 'partners_page')));
                        }
                    ),
                    array(
                        'name' => 'show_user',
                        'header' => 'Видимый',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_1'),
                        'value' => function ($data) {
                            return
                                ($data->show_user == 1 ? 'Да': 'Нет');
                        }
                    ),
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
