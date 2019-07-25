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
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            ['label' => 'GymV', 'url' => ['/gymv']],
            
            \Yii::$app->user->can('admin') ? (
                [
                    'label' => 'Administration',
                    'items' => [
                        [
                            'label'  => \Yii::t('app', 'Dashboard'),
                            'encode' => false,
                            'url'    => ['/admin/home']
                        ],
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

            Yii::$app->user->isGuest === false ? (
                [
                    'label' => 'Manage',
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
                            'label'  => '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> Bank Accounts',
                            'encode' => false,
                            'url'    => ['/bank-account/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> Transactions',
                            'encode' => false,
                            'url'    => ['/transaction/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Orders',
                            'encode' => false,
                            'url'    => ['/order/index']
                        ],
                        '<li class="divider"></li>',
                        [
                            'label'  => 'Products',
                            'encode' => false,
                            'url'    => ['/product/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-th" aria-hidden="true"></span> Manage Categories',
                            'encode' => false,
                            'url'    => ['/category/index']
                        ],
                        [
                            'label'  => '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Attachment',
                            'encode' => false,
                            'url'    => ['/attachment/index']
                        ],

                    ]

                ]
            ) : (''),

            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/user/security/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/user/security/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
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
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
