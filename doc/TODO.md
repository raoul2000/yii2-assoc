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

## Refactor Time Period

- evaluate [league\Period](https://period.thephpleague.com/) to handle time periods

## Export as CSV file

- use [League\Csv](https://csv.thephpleague.com/9.0/) - because [yii2-csv-importer](https://github.com/ruskid/yii2-csv-importer) may not be able to handle big CSV files (based on [this issue](https://github.com/ruskid/yii2-csv-importer/issues/10))

## [DONE] Add attachment to Transaction pack records

## [DONE] integrate with Yii2tech/config

see https://github.com/yii2tech/config for a complete config manager component

## [DONE] Test the `TimestampBheavior` for a *DATETIME* column type and if it works fine, replace all
  
One replacement solution is to create a custom TimestampBehavior class that inherits from the Yii2 one, and that set 
appropriates settings

## [DONE] Remove Validation Rules For `created_at` and `updated_at`
These properties are set by the TimestampBehavior, not the user ... 
