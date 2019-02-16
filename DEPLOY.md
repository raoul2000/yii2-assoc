
# Build and deploy

## Configure FTP settings

- configure `ftp.master.properties` with FTP settings of the target server

```
ftp.host=ftp.example.com
ftp.port=21
ftp.username=your-user-name
ftp.password=your-password
ftp.dir=deploy_foldername
ftp.mode=binary
```

## Build Deploy All

```
> ./vendor/bin/phing build-src
> ./vendor/bin/phing build-vendor
> ./vendor/bin/phing deploy-all
```

## Build Partial

All build are copied to the folder `./build`.

- `build-src` : build source files
- `build-vendor` : build Composer dependencies for production (**no-dev**)
- `deploy-src` : deploy source files to server
- `deploy-vendor` : deploy Composer dependencies previoulsy build
