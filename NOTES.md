# Requirements

# Build App (app-basic template)

```
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
```

## Yii2-usuario

- https://yii2-usuario.readthedocs.io/en/latest
- to install, `composer` required PHP > 7.0.0 in command line
- command run in a Windows PC

- update `./config/console.php`
```php 
'components' => [
    // .. other components ...
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ]
], 
'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
    ]
]
```
- update `./config/web.php`
  - remove default **User** component
  - add component **authManager**
  - add module **user**
```php 
'components' => [
    // remove existing component 'USER'
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ] 
],
'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
        'administrators' => ['admin']
    ]
]
```


```bash
composer require 2amigos/yii2-usuario:~1.0

# apply migrations
.\yii migrate --migrationNamespaces=Da\User\Migration
.\yii migrate --migrationPath=@yii/rbac/migrations

# create admin user and 'admin' role
.\yii user/create admin@email.com admin 123456  admin
```

- go to : http://localhost/[PATH]/index.php?r=user/admin

## Active Rcord History

- refers to doc https://github.com/bupy7/yii2-activerecord-history

```bash
composer require --prefer-dist bupy7/yii2-activerecord-history "*"
./yii migrate/up --migrationPath=@bupy7/activerecord/history/migrations
```
