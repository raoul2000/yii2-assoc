# Generate Fixtures 

This feature uses *yii2-faker* extension which is a wrapper for the *Faker* Library.

Generate Fixtures for Contact : 
```
php yii fixture/generate "Contact"
```
By default fixtures are generated in folder `./tests/unit/fixtures/data`.

Load a Fixture For Contact : 
```
php yii fixture/load Contact
```