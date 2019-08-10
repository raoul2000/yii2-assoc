> All commands must be launched from the `./src` folder


# Prepare DB

```bash
# apply DB migration for RBAC and yii2-usuario
yii migrate --migrationPath=@yii/rbac/migrations --interactive=0

yii migrate --migrationPath=@bupy7/activerecord/history/migrations --interactive=0
```

mysql -h <hostname> -u <username> --password=<password> -D <database> -e 'source <path-to-sql-file>'

# Test Data

> Before generating new fixture data, REMOVE ALL FILES FROM @tests/unit/fixtures/data.

```bash
# create admin user
yii user/create admin@email.com admin 123456  admin

# Generate Fake Contact + Address and load them
yii fixture/generate contact --count=5 --interactive=0 
yii fixture/generate address --count=5 --interactive=0 
yii fixture/generate bank_account --count=5 --interactive=0 

# load fixture for Contact and its dependent models
yii fixture/load Contact --interactive=0
```

```bash
# Generate and load all (10 items)
yii fixture/generate "*" --count=10 --interactive=0 
yii fixture/load "*" 
```

To only load one fixture (ex: Product) :
```bash
yii fixture/load Product  --interactive=0 
```

# Prepare FS

A Data folder is required by several features of the system so check that aliases configured are refering to existing folders :

in `./config/web.php` 
```
'@data' => '@app/../data',
'@template' => '@app/../data/templates'
'@imports' => '@app/../data/imports'
```
