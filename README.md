SymfonySchemaBundle
==============

## Installation

### Get the bundle

Add the following lines in your composer.json:

```json
{
    "require": {
        "iliev/symfony-schema": "dev-master",
    }
}
```

### Initialize the bundle

To start using the bundle, register the bundle in your application's kernel class:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Iliev\SymfonySchemaBundle\IlievSymfonySchemaBundle(),
    );
)
```

## Configuration Reference

Below is the full default configuration for the bundle:

```yaml
# app/config/config.yml
iliev_symfony_schema:
    database:
        # A database connection name that is used to execute the SQL queries
        default_connection: default

        # doctrine or propel
        orm: doctrine

        # A database table name used to track the applied SQL files
        table_name: model_version

    # Path to the directory containing the sql files
    working_path: "%kernel.root_dir%/../schema/sql/updates"
```

## Usage

Create your update scripts in the *working_path* of this bundle.
It is recommended to follow a naming convention to ensure incremental updates.

Example:
```
$ ls schema/sql/updates/ -l
total 24
-rw-r--r-- 1 user user 181 Jun  9 17:19 20130606-1.sql
-rw-r--r-- 1 user user 135 Jun  9 17:19 20130608-1.sql
-rw-r--r-- 1 user user 270 Jun  9 17:19 20130608-2.sql
-rw-r--r-- 1 user user 537 Jun  9 17:19 20130609-1.sql
-rw-r--r-- 1 user user 360 Jun  9 17:20 20130609-2.sql
-rw-r--r-- 1 user user 184 Jun  9 17:20 20130609-3.sql

Each file should contain a description block

### Multi line example:

```sql
# schema/sql/updates/20130609-1.sql

#
# <description>
# Multi-line
# description
# of the update
# </description>
#

ALTER TABLE `accounts` ADD `username_normalized` VARCHAR(255) NOT NULL AFTER `username_canonical`;
```

### Single line example:

```sql
# schema/sql/updates/20130609-2.sql

#
# --> Single line description
#

ALTER TABLE `accounts` ADD `username_normalized` VARCHAR(255) NOT NULL AFTER `username_canonical`;
```
