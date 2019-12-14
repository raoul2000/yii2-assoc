- create DB schema
```sql
CREATE SCHEMA `yii2_assoc` DEFAULT CHARACTER SET utf8 ;
```
- run script `create-db.sql`
- go to http://localhost/dev/yii2-assoc/src/web/index.php?r=admin/install
    - initialize RBAC
    - create user `Admin`
- manually create user GymV
    - contact Id = 5
    - bank account id = 1
- via PhpMyAdmin import
    - **category**, import `db-dataset/set1/category.csv`
    - **product**, import `db-dataset/set1/product.csv`
    - **config**, import `db-dataset/set1/config.csv`

A partir de là, les imports spécifiques peuvent être fait, ainsi que les saisies manuelles.




