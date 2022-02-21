    public function actionNew_domain()
    {
        $resultTransaction = false;
        $transaction = Yii::app()->db->beginTransaction();
        $user = Users::model()->with('roles')->findByPk(Yii::app()->user->id);
        $userRight = UserRight::model()->find('user_id = ' . $user->id);
        if ($user->roles[0]->name == 'admin' || $userRight['create_domains']) {
            try {
                $errorAddField = false;
                $errorAddFieldText = '';
                $domain = new Domains();

                $partnersData = Partners::model()->findAll();
                $partners = [];
                $partners[0] = 'Не выбран';
                foreach ($partnersData as $pd)
                    $partners[$pd->id] = $pd->tg_login;

                $sitesData = Sites::model()->findAll();
                $sites = [];
                foreach ($sitesData as $pd)
                    $sites[$pd->id] = $pd->name;

                if (isset($_POST['Domains'])) {
                    if ($user->roles[0]->name == 'admin' || $userRight->create_domains == 1) {
                        $domain->attributes = $_POST['Domains'];
                    }
                    if ($domain->save()) {
                        $resultTransaction = true;
                    }
                    if ($resultTransaction) {
                        $transaction->commit();
                        Yii::app()->user->setFlash('success', 'Изменения сохранены');
                        //$this->redirect(array('partners_page' => $partner));
                        $this->redirect(array('update_domain', 'id' => $domain->id));
                    } else {
                        $transaction->rollback();
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }
            $this->render('domain_form',
                array(
                    'domain' => $domain,
                    'user' => $user,
                    'sites' => $sites,
                    'partners' => $partners,
                    'errorAddField' => $errorAddField,
                    'errorAddFieldText' => $errorAddFieldText,
                )
            );
        } else {
            throw new CHttpException(412, 'no_access_new_client');
        }
    }

    public function tgRequest($chatId, $options)
    {
        $channelChatId = Yii::app()->params['TG_CHANNEL_ID'];
        if ($chatId == 'channel')
            $chatId = $channelChatId;
        //$token = 'xxx';
        $api_url = Yii::app()->params['TG_API_URL'];
        $url = $api_url . "sendMessage?chat_id=" . $chatId;
        $post_fields = $options;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
    }

    public function actionBot_text_page(){
        $user = Users::model()->findByPk(Yii::app()->user->id);
        $botText = new BotText();
        $userRight = UserRight::model()->find('user_id = ' . Yii::app()->user->id);

        if ($userRight->edit_bot_text) {
            if (isset($_GET['BotText']) && isset($_GET['BotText']['search'])) {
                $botText->attributes = $_GET['BotText'];
            }

            $botTextTableData = $botText->searchBotText(null, false);
            //$l = $botTextTableData->getData();

            $this->render('bot_text_page', array(
                'user' => $user,
                'botText' => $botText,
                'botTextTableData' => $botTextTableData,
            ));
        } else {
            $this->redirect('NotFoundPage');
        }
    }

    public function actionUpdate_bot_text()
    {
        $resultTransaction = false;
        $transaction = Yii::app()->db->beginTransaction();
        $user = Users::model()->with('roles')->findByPk(Yii::app()->user->id);
        $id = Yii::app()->request->getParam('id');
        $userRight = UserRight::model()->find('user_id = ' . $user->id);
        if ($user->roles[0]->name == 'admin' || $userRight['edit_bot_text']) {
            try {
                $errorAddField = false;
                $errorAddFieldText = '';
                $botText = BotText::model()->findByPk($id);
                if (isset($_POST['BotText'])) {
                    if ($user->roles[0]->name == 'admin' || $userRight->edit_bot_text == 1) {
                        $botText->attributes = $_POST['BotText'];
                    }
                    if ($botText->save()) {
                        $resultTransaction = true;
                    }
                    if ($resultTransaction) {
                        $botText->save();
                        $transaction->commit();
                        Yii::app()->user->setFlash('success', 'Изменения сохранены');
                        //$this->redirect(array('partners_page' => $partner));
                        $this->redirect(array('update_bot_text', 'id' => $botText->id));
                    } else {
                        $transaction->rollback();
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }
            $this->render('bot_text_form',
                array(
                    'botText' => $botText,
                    'user' => $user,
                    'errorAddField' => $errorAddField,
                    'errorAddFieldText' => $errorAddFieldText,
                )
            );
        } else {
            $this->redirect('NotFoundPage');
        }
    }

    public function actionApplications_page($partnersStatusFilter = 0)
    {
        $user = Users::model()->findByPk(Yii::app()->user->id);
        $application = new Applications();
        $userRight = UserRight::model()->find('user_id = ' . Yii::app()->user->id);
        $model = 'Applications';
        if ($userRight->check_applications) {
            if (isset($_GET[$model]) && isset($_GET[$model]['search'])) {
                $application->attributes = $_GET[$model];
            }

            $applicationTableData = $application->searchApplication(null, false);
            //$l = $applicationTableData->getData();

            $this->render('applications_page', array(
                'user' => $user,
                'application' => $application,
                'applicationTableData' => $applicationTableData,
            ));
        } else {
            $this->redirect('NotFoundPage');
        }
    }

    public function actionEdit_application()
    {
        $resultTransaction = false;
        $transaction = Yii::app()->db->beginTransaction();
        $id = Yii::app()->request->getParam('id');
        $user = Users::model()->with('roles')->findByPk(Yii::app()->user->id);
        $userRight = UserRight::model()->find('user_id = ' . $user->id);
        if ($user->roles[0]->name == 'admin' || $userRight['create_partners']) {
            try {
                $errorAddFieldText = '';
                $application = Applications::model()->findByPk($id);
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
                if (isset($_POST['Partners'])) {
                    if ($user->roles[0]->name == 'admin' || $userRight->create_partners == 1) {
                        $partnersData = Partners::model()->exists('tg_login = :tl', [':tl' => $application->tg_login]);
                        if (!$partnersData) {
                            if (isset($_POST['Partners']['new'])) {
                                $partner = new Partners();
                                $partner->tg_login = $application->tg_login;
                                $partner->status = Partners::STATUS_PARTNER;
                                $partner->created_at = date('Y-m-d H:i:s');
                                $partner->updated_at = date('Y-m-d H:i:s');
                                $partner->chat_id = $application->chat_id;
                                if ($partner->save()) {
                                     $resultTransaction = true;
                                    /*$text = '🥳 Поздравляем! Вас приняли в нашу команду.
🔔 Отстук о заявках: (' . Yii::app()->params['TG_CHANNEL_LINK'] . ')
💬 Чат: (' . Yii::app()->params['TG_GROUP_LINK'] . ')
🗞 Новостной канал: (ссылка на канал, дам позже)
(обязательно включайте уведомления)
Если у вас возникли какие-либо вопросы, пишите @dappparthners_support';*/
                                    $n = BotText::model()->find("t.name = 'application_approved'");
                                    $nOld = ['{tg_channel_link}','{tg_group_link}','{tg_news_link}'];
                                    $nNew = [Yii::app()->params['TG_CHANNEL_LINK'],Yii::app()->params['TG_GROUP_LINK'],''];
                                    if ($application->chat_id != 0) {
                                        $this->tgRequest($application->chat_id, array(
                                            'text' => str_replace($nOld, $nNew, $n->text),
                                            'disable_web_page_preview' => false,
                                            'reply_markup' => json_encode(array('inline_keyboard' => $keyboard, "resize_keyboard" => true))
                                        ));
                                    }
                                }
                            } elseif (isset($_POST['Partners']['decline'])) {
                                $resultTransaction = true;
                                $n = BotText::model()->find("t.name = 'application_declined'");
                                if ($application->chat_id != 0) {
                                    $this->tgRequest($application->chat_id, array(
                                        'text' => $n->text,
                                        'disable_web_page_preview' => false,
                                    ));
                                }
                            }
                            $appAll = Applications::model()->findAll('tg_login = :tl', [':tl' => $application->tg_login]);
                            foreach ($appAll as $aa)
                                $aa->delete();
                        } else {
                            $partner = Partners::model()->find("tg_login = '".$application->tg_login."'");
                            if ($partner->status == Partners::STATUS_BANNED){
                                $partner->status = Partners::STATUS_PARTNER;
                                $partner->updated_at = date('Y-m-d H:i:s');

                                if ($partner->save()) {
                                    $resultTransaction = true;
                                    $n = BotText::model()->find("t.name = 'application_approved_unban'");
                                    $nOld = ['{tg_channel_link}', '{tg_group_link}', '{tg_news_link}'];
                                    $nNew = [Yii::app()->params['TG_CHANNEL_LINK'], Yii::app()->params['TG_GROUP_LINK'], ''];
                                    if ($application->chat_id != 0) {
                                        $this->tgRequest($application->chat_id, array(
                                            'text' => str_replace($nOld, $nNew, $n->text),
                                            'disable_web_page_preview' => false,
                                            'reply_markup' => json_encode(array('inline_keyboard' => $keyboard, "resize_keyboard" => true))
                                        ));
                                    }
                                }
                            } else {
                                $errorAddFieldText = 'Такой партнер уже существует';
                            }
                            $appAll = Applications::model()->findAll('tg_login = :tl', [':tl' => $application->tg_login]);
                            foreach ($appAll as $aa)
                                $aa->delete();
                        }
                    }
                    if ($resultTransaction) {
                        $transaction->commit();
                        Yii::app()->user->setFlash('success', 'Изменения сохранены');
                        $this->redirect('/page/applications_page');
                    } else {
                        $transaction->rollback();
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }
            $this->render('edit_application',
                array(
                    'application' => $application,
                    'user' => $user,
                    'userRight' => $userRight,
                    'errorAddFieldText' => $errorAddFieldText,
                )
            );
        } else {
            $this->redirect('NotFoundPage');
        }
    }

    public function actionUpdate_domain()
    {
        $resultTransaction = false;
        $transaction = Yii::app()->db->beginTransaction();
        $user = Users::model()->with('roles')->findByPk(Yii::app()->user->id);
        $id = Yii::app()->request->getParam('id');
        $userRight = UserRight::model()->find('user_id = ' . $user->id);
        if ($user->roles[0]->name == 'admin' || $userRight['create_domains']) {
            try {
                $errorAddField = false;
                $errorAddFieldText = '';
                $messageBool = false;
                $domain = Domains::model()->findByPk($id);
                $domainActiveBool = $domain->active;

                $partnersData = Partners::model()->findAll();
                $partners = [];
                $partners[0] = 'Не выбран';
                foreach ($partnersData as $pd)
                    $partners[$pd->id] = $pd->tg_login;

                $sitesData = Sites::model()->findAll();
                $sites = [];
                foreach ($sitesData as $pd)
                    $sites[$pd->id] = $pd->name;

                if (isset($_POST['Domains'])) {
                    if ($user->roles[0]->name == 'admin' || $userRight->create_domains == 1) {
                        $domain->attributes = $_POST['Domains'];
                        if ($domainActiveBool == 1 && $domainActiveBool != $domain->active) {
                            $partner = Partners::model()->findByPk($domain->partner_id);
                            $domain->partner_id = 0;
                            $site = Sites::model()->findByPk($domain->site_id);
                            $messageBool = true;
                        }
                    }
                    if ($domain->save()) {
                        $resultTransaction = true;
                    }
                    if ($resultTransaction) {
                        if ($messageBool == true && isset($partner->chat_id)) {
                            $n = BotText::model()->find("t.name = 'notification_domain_disabled'");
                            $nOld = ['{domain}','{site_name}'];
                            $nNew = [$domain->site_domain,$site->name];
                            $replyText = str_replace($nOld, $nNew, $n->text);
                            //$text = 'Статус домена [' . $domain->site_domain . '] сайта ' . $site->name . " был изменен на НЕ АКТИВНЫЙ\nПожалуйста, отключите трафик";
                            $this->tgRequest($partner->chat_id, array(
                                'text' => $replyText,
                                'disable_web_page_preview' => false,
                            ));
                        }
                        //
                        $transaction->commit();
                        Yii::app()->user->setFlash('success', 'Изменения сохранены');
                        $this->redirect(array('update_domain', 'id' => $domain->id));
                    } else {
                        $transaction->rollback();
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }
            $this->render('domain_form',
                array(
                    'domain' => $domain,
                    'user' => $user,
                    'partners' => $partners,
                    'sites' => $sites,
                    'errorAddField' => $errorAddField,
                    'errorAddFieldText' => $errorAddFieldText,
                )
            );
        } else {
            $this->redirect('NotFoundPage');
        }
    }

    public function actionDomains_page($partnersStatusFilter = 0)
    {
        $user = Users::model()->findByPk(Yii::app()->user->id);
        $domains = new Domains();
        $userRight = UserRight::model()->find('user_id = ' . Yii::app()->user->id);

        //$domainsTableData = $domains->searchDomain(null, false);

        if ($userRight->create_domains) {
            if (isset($_GET['Domains']) && isset($_GET['Domains']['search'])) {
                $domains->attributes = $_GET['Domains'];
            }

            $domainsTableData = $domains->searchDomain(null, false);
            //$l = Domains::model()->findAll();
            $l = $domainsTableData->getData();

            $partnersId = [];
            foreach ($l as $d)
                $partnersId[] = $d->partner_id;
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $partnersId);
            $partnersData = Partners::model()->findAll($criteria);
            $partners = [];
            foreach ($partnersData as $pd)
                $partners[$pd->id] = $pd;
            $sitesId = [];
            foreach ($l as $d)
                $sitesId[] = $d->site_id;
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $sitesId);
            $sitesData = Sites::model()->findAll($criteria);
            $sites = [];
            foreach ($sitesData as $pd)
                $sites[$pd->id] = $pd;

            $this->render('domains_page', array(
                'user' => $user,
                'partners' => $partners,
                'sites' => $sites,
                'domains' => $domains,
                'domainsTableData' => $domainsTableData,
            ));
        } else {
            $this->redirect('NotFoundPage');
        }
    }

    public function actionSites_page()
    {
        $user = Users::model()->findByPk(Yii::app()->user->id);
        $site = new Sites();
        $userRight = UserRight::model()->find('user_id = ' . Yii::app()->user->id);

        if ($userRight->create_sites) {
            if (isset($_GET['Sites']) && isset($_GET['Sites']['search'])) {
                $site->attributes = $_GET['Sites'];
            }

            $siteTableData = $site->searchSite(null, false);
            //$l = $siteTableData->getData();

            $this->render('sites_page', array(
                'user' => $user,
                'site' => $site,
                'siteTableData' => $siteTableData,
            ));
        } else {
            $this->redirect('NotFoundPage');
        }
    }

    public function actionNew_site()
    {
        $resultTransaction = false;
        $transaction = Yii::app()->db->beginTransaction();
        $user = Users::model()->with('roles')->findByPk(Yii::app()->user->id);
        $userRight = UserRight::model()->find('user_id = ' . $user->id);
        if ($user->roles[0]->name == 'admin' || $userRight['create_sites']) {
            try {
                $errorAddField = false;
                $errorAddFieldText = '';
                $site = new Sites();

                if (isset($_POST['Sites'])) {
                    if ($user->roles[0]->name == 'admin' || $userRight->create_sites == 1) {
                        $site->attributes = $_POST['Sites'];
                        $site->img = CUploadedFile::getInstance($site, 'img');
                    }
                    if ($site->save()) {
                        $resultTransaction = true;
                    }
                    if ($resultTransaction) {
                        $path = Yii::getPathOfAlias('webroot') . '/upload/' . $site->img->getName();
                        $site->img->saveAs($path);
                        $transaction->commit();
                        Yii::app()->user->setFlash('success', 'Изменения сохранены');
                        $this->redirect(array('update_site', 'id' => $site->id));
                    } else {
                        $transaction->rollback();
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }
            $this->render('site_form',
                array(
                    'site' => $site,
                    'user' => $user,
                    'errorAddField' => $errorAddField,
                    'errorAddFieldText' => $errorAddFieldText,
                )
            );
        } else {
            $this->redirect('NotFoundPage');
        }
    }

    public function actionUpdate_site()
    {
        $resultTransaction = false;
        $transaction = Yii::app()->db->beginTransaction();
        $user = Users::model()->with('roles')->findByPk(Yii::app()->user->id);
        $id = Yii::app()->request->getParam('id');
        $userRight = UserRight::model()->find('user_id = ' . $user->id);
        if ($user->roles[0]->name == 'admin' || $userRight['create_sites']) {
            try {
                $errorAddField = false;
                $errorAddFieldText = '';
                $site = Sites::model()->findByPk($id);
                if (isset($_POST['Sites'])) {
                    if ($user->roles[0]->name == 'admin' || $userRight->create_sites == 1) {
                        $site->attributes = $_POST['Sites'];
                        $site->img = CUploadedFile::getInstance($site, 'img');
                    }
                    if ($site->save()) {
                        $resultTransaction = true;
                    }
                    if ($resultTransaction) {
                        $imgName = $site->id . '_img.' . $site->img->getExtensionName();
                        $path = Yii::getPathOfAlias('webroot') . '/site_imgs/' . $imgName;
                        $site->img->saveAs($path);
                        $site->img = $imgName;
                        $site->save();
                        $transaction->commit();
                        Yii::app()->user->setFlash('success', 'Изменения сохранены');
                        //$this->redirect(array('partners_page' => $partner));
                        $this->redirect(array('update_site', 'id' => $site->id));
                    } else {
                        $transaction->rollback();
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }
            $this->render('site_form',
                array(
                    'site' => $site,
                    'user' => $user,
                    'errorAddField' => $errorAddField,
                    'errorAddFieldText' => $errorAddFieldText,
                )
            );
        } else {
            $this->redirect('NotFoundPage');
        }
    }