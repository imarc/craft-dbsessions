# Craft DB Sessions Handler

For Craft 2.x. Sets up a database sesssion handler using Craft's 
existing DB and Caching libraries to ensure that only a single 
database connection is ever created.

This is not a true Craft plugin, but is best distributed as one.

## Installation

Install through composer (recommended):

    composer require imarc/craft-dbsessions

Alternatively place all files into a `craft/plugins/dbsessions` directory.

## Usage

1. Edit the general.php config file in your Craft CMS instance to use the `overridePhpSessionLocation` setting. More Info at: https://docs.craftcms.com/v2/config-settings.html#overridephpsessionlocation

1. Add the following at the very top of your `public/index.php` file:

    ```require __DIR__ . '/../craft/plugins/dbsessions/SessionHandler.php';
    \Imarc\CraftDatabaseSessions\SessionHandler::register();

Your sessions will now be stored in craft's database instead of on the filesystem.

The `dbsessions_sessions` database table used by this library *should* be created automatically. If there
are restrictive database permissions, you can use the following migration to create the table manually:

```(sql)
CREATE TABLE dbsessions_sessions (
    id char(128) NOT NULL PRIMARY KEY,
    expire int(11) NULL,	 
    value longblob NULL
);
```

## Why?

Sometimes, in an high-availability environment with multiple application servers, storing the sessions 
directly on the filesystem is not ideal as there is no consistency to which application server
an end-user is hitting. **Redis or Memcached is recommended over using the DB for sessions** as Craft can 
work with those out of the box, but DB is fine if those services aren't available for whatever reason.
