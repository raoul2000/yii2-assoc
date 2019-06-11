# Build and deploy

the project tools is based on **gulp tasks**.

## Tasks for building

The main task for building the project (the default task) builds *source*, *vendor*, and produces a folder ready to deploy :
```
gulp
```

## Task for deploying 

### FTP

**Currently only FTP deployment is supported**

Before dploying you must configure the target server information into the file `./gulp-task/sftp-prod.json`

```
gulp deployFtp
```


- `clean` : completelty removes the build folder
- `buildSource` : copy sources files from `./src` to the build folder `./build/src`. During this task, not all files from the source folder are copied 
  - 


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
