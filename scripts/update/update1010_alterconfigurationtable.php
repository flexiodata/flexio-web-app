<?php
/*!
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-27
 *
 * @package flexio
 * @subpackage Database_Update
 */


include_once __DIR__.'/../stub.php';


if ($argc != 5)
{
    echo '{ "success": false, "msg": "Usage: php update*.php <host> <username> <password> <database_name>" }';
    exit(0);
}


$params = array('host' => $argv[1],
                'port' => 5432,
                'username' => $argv[2],
                'password' => $argv[3],
                'dbname' => $argv[4]);

try
{
    $db = \Flexio\Base\Db::factory('PDO_POSTGRES', $params);
    $conn = $db->getConnection();
}
catch (\Exception $e)
{
    echo($e->getMessage());
    $db = null;
}

if (is_null($db))
{
    echo '{ "success": false, "msg": "Could not connect to database." }';
    exit(0);
}


try
{
    // STEP 1: create the new connection table
    $sql = <<<EOT
        drop table if exists _temp_tbl_connection;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        create table _temp_tbl_connection (
          id serial,
          eid varchar(12) not null default '',
          name text default '',
          description text default '',
          display_icon text default '',
          connection_type varchar(40) not null default '',
          host varchar(255) not null default '',
          port int not null,
          username varchar(80) not null default '',
          password varchar(80) not null default '',
          token text not null default '',
          token_expires timestamp null default null,
          database varchar(255) not null default '',
          connection_status varchar(1) not null default 'U',
          created timestamp null default null,
          updated timestamp null default null,
          primary key (id),
          unique (eid)
        );
EOT;
    $db->exec($sql);


    // STEP 2: migrate the values from the old connection table
    // to the new connection table; connection_status will update
    // with default value, which is what we want
    $sql = <<<EOT
        insert into _temp_tbl_connection
            (eid, name, description, display_icon, connection_type,
             host, port, username, password, token, token_expires, database,
             created, updated)
        select
            eid as eid,
            name as name,
            description as description,
            display_icon as display_icon,
                case
                    when server_type = 'postgres' then 'postgres.api'
                    else connection_type
                end as connection_type,
            server as host,
            server_port as port,
            server_username as username,
            server_password as password,
            token as token,
            token_expires as token_expires,
            location_group as database,
            created as created,
            updated as updated
        from
            tbl_connection;
EOT;
    $db->exec($sql);

    // STEP 3: drop the old table and add the new table
    $db->exec("alter table tbl_connection rename to _temp_tbl_connection_old");
    $db->exec("alter table _temp_tbl_connection rename to tbl_connection");
    $db->exec("drop table _temp_tbl_connection_old");
}
catch(\Exception $e)
{
    echo '{ "success": false, "msg":' . json_encode($e->getMessage()) . '}';
    exit(0);
}


// update the version number
$current_version = \Flexio\System\System::getUpdateVersionFromFilename(__FILE__);
\Flexio\System\System::getModel()->setDbVersionNumber($current_version);

echo '{ "success": true, "msg": "Operation completed successfully." }';
