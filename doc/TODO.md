## Test the `TimestampBheavior` for a *DATETIME* column type and if it works fine, replace all
  
One replacement solution is to create a custom TimestampBehavior class that inherits from the Yii2 one, and that set 
appropriates settings

## Remove Validation Rules For `created_at` and `updated_at`
These properties are set by the TimestampBehavior, not the user ... 