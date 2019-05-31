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
## Function

### Overlap

```
range 1 : -------|******|----------------------
range 2 : ------------|******|-----------------
```


## Algebra

### Addition

Adding 2 date ranges can produce :
- a new date range : when range overlap
- a date range set : when date don't overlap

The example below illustrate the sum of 2 simple date ranges
Date Range Overlap :
```
range 1 : -------|******|----------------------
range 2 : ------------|******|-----------------
SUM     : -------|***********|-----------------
```

Date Range With no overlap :
```
range 1 : -----|******|----------------------
range 2 : -------------------|******|--------
SUM     : -----|******|------|******|--------
```

In the second case, the addition of 2 date Ranges produces a set of 2 date Ranges : this is called a **composite** date range.
Composite Date range can also be subject to addition.

```
range 1 : -------|******|-----------|*****|------
range 1 : --|**|-----------|*****|-----|******|--
SUM     : --|**|-|******|--|*****|--|*********|--
```
The result is a composite date range made of 4 simple date ranges. In the example below, the result is a simple date range.
```
range 1 : -------|******|--------|*****|------
range 1 : ----|*****|--|*****************|----
SUM     : ----|**************************|----
```



### Soustraction

No overlap :
```
range 1 : -----------------|******|--------
range 2 : ----|*****|----------------------
R2 - R1 : ----|*****|----------------------
```
overlap left:
```
range 1 : ----|******|---------------------
range 2 : --------|*********|--------------
R2 - R1 : -----------|******|----------------------
```
overlap right:
```
range 1 : ---------------|******|----------
range 2 : --------|*********|--------------
R2 - R1 : --------|******|-----------------
```
included:
```
range 1 : -----------|***|----------
range 2 : ------|****************|--
R2 - R1 : ------|****|---|*******|--
```
contains:
```
range 1 : ----|*******************|----
range 2 : ------|****************|-----
R2 - R1 : -----------------------------
```



