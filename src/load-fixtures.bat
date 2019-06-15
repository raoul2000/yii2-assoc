@echo off

@setlocal

set YII_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe


"%PHP_COMMAND%" "%YII_PATH%yii" fixture/generate contact --count=20 --interactive=0
"%PHP_COMMAND%" "%YII_PATH%yii" fixture/generate address --count=20 --interactive=0
"%PHP_COMMAND%" "%YII_PATH%yii" fixture/generate bank_account --count=20 --interactive=0

"%PHP_COMMAND%" "%YII_PATH%yii" fixture/load "*" --interactive=0

"%PHP_COMMAND%" "%YII_PATH%yii" user/create  admin@email.com admin 123456 admin 

@endlocal

