# Build and deploy

the project tools is based on **gulp tasks**. BEfore being able to use Gulp Tasks, you must install it. From the project's main folder, run :

```
npm install
```

## Tasks for building

The main task for building the project (the default task) builds *source*, *vendor*, and produces a folder ready to deploy :

```
gulp
```

Other Gulp tasks are also available :
- `gulp buildSource` : copy in the `./build/src` folder all the source files including the `index.php` file, ready for Production. Note that an empty `vendor` folder will be created.
- `gulp buildVendor` : build composer's dependencies based on the file `composer.lock`, into the folder `./build/composer/vendor`
- `gulp mergeSourceVendor` : merges the `./build/composer` folder with the source folder.


## Task for deploying 

**Currently only FTP deployment is supported**

Before dploying you must configure the target server information into the file `./gulp-task/ftp.json`. 

```json
{
    "type" : "ftp",
    "host" : "hostnam.com",
    "user" : "username",
    "password" : "*******",
    "port" : 22,
    "remote_path" : "destinationFolder"
}
```

When done, you can start the deployement task. It will deploy all files from the local folder `./build/src` into the configured target folder.

```
gulp deployFtp
```



### Other Tasks

- `clean` : completelty removes the build folder

