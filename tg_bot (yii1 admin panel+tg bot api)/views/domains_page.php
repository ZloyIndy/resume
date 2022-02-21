<?php $this->pageTitle = 'Домены'; ?>
<div class="clients-hat">
    <?php /*echo CHtml::link('Домены', array('page/domains_page')); */?><!--
    <img src="/img/right-arrow-button.svg" alt="">--><?/* echo $this->pageTitle*/?>
    <div class="goback-link pull-right">
        <?php
        if ($user->roles[0]->name == 'admin' || $userRight->create_domain) {
            echo CHtml::button('Новый домен', array('onClick' => 'window.location.href= "' . Yii::app()->createUrl("page/new_domain") . '"',
                'class' => 'btn_100 popup-open popup-open', 'id' => 'popup_new_domain_button'));
        }
        $showCheckboxes = $user->roles[0]->name == 'admin' || $userRight->create_domain || $userRight->create_action;
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
    <?php $this->renderPartial('_domains_search', array(
        'domains' => $domains,
        'user' => $user,
    )); ?>

    <div class="box-gray box-new-table">
        <div class="box-gray__body no-border bottom_margin">
            <?php
            if (count($domainsTableData->data) == 0) { ?>
                <div class="info_client_001"><p>Доменов нет</p></div>
                <?
            }
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $domainsTableData,
                'cssFile' => '',
                'emptyText' => '',
                'htmlOptions' => array('class' => 'new-table-main'),
                'columns' => array(
                    array(
                        'name' => 'domain',
                        'header' => 'Домены',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                CHtml::link($data->site_domain, Yii::app()->createUrl("page/update_domain", array("id" => $data->id, "render_page" => 'domains_page')));
                        }
                    ),
                    array(
                        'name' => 'partner_id',
                        'header' => 'Партнеры',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) use ($partners) {
                            if (isset($partners[$data->partner_id]->tg_login)){
                                return
                                    CHtml::link($partners[$data->partner_id]->tg_login, Yii::app()->createUrl("page/new_partner", array("id" => $partners[$data->partner_id]->id, "render_page" => 'partners_page')));
                            }else{
                                return 'Не выбран';
                            }
                        }
                    ),
                    array(
                        'name' => 'site_id',
                        'header' => 'Сайты',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) use ($sites) {
                            return
                                CHtml::link($sites[$data->site_id]->name, Yii::app()->createUrl("page/update_site", array("id" => $sites[$data->site_id]->id, "render_page" => 'sites_page')));
                        }
                    ),
                    array(
                        'name' => 'show_user',
                        'header' => 'Видимый',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                ($data->show_user == 1 ? 'Да': 'Нет');
                        }
                    ),
                    array(
                        'name' => 'active',
                        'header' => 'Актвен',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                ($data->active == 1 ? 'Да': 'Нет');
                        }
                    ),
                    array(
                        'name' => 'replaced',
                        'header' => 'Заменен',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                ($data->replaced == 1 ? 'Да': 'Нет');
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
