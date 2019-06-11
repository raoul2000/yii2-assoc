@echo off

@setlocal

set YII_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%YII_PATH%yii" migrate --migrationPath=@yii/rbac/migrations --interactive=0
"%PHP_COMMAND%" "%YII_PATH%yii" migrate --migrationPath=@bupy7/activerecord/history/migrations --interactive=0

rem Create admin user (for dev only)
"%PHP_COMMAND%" "%YII_PATH%yii"  user/create admin@email.com admin 123456  admin

@endlocal

