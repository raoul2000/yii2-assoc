# Tags

In the system, the user can add or remove tags to an entity. A tag is a string that can be linked to a record, used as a search criteria to find linked records, updated or unlinked from a record.

## Add Tags to a Model

For a complete documentation please refer to https://github.com/creocoder/yii2-taggable

Before being able to add Tag support to an existing model, you must create the pivot table that will holde the liks between the `tag` table of the subject table. In the following example, we will use the table `transaction` as subject.

- create the **pivot** table
```sql
CREATE TABLE IF NOT EXISTS `tag_has_transaction` (
  `tag_id` INT(10) UNSIGNED NOT NULL,
  `transaction_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`tag_id`, `transaction_id`),
  INDEX `fk_tag_has_transaction_transaction1_idx` (`transaction_id` ASC),
  INDEX `fk_tag_has_transaction_tag_idx` (`tag_id` ASC),
  CONSTRAINT `fk_tag_has_transaction_tag`
    FOREIGN KEY (`tag_id`)
    REFERENCES `tag` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_has_transaction_transaction1`
    FOREIGN KEY (`transaction_id`)
    REFERENCES `transaction` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;
```

- Add the `TaggableBehavior` behavior to the model definition class :
```php
public function behaviors()
{
    return [
        'taggable' => [
            'class' => \app\components\behaviors\TaggableBehavior::className(),
        ],
    ];
}
```
- Add a validation rule for the **tagValues** attribute. This attribute is completely handled by the `TaggableBehavior` behavior and as no existence aa a real model's attribute.
```php
public function rules()
{
    return [
        // tags
        ['tagValues', 'safe'],
        // etc ...
    ];
}
```
- Add the **Relation Declaration** to the model. Note that the name of the pivot table (*tag_has_transaction*) and its subject refering key (*transaction_id*) are hard coded in the relation definition.
```php
public function getTags()
{
    return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
        ->viaTable('{{%tag_has_transaction}}', ['transaction_id' => 'id']);
}
```
- Add the behavior `TaggableQueryBehavior` to the Query Class. If you don't have a query class for your subject record, create one.
```php
public function behaviors()
{
    return [
        \app\components\behaviors\TaggableQueryBehavior::className(),
    ];
}
```
- make sure you subject model is using the related query class to perform SQL requests

```php
public static function find()
{
    return new TransactionQuery(get_called_class());
}
```

For actual implementaiton example, please check the `Transaction` model.
