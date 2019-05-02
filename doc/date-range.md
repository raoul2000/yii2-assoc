# Date Range

a Date Range is a partial amount of time. It is defined by one ore two date boundaries representing the limits of the range. 3 types of date range exist :
- *closed* : defined by a `start` and `end` date
- *right opened* : defined by an `end` date only
- *left opened* : defined by an `start` date only


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

### Item Selection

Given a simple date interval, the figure below show selected items (orders) depending on their `valid_date_start` and `valid_date_end` values.

```
date range : ---------|**********|------------- :
order1     : --|**|---------------------------- : NOT selected
order1     : --|******************************* : selected
order2     : --------------------------|**|---- : NOT selected
order3     : ----|**********|------------------ : selected
order4     : -----------|*******|-------------- : selected
order5     : ---------------|*********|-------- : selected
order5     : ********************************** : selected
```

In terms of expression :

```
valid_start_date <= endRange or valid_start_date IS NULL
AND
valid_end_date >= startRange or valid_end_date IS NULL
```
