# Interval

## Domain Value

```php
$domain = new ValueDomain();
$domain->isValidValue($value); // boolean
$domain->isNullValue($value); // boolean

$vdo = new ValueDomainOrder($domain);
$vdo->compare($value1, $value2);     // 0 : $value1 == $value2
                                        // <0 : $value1 < $value2 
                                        // >0 : $value1 > $value2 

// next  methods are shortcuts to the 'compare' method
$vdo->equalValues($value1, $value2); // boolean
$vdo->greaterThan($value1, $value2); // boolean
$vdo->lowerThan($value1, $value2);   // boolean
$vdo->max($value1, $value2);         // $value1 or $value2
$vdo->min($value1, $value2);         // $value1 or $value2
```

## Creating Interval

```php
// create a closed interval [$value1, $value2]
$int1 = IntervalFactory::create($value1, $value2);  // returns Interval

// create right opened interval [$value1
$int1 = IntervalFactory::create($value1);   // returns Interval

// create right opened interval $value1]
$int2 = IntervalFactory::create(null, $value1); // return Interval
```

## Interval methods

```php
$int1->contains('2019-01-23'); // returns Boolean
```

```php
$int1->overlaps($int2); // returns Boolean
```

## Interval Algebra


```php
$int1 = DateInterval::create('2019-01-02', '2019-02-12');
$int2 = DateInterval::create('2019-01-02', '2019-02-12');

$intervalSet = DateInterval::add($int1, $int2); // IntervalSet

var_dump($intervalSet);
// [ ['2019'-01-02', '2019-02-12']]
$sum1->contains('2019-01-10'); // true
$sum1->isFragmented(); // false
```
