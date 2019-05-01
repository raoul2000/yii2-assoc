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

## Validity Date Range

The *validity date range* is the time interval during which a product is owned by a contact. In its simplest form, it consists in a *start date* and an *end date* :
- [start_date, end_date] : where `start date <= end date`. Defines a limited range
- [null, end_date] : defines a left-opened range
- [start_date, null] : defines a right-opened range
- [null, null] : defines an opened range


At a given date, a product is owned by a contact if this date is included in the *Validity Date Range* of the product.

Example :
- Product : "class registration"
- validity Range : [2019-01-01, 2019-12-31]
- current date : 2019-05-01
- ownership : **YES**

### Date Range Composition

A *Validity Date Range* can be composed of one or more date ranges. In this case ownership is granted for a given date, if this date is included in one of the date range composing the *Validity Date Range*.

Example :
- product : ""
- validity date range : [ [2019-01-01, 2019-01-31] , [2019-03-01, 2019-03-31] ]
- current date : 2019-05-01
- ownership : **NO**


## integrate with Yii2tech/config

see https://github.com/yii2tech/config for a complete config manager component

## [DONE] Test the `TimestampBheavior` for a *DATETIME* column type and if it works fine, replace all
  
One replacement solution is to create a custom TimestampBehavior class that inherits from the Yii2 one, and that set 
appropriates settings

## [DONE] Remove Validation Rules For `created_at` and `updated_at`
These properties are set by the TimestampBehavior, not the user ... 
