<?php $this->pageTitle = 'Статистика'; ?>
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
    <?php /*$this->renderPartial('_stats_search', array(
        'topStats' => $topStats,
        //'user' => $user,
    )); */
    ?>
    <div class="box-gray box-new-table bnt-stats">
        <div class="box-gray__body no-border bottom_margin">
            <?php
            /*if (count($topStats) == 0) { */?><!--
                <div class="info_client_001"><p>Статистика отсутсвует</p></div>
                --><?/*
            }*/
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $partners,
                'cssFile' => '',
                'emptyText' => '',
                'htmlOptions' => array('class' => 'new-table-main'),
                'columns' => array(
                    array(
                        'name' => 'tg_login',
                        'header' => 'Логин',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_2'),
                        'value' => function ($data) {
                            return
                                $data['tg_login'];
                        }
                    ),
                    array(
                        'name' => 'app_count',
                        'header' => 'Количество заявок',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_2'),
                        'value' => function ($data) use ($topStats)  {
                            if (isset($topStats[$data['tg_login']]))
                                $t = $topStats[$data['tg_login']];
                            else
                                $t = '0';
                            return
                                $t;
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
