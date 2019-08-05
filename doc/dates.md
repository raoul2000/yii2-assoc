# Date management

## Persistence

DATE attributes are saved in DB with the following format : 

```
yyyy-mm-dd
```

In the system, DATE are represented in another format and there must be a conversion process to apply to DATE attributes :
- when loaded from the DB :  convert `yyyy-mm-dd` to the current app format (e.g. `dd/mm/yyyy`) AFTER reading values from db
- when saved to DB :  convert App format to DB format BEFORE saving 

This task is handled by the `app\components\behaviors\DateConverterBehavior` behavior, attached to any model that needs the DATE conversion feature (i.e. with a DATE attribute). This behavior automatically applies conversion when the record is loaded (from Db to App format) and before it is saved (from App to Db).

Date conversion is actually performed by the helper class `\app\components\helpers\DateHelper` through the methods :

- `DateHelper::toDateAppFormat`
- `DateHelper::toDateDBFormat`
- 
## Validation

All date fields entered by user are validated by the `yii\validators\DateValidator` class and are expected to have the format configured in the `./config/params.php` file.

```php
'dateValidatorFormat' => 'dd/MM/yyyy'
```

Each model having DATE attributes subject to validation (i.e. user input) are validating those attributes adding the following rules :

```php
[['birthday', 'date_1'], 'date', 'format' => Yii::$app->params['dateValidatorFormat']]
```

### Date Range Validator

In order to handle date conversion correctly during the validation process of a Date Range, a custom validator has been implemented : `\app\components\validators\DateRangeValidator`. 

> this validator only applies to attributes `valid_date_start` and `valid_date_end`, so make sure that the model does inclides thoses attributes

## Rendering

Rendering date can be done using the `app\components\Formatter` which extends `yii\i18n\Formatter` with the method `asAppDate`.
This method expects a date in App format, convert it to DB format and call the methode `asDate` in the parent class.

In a GridView, a DATE attribute can be rendered this way : 
```php
'birthday:appDate',
```


