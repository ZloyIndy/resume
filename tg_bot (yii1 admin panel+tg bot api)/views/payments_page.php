<?php $this->pageTitle = 'Выплаты'; ?>
<div class="clients-hat">
    <div class="goback-link pull-right">
        <?php
        if ($user->roles[0]->name == 'admin' || $userRight->create_payments) {
            echo CHtml::button('Новая выплата', array('onClick' => 'window.location.href= "' . Yii::app()->createUrl("page/new_payments") . '"',
                'class' => 'btn_100 popup-open popup-open', 'id' => 'popup_new_payment_button'));
        }
        $showCheckboxes = $user->roles[0]->name == 'admin' || $userRight->create_payments || $userRight->create_action;
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
    <?php $this->renderPartial('_payments_search', array(
        'payment' => $payment,
        'partnerList' => $partnerList,
        'user' => $user,
    )); ?>

    <div class="box-gray box-new-table">
        <div class="box-gray__body no-border bottom_margin">
            <?php
            if (count($paymentTableData->data) == 0) { ?>
                <div class="info_client_001"><p>Данных о выплатах нет</p></div>
                <?
            }
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider' => $paymentTableData,
                'cssFile' => '',
                'emptyText' => '',
                'htmlOptions' => array('class' => 'new-table-main'),
                'columns' => array(
                    array(
                        'name' => 'partner_id',
                        'header' => 'Партнеры',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) use ($partners) {
                            return
                                CHtml::link($partners[$data->partner_id]->tg_login, Yii::app()->createUrl("page/new_partner", array("id" => $partners[$data->partner_id]->id, "render_page" => 'partners_page')));
                        }
                    ),
                    array(
                        'name' => 'sum',
                        'header' => 'Сумма',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                    ),
                    array(
                        'name' => 'paid',
                        'header' => 'Выплачено',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            return
                                ($data->paid == 1 ? 'Да': 'Нет');
                        }
                    ),
                    array(
                        'name' => 'date',
                        'header' => 'Выплата',
                        'type' => 'raw',
                        'headerHtmlOptions' => array('class' => 'w_f_3'),
                        'value' => function ($data) {
                            if ($data->paid != 0) {
                                return
                                    '<span class="paid">Выплачено ('.$data->date.')</span>';
                            } else {
                                //CHtml::link($partners[$data->partner_id]->tg_login, Yii::app()->createUrl("page/new_partner", array("id" => $partners[$data->partner_id]->id, "render_page" => 'partners_page')));
                                return
                                    '<a class="js-payment-update not-paid" data-id="'.$data->id.'" href="#">Выплачено</a>';
                            }
                        }
                    ),
                )));
            ?>
        </div>
    </div>
</main><!--.content-->
<script>
    //$('table").removeClass("items");
    //$("table").addClass("main-table");

    $(function () {
        $('.js-payment-update').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var link = $(this);
            $.ajax({
                method: 'POST',
                url: '/page/ajax_setpaid/',
                data: {
                    /*YII_CSRF_TOKEN:document.getElementsByName('YII_CSRF_TOKEN')[0].value,*/
                    paymentId: id,
                },
                dataType : 'json'
            }).done(function(response) {
                console.log(response);
                link.after('<span class="paid">Выплачено</span>');
                link.remove();
            });
        });
    });
</script>
