# polling
minimalist online polling and surveys

It is meant to be trivially deployable in PHP and vanilla.js, 
using the filesystem as a rough NoSQL database.

## setup

Copy the contents of src/ into a folder on a webhost configured to run php files

Create a data directory (elsewhere on the file system, outside of the public html/php hierarchy)
with two subdirectories: meta, and results. Make sure this directory is writable by
the webserver.

Edit config.json: 

```
{
    "APPNAME": "display-name",
    "DATAROOT": "/folder/to/store/files",
    "APPROOT": "servername/path"
}

```

APPNAME is used at the top of pages of the app, DATAROOT is the directory you just created,
APPROOT is the URL for the deployed app (omitting the https://)
