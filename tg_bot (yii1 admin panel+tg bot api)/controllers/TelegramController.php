<?php

/*define('API_URL', 'https://api.telegram.org/xxx/');*/

class TelegramController extends CController
{

    public function tgRequest($chatId, $options)
    {
        $url = Yii::app()->params['TG_API_URL'] . "sendMessage?chat_id=" . $chatId;
        $post_fields = $options;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
    }

    public function tgPhoto($chatId, $options)
    {
        $url = Yii::app()->params['TG_API_URL'] . "sendPhoto?chat_id=" . $chatId;
        //$path = realpath(getcwd() . $options['path']);
        $path = $options['photo'];
        $post_fields = array(
            'chat_id' => $chatId,
            'photo' => new CURLFile($path),
            //'caption' => $options['caption']
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
    }

    public function actionStart()
    {
        $botToken = Yii::app()->params['TG_TOKEN'];
        //$website = Yii::app()->params['TG_API_URL'];
        $content = file_get_contents("php://input");
        $update = json_decode($content, TRUE);
        //file_put_contents('Telegram2'.time().'.txt',$content);
        $isCallBack = false;
        $isMessage = false;
        $requestSent = false;

        if (!isset($update['my_chat_member']['chat']['id'])) {
            //file_put_contents('telegramLogs/TelegramStart' . time() . '.txt', $content);
            if (key_exists('callback_query', $update)) {
                $message = $update['callback_query'];
                $chatId = $message['from']['id'];
                $tg_login = $message['from']['username'];
                $tg_data = $message['data'];
                $isCallBack = true;
            } else {
                $message = $update['message'];
                $chatId = $message['chat']['id'];
                $text = $message['text'];
                $tg_login = $message['chat']['username'];
                $tg_message = $message['text'];
                $isMessage = true;
            }
            $isPartner = Partners::model()->exists('tg_login = :tl AND status < 3', [':tl' => $tg_login]);

            if ($isCallBack) {
                if ($isPartner) {
                    if (isset($update['callback_query'])) {
                        $n = BotText::model()->find("t.name = 'empty_msg'");
                        $replyText = $n->text;
                        $keyboard = [];
                        //Partner Keyboard actions
                        if ($tg_data == 'kdb_sites') {

                        } elseif ($tg_data == 'kbd_mystats') {
                            $keyboard = [
                                [
                                    ['text' => '24ч', 'callback_data' => 'kbd_mystats_24'],
                                    ['text' => 'Неделя', 'callback_data' => 'kbd_mystats_week'],
                                ],
                                [
                                    ['text' => 'Всё время', 'callback_data' => 'kbd_mystats_all'],
                                ],
                            ];
                            $replyText = 'Выберите период своей статистики';
                        } elseif ($tg_data == 'kbd_topPartners') {
                            $keyboard = [
                                [
                                    ['text' => '24ч', 'callback_data' => 'kbd_top_24'],
                                    ['text' => 'Неделя', 'callback_data' => 'kbd_top_week'],
                                ],
                                [
                                    ['text' => 'Всё время', 'callback_data' => 'kbd_top_all'],
                                ],
                            ];
                            $replyText = 'Выберите период статистики топ партнеров';
                        } elseif ($tg_data == 'kbd_payments') {
                            $criteria = new CDbCriteria;
                            $criteria->mergeWith(['join' => 'LEFT JOIN partners as b ON b.id = partner_id']);
                            $criteria->addCondition('tg_login = :tg_login');
                            $criteria->addCondition('paid = 0');
                            $criteria->params[':tg_login'] = $tg_login;
                            $payments = Payments::model()->findAll($criteria);

                            $sum = 0;
                            foreach ($payments as $p)
                                $sum += $p->sum;

                            $n = BotText::model()->find("t.name = 'balance'");
                            $nOld = ['{sum}'];
                            $nNew = [$sum];
                            $replyText = str_replace($nOld, $nNew, $n->text);
                            //  $replyText = 'Баланс, доступный для вывода: ' . $sum . 'btc';

                            $criteria = new CDbCriteria();
                            $criteria->addCondition('tg_login = :tg_login');
                            $criteria->params[':tg_login'] = $tg_login;
                            $address = Partners::model()->find($criteria);
                            $addressText = $address->address ?? 'Не указан';
                            $replyText .= "\nТекущий адрес вывода: " . $addressText;
                            $replyText .= "\nДля указания нового адреса напишите: /address *новый адрес*";
                        } elseif ($tg_data == 'kbd_chat') {
                            $n = BotText::model()->find("t.name = 'link_group'");
                            $nOld = ['{tg_group_link}'];
                            $nNew = [Yii::app()->params['TG_GROUP_LINK']];
                            $replyText = str_replace($nOld, $nNew, $n->text);
                        } elseif ($tg_data == 'kbd_info') {
                            $n = BotText::model()->find("t.name = 'link_channel'");
                            $nOld = ['{tg_channel_link}'];
                            $nNew = [Yii::app()->params['TG_CHANNEL_LINK']];
                            $replyText = str_replace($nOld, $nNew, $n->text);
                            //$replyText = "Ссылка на канал для отстуков: \n" . Yii::app()->params['TG_CHANNEL_LINK'];
                        } //MyStats buttons
                        elseif (strpos($tg_data, 'kbd_mystats_') !== false) {
                            $criteria = new CDbCriteria;
                            $criteria->mergeWith(['join' => 'LEFT JOIN additional_fields_values as b ON t.id = b.client_id']);
                            $criteria->addCondition('field_22 = :tg_login');
                            $criteria->params[':tg_login'] = $tg_login;
                            $replyText = 'Статистика за всё время: ';
                            if ($tg_data == 'kbd_mystats_24') {
                                $criteria->addCondition('creation_date > now() - interval 1 day');
                                $replyText = 'Статистика за 24 часа: ';
                            } elseif ($tg_data == 'kbd_mystats_week') {
                                $criteria->addCondition('creation_date > now() - interval 1 week');
                                $replyText = 'Статистика за неделю: ';
                            }
                            $mystatsData = Clients::model()->findAll($criteria);
                            $replyText .= count($mystatsData);
                        } //TopStats buttons
                        elseif (strpos($tg_data, 'kbd_top_') !== false) {
                            $criteria = new CDbCriteria;
                            $replyText = 'Топ партнеров по количеству заявок за всё время: ';
                            if ($tg_data == 'kbd_top_24') {
                                $criteria->addCondition('creation_date > now() - interval 1 day');
                                $replyText = 'Топ партнеров по количеству заявок за 24 часа: ';
                            } elseif ($tg_data == 'kbd_top_week') {
                                $criteria->addCondition('creation_date > now() - interval 1 week');
                                $replyText = 'Топ партнеров по количеству заявок за неделю: ';
                            }
                            $topStatsData = Clients::model()->findAll($criteria);
                            foreach ($topStatsData as $tsd) {
                                $addFV = AdditionalFieldsValues::model()->find('client_id = ' . $tsd->id);
                                $topStats[$addFV->field_22] = ($topStats[$addFV->field_22] ?? 0) + 1;
                            }
                            if (isset($topStats)) {
                                arsort($topStats);
                                foreach ($topStats as $k => $ts)
                                    $replyText .= "\n [" . $k . '] - ' . $ts;
                            } else {
                                $n = BotText::model()->find("t.name = 'empty_msg'");
                                $replyText = $n->text;
                            }

                            //} Display sites choose
                            /*elseif ($tg_data == 'kbd_sites') {
                                //
                                $criteria = new CDbCriteria;
                                $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                                $criteria->addCondition('partner_id = ' . $partner->id);
                                $criteria->addCondition('active = 1');
                                $criteria->addCondition('replaced = 0');
                                $domains = Domains::model()->findAll($criteria);
                                $partnerSites = [];
                                foreach ($domains as $d)
                                    $partnerSites[] = $d->site_id;
                                //
                                $criteria = new CDbCriteria;
                                $criteria->addCondition('show_user = 1');
                                $criteria->addCondition('active = 1');
                                $criteria->addCondition('partner_id = 0');
                                $criteria->addCondition('replaced = 0');
                                $criteria->order = 'site_id DESC';
                                $domains = Domains::model()->findAll($criteria);
                                if (!empty($domains)) {
                                    foreach ($domains as $d) {
                                        if (!in_array($d->site_id, $partnerSites)) {
                                            if (!isset($siteList[$d->site_id])) {
                                                $site = Sites::model()->find('id = ' . $d->site_id);
                                                $siteList[$d->site_id] = $site;
                                            }
                                            $domainList[$d->site_id][$d->id] = $d->site_domain;
                                        }
                                    }
                                    foreach ($siteList as $k => $sl) {
                                        $keyboard = [];
                                        $keyboardLine = [];
                                        //$caption = $site->description."\nДомены сайта " . $sl->name . ' :';

                                        $n = BotText::model()->find("t.name = 'sites_display'");
                                        $nOld = ['{site_description}','{site_name}'];
                                        $nNew = [$site->description, $sl->name];
                                        $caption = str_replace($nOld, $nNew, $n->text);

                                        $photo = $sl->getImagePath();

                                        foreach ($domainList[$k] as $dk => $dl)
                                            $keyboardLine[] = ['text' => $dl, 'callback_data' => 'kbd_claim_domain_' . $dk];
                                        $i = 0;
                                        $tempLine = [];
                                        foreach ($keyboardLine as $kl) {
                                            $i++;
                                            $tempLine[$i] = $kl;
                                            if ($i == 3) {
                                                $keyboard[] = [$tempLine[1], $tempLine[2], $tempLine[3]];
                                                $i = 0;
                                            }
                                        }
                                        if ($i != 0) {
                                            if ($i == 1)
                                                $keyboard[] = [$tempLine[1]];
                                            if ($i == 2)
                                                $keyboard[] = [$tempLine[1], $tempLine[2]];
                                        }
                                        $requestSent = true;
                                        $this->tgPhoto($chatId, [
                                            'photo' => $photo,
                                        ]);
                                        $this->tgRequest($chatId, array(
                                            'text' => $caption,
                                            'disable_web_page_preview' => false,
                                            'reply_markup' => json_encode(array('inline_keyboard' => $keyboard, "resize_keyboard" => true))
                                        ));
                                    }
                                } else {
                                    $n = BotText::model()->find("t.name = 'empty_msg'");
                                    $replyText = $n->text;
                                }*/
                            //} Claim domain choose
                            /*elseif (strpos($tg_data, 'kbd_claim_domain_') !== false) {
                                $domainId = (int)filter_var($tg_data, FILTER_SANITIZE_NUMBER_INT);
                                $domain = Domains::model()->findByPk($domainId);
                                if ($domain && $domain->partner_id == 0) {
                                    $resultTransaction = false;
                                    $transaction = Yii::app()->db->beginTransaction();
                                    $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                                    $domain->partner_id = $partner->id;
                                    try {
                                        if ($domain->save()) {
                                            $resultTransaction = true;
                                        }
                                        if ($resultTransaction) {
                                            $transaction->commit();
                                            //$replyText = 'Отлично! Домен ' . $domain->site_domain . ' сайта '. $domain->site_id .' теперь привязан к Вам';
                                            $n = BotText::model()->find("t.name = 'domain_claim_success'");
                                            $nOld = ['{new_domain}', '{site_name}'];
                                            $nNew = [$domain->site_domain, $domain->site_domain];
                                            $replyText = str_replace($nOld, $nNew, $n->text);
                                        } else {
                                            $transaction->rollback();
                                            $n = BotText::model()->find("t.name = 'domain_claim_fail'");
                                            $replyText = $n->text;
                                        }
                                    } catch (Exception $e) {
                                        $transaction->rollback();
                                        $n = BotText::model()->find("t.name = 'domain_claim_fail'");
                                        $replyText = $n->text;
                                    }
                                } else {
                                    $n = BotText::model()->find("t.name = 'domain_claim_fail'");
                                    $replyText = $n->text;
                                }*/
                        }//display sites
                        elseif ($tg_data == 'kbd_sites') {
                            $criteria = new CDbCriteria;
                            $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                            $criteria->addCondition('partner_id = ' . $partner->id);
                            $criteria->addCondition('active = 1');
                            $criteria->addCondition('replaced = 0');
                            $domains = Domains::model()->findAll($criteria);
                            $partnerSites = [];
                            foreach ($domains as $d)
                                $partnerSites[] = $d->site_id;
                            $criteria = new CDbCriteria;
                            $criteria->addCondition('show_user = 1');
                            $criteria->addCondition('active = 1');
                            $criteria->addCondition('partner_id = 0');
                            $criteria->addCondition('replaced = 0');
                            $criteria->addNotInCondition('site_id', $partnerSites);
                            $criteria->order = 'site_id DESC';
                            $domains = Domains::model()->findAll($criteria);
                            if (!empty($domains)) {
                                foreach ($domains as $d) {
                                    if (!isset($siteList[$d->site_id])) {
                                        $site = Sites::model()->find('id = ' . $d->site_id);
                                        $siteList[$d->site_id] = $site;
                                    }
                                }
                                foreach ($siteList as $k => $sl) {
                                    //$caption = $site->description."\nДомены сайта " . $sl->name . ' :';
                                    $n = BotText::model()->find("t.name = 'sites_display_random'");
                                    $nOld = ['{site_description}', '{site_name}'];
                                    $nNew = [$sl->description, $sl->name];
                                    $caption = str_replace($nOld, $nNew, $n->text);

                                    $photo = $sl->getImagePath();
                                    $requestSent = true;
                                    $this->tgPhoto($chatId, [
                                        'photo' => $photo,
                                    ]);

                                    $keyboard = [[['text' => 'Получить домен сайта ' . $sl->name, 'callback_data' => 'kbd_domain_claim_' . $k]]];
                                    $this->tgRequest($chatId, array(
                                        'text' => $caption,
                                        'disable_web_page_preview' => false,
                                        'reply_markup' => json_encode(array('inline_keyboard' => $keyboard, "resize_keyboard" => true))
                                    ));
                                }
                            } else {
                                $n = BotText::model()->find("t.name = 'empty_msg'");
                                $replyText = $n->text;
                            }
                        } //Domain claim
                        elseif (strpos($tg_data, 'kbd_domain_claim_') !== false) {
                            $siteId = (int)filter_var($tg_data, FILTER_SANITIZE_NUMBER_INT);

                            $criteria = new CDbCriteria;
                            $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                            $criteria->addCondition('partner_id = ' . $partner->id);
                            $criteria->addCondition('active = 1');
                            $criteria->addCondition('replaced = 0');
                            $criteria->addCondition('site_id = ' . $siteId);
                            $partnerDomains = Domains::model()->find($criteria);


                            $criteria = new CDbCriteria;
                            $criteria->addCondition('show_user = 1');
                            $criteria->addCondition('active = 1');
                            $criteria->addCondition('partner_id = 0');
                            $criteria->addCondition('replaced = 0');
                            $criteria->addCondition('site_id = ' . $siteId);
                            $domain = Domains::model()->find($criteria);

                            if (!empty($domain) && empty($partnerDomains)) {
                                $resultTransaction = false;
                                $transaction = Yii::app()->db->beginTransaction();
                                $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                                $domain->partner_id = $partner->id;
                                $site = Sites::model()->findByPk($domain->site_id);
                                try {
                                    if ($domain->save()) {
                                        $resultTransaction = true;
                                    }
                                    if ($resultTransaction) {
                                        $transaction->commit();
                                        //$replyText = 'Отлично! Домен ' . $domain->site_domain . ' сайта '. $domain->site_id .' теперь привязан к Вам';
                                        $n = BotText::model()->find("t.name = 'domain_claim_success'");
                                        $nOld = ['{new_domain}', '{site_name}'];
                                        $nNew = [$domain->site_domain, $site->name];
                                        $replyText = str_replace($nOld, $nNew, $n->text);
                                    } else {
                                        $transaction->rollback();
                                        $n = BotText::model()->find("t.name = 'domain_claim_fail'");
                                        $replyText = $n->text;
                                    }
                                } catch (Exception $e) {
                                    $transaction->rollback();
                                    $n = BotText::model()->find("t.name = 'domain_claim_fail'");
                                    $replyText = $n->text;
                                }
                            } else {
                                $n = BotText::model()->find("t.name = 'domain_claim_fail'");
                                $replyText = $n->text;
                            }
                        } //Display mydomains
                        elseif ($tg_data == 'kbd_mydomains') {
                            $criteria = new CDbCriteria;
                            $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                            //$criteria->addCondition('partner_id = :pid', [':pid'=>$partner->id]);
                            $criteria->addCondition('partner_id = ' . $partner->id);

                            $domains = Domains::model()->findAll($criteria);
                            if (!empty($domains)) {
                                $replyText = "Список Ваших доменов: \n";
                                foreach ($domains as $d) {
                                    if (!isset($siteList[$d->site_id])) {
                                        $site = Sites::model()->find('id = ' . $d->site_id);
                                        $siteList[$d->site_id] = $site->name;
                                        $replyText .= "\n[" . $siteList[$d->site_id] . '] - ' . $d->site_domain;
                                    }
                                }
                                $keyboard = [
                                    [
                                        ['text' => 'Меню замены доменов', 'callback_data' => 'kbd_domain_replace']
                                    ],
                                ];
                            } else {
                                $n = BotText::model()->find("t.name = 'empty_msg'");
                                $replyText = $n->text;
                            }
                            //} Замена доменов
                            /*elseif ($tg_data == 'kbd_domain_replace') {
                                $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                                $criteria = new CDbCriteria;
                                $criteria->addCondition('partner_id = ' . $partner->id);
                                $criteria->addCondition('active = 1');
                                //$criteria->addCondition('show_user = 1');
                                $criteria->addCondition('replaced = 0');
                                $domainsPartner = Domains::model()->findAll($criteria);

                                foreach ($domainsPartner as $dp)
                                    $siteIds[] = $dp->site_id;

                                if (isset($siteIds)) {
                                    $criteria = new CDbCriteria;
                                    $criteria->addInCondition('id', $siteIds);
                                    $sites = Sites::model()->findAll($criteria);
                                    foreach ($sites as $s) {
                                        $siteList[$s->id] = $s;
                                    }
                                    //
                                    $criteria = new CDbCriteria;
                                    $criteria->addInCondition('site_id', $siteIds);
                                    $criteria->addCondition('partner_id = ' . $partner->id . ' OR partner_id = 0');
                                    $criteria->addCondition('active = 1');
                                    $criteria->addCondition('show_user = 1');
                                    $criteria->addCondition('replaced = 0');
                                    $criteria->order = 'site_id ASC';
                                    $domainsAll = Domains::model()->findAll($criteria);

                                    $replyText = count($domainsAll);
                                    foreach ($domainsAll as $d) {
                                        if (in_array($d, $domainsPartner)) {
                                            $oldDomain[$d->site_id] = $d->site_domain;
                                            $oldDomainId[$d->site_id] = $d->id;
                                        } else
                                            $domainList[$d->site_id][$d->id] = $d->site_domain;
                                    }
                                    foreach ($siteList as $k => $sl) {
                                        $keyboard = [];
                                        $keyboardLine = [];

                                        //$caption = 'Выберите новый домен для сайта ' . $sl->name . ' вместо ' . $oldDomain[$k] . ' :';
                                        $n = BotText::model()->find("t.name = 'domain_replace_get_new'");
                                        $nOld = ['{site_name}', '{old_domain}'];
                                        $nNew = [$sl->name, $oldDomain[$k]];
                                        $caption = str_replace($nOld, $nNew, $n->text);
                                        $photo = $sl->getImagePath();

                                        if (isset($domainList[$k]) && count($domainList[$k]) > 0) {
                                            foreach ($domainList[$k] as $dk => $dl) {
                                                $keyboardLine[] = ['text' => $dl, 'callback_data' => 'kbd_replace_domain_[[' . $k . '][' . $oldDomainId[$k] . '][' . $dk . ']]'];
                                            }
                                            $i = 0;
                                            $tempLine = [];
                                            foreach ($keyboardLine as $kl) {
                                                $i++;
                                                $tempLine[$i] = $kl;
                                                if ($i == 3) {
                                                    $keyboard[] = [$tempLine[1], $tempLine[2], $tempLine[3]];
                                                    $i = 0;
                                                }
                                            }
                                            if ($i != 0) {
                                                if ($i == 1)
                                                    $keyboard[] = [$tempLine[1]];
                                                if ($i == 2)
                                                    $keyboard[] = [$tempLine[1], $tempLine[2]];
                                            }
                                            $requestSent = true;
                                            $this->tgPhoto($chatId, [
                                                'photo' => $photo,
                                            ]);
                                            $this->tgRequest($chatId, array(
                                                'text' => $caption,
                                                'disable_web_page_preview' => false,
                                                'reply_markup' => json_encode(array('inline_keyboard' => $keyboard, "resize_keyboard" => true))
                                            ));
                                        }
                                    }
                                } else {
                                    $n = BotText::model()->find("t.name = 'empty_msg'");
                                    $replyText = $n->text;
                                }*/
                        } //Замена доменов random
                        elseif ($tg_data == 'kbd_domain_replace') {
                            $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                            $criteria = new CDbCriteria;
                            $criteria->addCondition('partner_id = ' . $partner->id);
                            $criteria->addCondition('active = 1');
                            //$criteria->addCondition('show_user = 1');
                            $criteria->addCondition('replaced = 0');
                            $domainsPartner = Domains::model()->findAll($criteria);

                            foreach ($domainsPartner as $dp)
                                $siteIds[] = $dp->site_id;

                            if (isset($siteIds)) {
                                $criteria = new CDbCriteria;
                                $criteria->addInCondition('id', $siteIds);
                                $sites = Sites::model()->findAll($criteria);
                                foreach ($sites as $s) {
                                    $siteList[$s->id] = $s;
                                }
                                $criteria = new CDbCriteria;
                                $criteria->addInCondition('site_id', $siteIds);
                                $criteria->addCondition('partner_id = ' . $partner->id . ' OR partner_id = 0');
                                $criteria->addCondition('active = 1');
                                $criteria->addCondition('show_user = 1');
                                $criteria->addCondition('replaced = 0');
                                $criteria->order = 'site_id ASC';
                                $domainsAll = Domains::model()->findAll($criteria);

                                foreach ($domainsAll as $d) {
                                    if (in_array($d, $domainsPartner)) {
                                        $oldDomain[$d->site_id] = $d->site_domain;
                                        $oldDomainId[$d->site_id] = $d->id;
                                    } else
                                        $domainList[$d->site_id][$d->id] = $d->site_domain;
                                }
                                foreach ($siteList as $k => $sl) {
                                    $n = BotText::model()->find("t.name = 'domain_replace_get_new_random'");
                                    $nOld = ['{site_name}', '{old_domain}'];
                                    $nNew = [$sl->name, $oldDomain[$k]];
                                    $caption = str_replace($nOld, $nNew, $n->text);
                                    $photo = $sl->getImagePath();

                                    if (isset($domainList[$k]) && count($domainList[$k]) > 0) {
                                        $keyboard = [
                                            [
                                                ['text' => 'Заменить домен сайта ' . $sl->name, 'callback_data' => 'kbd_replace_domain_[[' . $k . '][' . $oldDomainId[$k] . ']]'],
                                            ]
                                        ];

                                        $requestSent = true;
                                        $this->tgPhoto($chatId, [
                                            'photo' => $photo,
                                        ]);
                                        $this->tgRequest($chatId, array(
                                            'text' => $caption,
                                            'disable_web_page_preview' => false,
                                            'reply_markup' => json_encode(array('inline_keyboard' => $keyboard, "resize_keyboard" => true))
                                        ));
                                    }
                                }
                            } else {
                                $n = BotText::model()->find("t.name = 'empty_msg'");
                                $replyText = $n->text;
                            }
                            //} Замена домена кнопка создание заявки
                            /*elseif (strpos($tg_data, 'kbd_replace_domain_[') !== false) {
                                preg_match_all('#\[(.*?)\]#', $tg_data, $match);
                                //$dataSiteId = (int)filter_var($match[1][0], FILTER_SANITIZE_NUMBER_INT);
                                $dataOldDomain = (int)filter_var($match[1][1], FILTER_SANITIZE_NUMBER_INT);
                                $dataDomain = (int)filter_var($match[1][2], FILTER_SANITIZE_NUMBER_INT);
                                $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);

                                $criteria = new CDbCriteria();
                                $criteria->addCondition('partner_id = ' . $partner->id);
                                $criteria->addCondition('comment IS NULL');
                                $drAll = DomainsReplace::model()->findAll($criteria);
                                foreach ($drAll as $da)
                                    $da->delete();

                                $domain = Domains::model()->findByPk($dataDomain);
                                $domainOld = Domains::model()->findByPk($dataOldDomain);
                                $site = Sites::model()->find('id = :id', [':id' => $domain->site_id]);

                                $dr = new DomainsReplace();
                                $dr->partner_id = $partner->id;
                                $dr->domain_id = $dataDomain;
                                $dr->domain_old_id = $dataOldDomain;
                                $dr->created_at = date('Y-m-d H:i:s');
                                $dr->save();
                                //$replyText = "Заявка на смену домена сайта [".$site->name."] с [".$domainOld->site_domain."] на [".$domain->site_domain."] принята.\nДля того, чтобы произвести смену домена, пожалуйста, напишите причину смены через команду:\n/replace *текст причины*";
                                $n = BotText::model()->find("t.name = 'change_domain_app_msg'");
                                $nOld = ['{site_name}', '{old_domain}', '{new_domain}'];
                                $nNew = [$site->name, $domainOld->site_domain, $domain->site_domain];
                                $replyText = str_replace($nOld, $nNew, $n->text);*/
                        } //Замена домена кнопка создание заявки random
                        elseif (strpos($tg_data, 'kbd_replace_domain_') !== false) {
                            preg_match_all('#\[(.*?)\]#', $tg_data, $match);
                            $dataSiteId = (int)filter_var($match[1][0], FILTER_SANITIZE_NUMBER_INT);
                            $dataOldDomain = (int)filter_var($match[1][1], FILTER_SANITIZE_NUMBER_INT);
                            //$dataDomain = (int)filter_var($match[1][2], FILTER_SANITIZE_NUMBER_INT);
                            $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);

                            $criteria = new CDbCriteria();
                            $criteria->addCondition('partner_id = ' . $partner->id);
                            $criteria->addCondition('comment IS NULL');
                            $drAll = DomainsReplace::model()->findAll($criteria);
                            foreach ($drAll as $da)
                                $da->delete();

                            $criteria = new CDbCriteria();
                            $criteria->addCondition('partner_id = 0');
                            $criteria->addCondition('active = 1');
                            $criteria->addCondition('show_user = 1');
                            $criteria->addCondition('replaced = 0');
                            $criteria->addCondition('site_id = ' . $dataSiteId);
                            $criteria->order = 'site_id ASC';
                            $domain = Domains::model()->find($criteria);

                            if (!empty($domain)) {
                                $domainOld = Domains::model()->findByPk($dataOldDomain);
                                $site = Sites::model()->find('id = :id', [':id' => $domain->site_id]);

                                $dr = new DomainsReplace();
                                $dr->partner_id = $partner->id;
                                $dr->domain_id = 0;
                                $dr->domain_old_id = $dataOldDomain;
                                $dr->created_at = date('Y-m-d H:i:s');
                                $dr->save();
                                //$replyText = "Заявка на смену домена сайта [".$site->name."] с [".$domainOld->site_domain."] на [".$domain->site_domain."] принята.\nДля того, чтобы произвести смену домена, пожалуйста, напишите причину смены через команду:\n/replace *текст причины*";
                                $n = BotText::model()->find("t.name = 'change_domain_app_msg_random'");
                                $nOld = ['{site_name}', '{old_domain}'];
                                $nNew = [$site->name, $domainOld->site_domain];
                                $replyText = str_replace($nOld, $nNew, $n->text);
                            } else {
                                $n = BotText::model()->find("t.name = 'keyboard_old'");
                                $replyText = $n->text;
                            }

                        }
                    }
                }
            }

            if ($isMessage) {
                if ($isPartner) {
                    if ($text == '/start' || $text == '.ыефке') {
                        $keyboard = [
                            [
                                ['text' => 'Сайты под залив', 'callback_data' => 'kbd_sites'],
                                ['text' => 'Моя статистика', 'callback_data' => 'kbd_mystats'],
                                ['text' => 'Топ партнеров', 'callback_data' => 'kbd_topPartners'],
                            ],
                            [
                                ['text' => 'Выплаты', 'callback_data' => 'kbd_payments'],
                                ['text' => 'Чат', 'callback_data' => 'kbd_chat'],
                                ['text' => 'Отстук о заявках', 'callback_data' => 'kbd_info']
                            ],
                            [
                                ['text' => 'Мои Домены', 'callback_data' => 'kbd_mydomains'],
                            ]
                        ];
                        $n = BotText::model()->find("t.name = 'keyboard_reply'");
                        $replyText = $n->text;
                    } //Partner Address
                    elseif (strpos($text, '/address') !== false) {
                        //$text = trim(substr($text, 8));
                        $text = trim(preg_replace('/\/address/ui', '', $text));
                        $resultTransaction = false;
                        $transaction = Yii::app()->db->beginTransaction();
                        try {
                            $criteria = new CDbCriteria();
                            $criteria->addCondition('tg_login = :tg_login');
                            $criteria->params[':tg_login'] = $tg_login;
                            $partner = Partners::model()->find($criteria);
                            $partner->address = $text;
                            if ($partner->save()) {
                                $resultTransaction = true;
                            }
                            if ($resultTransaction) {
                                $transaction->commit();
                                $n = BotText::model()->find("t.name = 'address_change_success'");
                                $replyText = $n->text;
                            } else {
                                $transaction->rollback();
                                $n = BotText::model()->find("t.name = 'address_change_fail'");
                                $replyText = $n->text;
                            }
                        } catch (Exception $e) {
                            $transaction->rollback();
                            $n = BotText::model()->find("t.name = 'address_change_fail'");
                            $replyText = $n->text;
                        }
                    } //Причина смены домена
                    elseif (strpos($text, '/replace') !== false) {
                        $text = trim(preg_replace('/\/replace/ui', '', $text));
                        $resultTransaction = false;
                        $transaction = Yii::app()->db->beginTransaction();
                        try {
                            $partner = Partners::model()->find('tg_login = :tl', [':tl' => $tg_login]);
                            $criteria = new CDbCriteria();
                            $criteria->addCondition('partner_id = ' . $partner->id);
                            $criteria->addCondition('comment IS NULL');
                            $domainsReplace = DomainsReplace::model()->find($criteria);

                            $domainOld = Domains::model()->findByPk($domainsReplace->domain_old_id);

                            $criteria = new CDbCriteria();
                            $criteria->addCondition('partner_id = 0');
                            $criteria->addCondition('active = 1');
                            $criteria->addCondition('show_user = 1');
                            $criteria->addCondition('replaced = 0');
                            $criteria->addCondition('site_id = ' . $domainOld->site_id);
                            $criteria->order = 'site_id ASC';
                            $domain = Domains::model()->find($criteria);

                            if (!empty($domain) && $domain->partner_id == 0 && $domainOld->partner_id == $partner->id && $domain->active == 1 && $domain->show_user == 1 && $domain->replaced == 0) {
                                $site = Sites::model()->find('id = :id', [':id' => $domain->site_id]);
                                $domainsReplace->comment = $text;
                                $domainsReplace->domain_id = $domain->id;
                                $domain->partner_id = $partner->id;
                                $domainOld->partner_id = 0;
                                $domainOld->replaced = 1;
                                if ($domainsReplace->save() && $domain->save() && $domainOld->save()) {
                                    $resultTransaction = true;
                                }
                                if ($resultTransaction) {
                                    $transaction->commit();
                                    $n = BotText::model()->find("t.name = 'replace_domain_success'");
                                    $nOld = ['{site_name}', '{old_domain}', '{new_domain}'];
                                    $nNew = [$site->name, $domainOld->site_domain, $domain->site_domain];
                                    $replyText = str_replace($nOld, $nNew, $n->text);
                                } else {
                                    $transaction->rollback();
                                    $n = BotText::model()->find("t.name = 'replace_domain_fail'");
                                    $replyText = $n->text;
                                }
                            } else {
                                $transaction->rollback();
                                $criteria = new CDbCriteria();
                                $criteria->addCondition('partner_id = ' . $partner->id);
                                $criteria->addCondition('comment IS NULL');
                                $drAll = DomainsReplace::model()->findAll($criteria);
                                foreach ($drAll as $da) {
                                    $da->delete();
                                }
                                $n = BotText::model()->find("t.name = 'replace_domain_fail_taken'");
                                $replyText = $n->text;
                            }
                        } catch (Exception $e) {
                            $transaction->rollback();
                            $n = BotText::model()->find("t.name = 'replace_domain_fail'");
                            $replyText = $n->text;
                        }
                    }
                } else {
                    if ($text == '/start') {
                        $n = BotText::model()->find("t.name = 'application-start'");
                        $replyText = $n->text;
                    } else {
                        $application = new Applications();
                        $application->tg_login = $tg_login;
                        $application->description = $tg_message;
                        $application->created_at = date('Y-m-d H:i:s');
                        $application->chat_id = $chatId;
                        $application->save();

                        $n = BotText::model()->find("t.name = 'application-start-reply'");
                        $replyText = $n->text;
                    }
                }
            }

            //Request
            if (!$requestSent) {
                if (empty($keyboard)) {
                    $this->tgRequest($chatId, array(
                        'text' => $replyText,
                        'disable_web_page_preview' => false,
                    ));
                } else {
                    $this->tgRequest($chatId, array(
                        'text' => $replyText,
                        'disable_web_page_preview' => false,
                        'reply_markup' => json_encode(array('inline_keyboard' => $keyboard, "resize_keyboard" => true))
                    ));
                }
            }
        }
    }
}