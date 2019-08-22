<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\Constant;
use yii\helpers\Url;
use \app\components\SessionContact;


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);


    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => [

            // Date Range Selector Menu ------------------------

            Yii::$app->user->isGuest === false ? (
                \app\components\helpers\DateRangeHelper::buildMenuItem(Url::current())
            ) : ('')
        ]
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [


            Yii::$app->user->isGuest === false ? (  // Gymv Menu -----------------------------------------------------------------------------
                [
                    'label' => 'GymV',
                    'items' => [
                        [
                            'label'  => \Yii::t('app', 'Home'),
                            'encode' => false,
                            'url'    => ['/gymv']
                        ],
                        SessionContact::getContactId() !== null ? (
                            [
                                'label'  => \Yii::t('app', 'Contact'),
                                'encode' => false,
                                'url'    => ['/contact/view', 'id' => SessionContact::getContactId()]
                            ]
                        ) : (''),
                        SessionContact::getBankAccountId() !== null ? (
                            [
                                'label'  => \Yii::t('app', 'Bank Account'),
                                'encode' => false,
                                'url'    => ['/bank-account/view', 'id' => SessionContact::getBankAccountId()]
                            ]
                        ) : (''),
                    ],
                ]
            ) : (''),



            Yii::$app->user->isGuest === false ? (  // Management Menu -----------------------------------------------------------------------------
                [
                    'label' => \Yii::t('app', 'Manage'),
                    'items' => [
                        [
                            'label'  => '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' . \Yii::t('app', 'Contacts'),
                            'encode' => false,
                            'url'    => ['/contact/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-home" aria-hidden="true"></span> ' . \Yii::t('app', 'Address'),
                            'encode' => false,
                            'url'    => ['/address/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> ' . \Yii::t('app', 'Bank Account'),
                            'encode' => false,
                            'url'    => ['/bank-account/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> ' . \Yii::t('app', 'Transactions'),
                            'encode' => false,
                            'url'    => ['/transaction/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> ' . \Yii::t('app', 'Orders'),
                            'encode' => false,
                            'url'    => ['/order/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> ' . \Yii::t('app', 'Products'),
                            'encode' => false,
                            'url'    => ['/product/index']
                        ],
                        '<li class="divider"></li>',
                        [
                            'label'  => '<span class="glyphicon glyphicon-th" aria-hidden="true"></span> ' . \Yii::t('app', 'Categories'),
                            'encode' => false,
                            'url'    => ['/category/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> ' . \Yii::t('app', 'Attachments'),
                            'encode' => false,
                            'url'    => ['/attachment/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-tags" aria-hidden="true"></span> ' . \Yii::t('app', 'Tags'),
                            'encode' => false,
                            'url'    => ['/tag']
                        ],
                    ]

                ]
            ) : (''),

            \Yii::$app->user->can('admin') ? (  // Administration Menu -----------------------------------------------------------------------------
                [
                    'label' => 'Administration',
                    'items' => [
                        /*
                        [
                            'label'  => \Yii::t('app', 'Dashboard'),
                            'encode' => false,
                            'url'    => ['/admin/home']
                        ],
                        */
                        [
                            'label'  => \Yii::t('app', 'Configuration'),
                            'encode' => false,
                            'url'    => ['/config/index']
                        ],
                        [
                            'label'  => \Yii::t('app', 'Users'),
                            'encode' => false,
                            'url'    => ['/user/admin']
                        ],
                        [
                            'label'  => \Yii::t('app', 'Record History'),
                            'encode' => false,
                            'url'    => ['/record-history']
                        ],
                        [
                            'label'  => \Yii::t('app', 'Sessions'),
                            'encode' => false,
                            'url'    => ['/session']
                        ],
                        [
                            'label'  => \Yii::t('app', 'DB Backup/restore'),
                            'encode' => false,
                            'url'    => ['/db-manager']
                        ],
                        '<li class="divider"></li>',
                        [
                            'label'  => '<span class="glyphicon glyphicon-console" aria-hidden="true"></span> ' . \Yii::t('app', 'Web Shell'),
                            'encode' => false,
                            'url'    => ['/webshell'],
                            'linkOptions' => ['target' => 'webshell']
                        ],

                    ],
                ]
            ) : (''),

            Yii::$app->user->isGuest ? (
                ['label' => \Yii::t('app', 'Login'), 'url' => ['/user/security/login']]
            ) : (
                '<li>'
                    . Html::beginForm(['/user/security/logout'], 'post')
                    . Html::submitButton(
                        '<span class="glyphicon glyphicon-off" aria-hidden="true"></span> (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout', 'title' => \Yii::t('app', 'Logout')]
                    )
                    . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>
    <?php
        $containerClass = array_key_exists(Constant::PARAM_FLUID_LAYOUT, Yii::$app->params) && Yii::$app->params[Constant::PARAM_FLUID_LAYOUT] ===  true
            ? 'container-fluid'
            : 'container';
    ?>
    <div class="<?= $containerClass ?>">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Bob <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
