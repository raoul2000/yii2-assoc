## Link User to Contact

A site user (i.e. registered and able to login) should be related to a row in the *Contact* table. By doing so, a logged-in user would be considered as having Contact info which would provide direct access to all info related to this Contact :
- the contact info itself
- the optional address

### Default Bank Account

A site user linked to a Contact is also linked to a default bank account.
- the Contact has only one bank account : it is considered as **default**
- the Contact has more than one bank account
    - Automatic option : the bank account having the lower ID value is the **default** account
    - manual option : the site user must choose the **default** account
- the contact has no bank account : this is not a normal situation as a bank account is always created just after the Contact itself is created.

## Validate Email

- check [email validator](https://github.com/zytzagoo/smtp-validate-email) against SMTP

## Refactor Time Period

- evaluate [league\Period](https://period.thephpleague.com/) to handle time periods
This library required PHP 7.3

## Export as CSV file

- use [League\Csv](https://csv.thephpleague.com/9.0/) - because [yii2-csv-importer](https://github.com/ruskid/yii2-csv-importer) may not be able to handle big CSV files (based on [this issue](https://github.com/ruskid/yii2-csv-importer/issues/10))

## Tasks

implement a task system where a user can create a task linked to a record. It contains a description and a status (todo, done). In a first stage, the task is assigned to the user who created it. Later, we may extends this task system and manage assignments etc ..

## Off Canvas Menu

- [Off canvas menu on W3School](https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_sidenav_push)

## Execute SQL script in migration

- goal : being able to execute an SQL script file in a migration. This could allow init of the DB for custom tables
- see [Yii2 Guide](https://www.yiiframework.com/doc/guide/2.0/en/db-migrations)
- see [Yii2 forum](https://forum.yiiframework.com/t/execute-sql-file-in-migration/47901)


## [DONE] Charts

- check [yii2-highcharts](https://github.com/miloschuman/yii2-highcharts) : If you are a non-profit company, or use our products for personal use, you may enjoy our software for free under a Creative Commons (CC) Attribution-NonCommercial licence
- or maybe [yii2-chartjs-widget](https://github.com/2amigos/yii2-chartjs-widget) by [2amigos](https://2amigos.us/)


## [DONE] Add attachment to Transaction pack records

## [DONE] integrate with Yii2tech/config

see https://github.com/yii2tech/config for a complete config manager component

## [DONE] Test the `TimestampBheavior` for a *DATETIME* column type and if it works fine, replace all
  
One replacement solution is to create a custom TimestampBehavior class that inherits from the Yii2 one, and that set 
appropriates settings

## [DONE] Remove Validation Rules For `created_at` and `updated_at`
These properties are set by the TimestampBehavior, not the user ... 
