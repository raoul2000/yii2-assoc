
# Prepare DB

```bash
# apply DB migration for RBAC and yii2-usuario
yii migrate --migrationPath=@yii/rbac/migrations --interactive=0

yii migrate --migrationPath=@bupy7/activerecord/history/migrations --interactive=0
```

mysql -h <hostname> -u <username> --password=<password> -D <database> -e 'source <path-to-sql-file>'

# Inject Data (dev)

```bash
# create admin user
yii user/create admin@email.com admin 123456  admin

# Generate Fake Contact + Address and load them
yii fixture/generate contact --count=5 --interactive=0
yii fixture/generate address --count=5 --interactive=0
yii fixture/load Contact --interactive=0
```

```bash
yii fixture/generate "*" --count=10 --interactive=0
yii fixture/load
```
