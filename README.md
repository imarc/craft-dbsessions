# Craft DB Sessions Handler

For Craft 2.x

## Installation & Usage

Install through composer (recommended) or manually place all files into a `craft/plugins/dbsessions` directory.

Finally add the following at the very top of your `public/index.php` file:

    require __DIR__ . '/../craft/plugins/dbsessions/SessionHandler.php';
    \Imarc\CraftDatabaseSessions\SessionHandler::register();

Your sessions will now be stored in craft's database instead of on the filesystem.

## Why?

Sometimes, in an high-availability environment with multiple application servers, storing the sessions 
directly on the filesystem is not ideal as there is no consistency to which application server
an end-user is hitting. **Redis or Memcached is recommended over using the DB for sessions** as Craft can 
work with those out of the box, but DB is fine if those services aren't available for whatever reason.
