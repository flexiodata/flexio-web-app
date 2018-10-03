

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;



--
-- Table structure for table tbl_object
--

DROP TABLE IF EXISTS tbl_object;
CREATE TABLE tbl_object (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  eid_type char(3) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table tbl_association
--

DROP TABLE IF EXISTS tbl_association;
CREATE TABLE tbl_association (
  id int UNSIGNED NOT NULL auto_increment,
  source_eid char(12) NOT NULL default '',
  target_eid char(12) NOT NULL default '',
  association_type char(3) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (source_eid,association_type,target_eid),
  UNIQUE KEY (target_eid,association_type,source_eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table tbl_user
--

DROP TABLE IF EXISTS tbl_user;
CREATE TABLE tbl_user (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL,
  eid_status char(1) NOT NULL default '',
  username char(80) NOT NULL default '',
  email varchar(255) default '',
  description text default '',
  full_name text default '',
  first_name text default '',
  last_name text default '',
  phone text default '',
  location_city text default '',
  location_state text default '',
  location_country text default '',
  company_name text default '',
  company_url text default '',
  locale_language char(30) NOT NULL default 'en_US',
  locale_decimal char(1) NOT NULL default '.',
  locale_thousands char(1) NOT NULL default ',',
  locale_dateformat char(30) NOT NULL default 'm/d/Y',
  timezone char(30) NOT NULL default 'UTC',
  password char(80) NOT NULL default '',
  verify_code char(40) NOT NULL default '',
  role char(1) NOT NULL default '',
  config text default '',
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid),
  UNIQUE KEY (username),
  UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_user_owned_by ON tbl_user (owned_by);
CREATE INDEX idx_user_created ON tbl_user (created);



--
-- Table structure for table tbl_token
--

DROP TABLE IF EXISTS tbl_token;
CREATE TABLE tbl_token (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL,
  eid_status char(1) NOT NULL default '',
  access_code varchar(255) NOT NULL default '',
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid),
  UNIQUE (access_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_token_owned_by ON tbl_token (owned_by);
CREATE INDEX idx_token_created ON tbl_token (created);



--
-- Table structure for table tbl_acl
--

DROP TABLE IF EXISTS tbl_acl;
CREATE TABLE tbl_acl (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL,
  eid_status char(1) NOT NULL default '',
  object_eid char(12) NOT NULL default '',
  access_type char(3) NOT NULL default '',
  access_code varchar(255) NOT NULL default '',
  actions text default '',
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_acl_object_eid ON tbl_acl (object_eid);
CREATE INDEX idx_acl_access_code ON tbl_acl (access_code);
CREATE INDEX idx_acl_owned_by ON tbl_acl (owned_by);
CREATE INDEX idx_acl_created ON tbl_acl (created);



--
-- Table structure for table tbl_pipe
--

-- deploy_mode values:
--    B - build
--    R - run

-- deploy_schedule, deploy_api, deploy_ui values:
--    A - active
--    I - inactive

DROP TABLE IF EXISTS tbl_pipe;
CREATE TABLE tbl_pipe (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  eid_status char(1) NOT NULL default '',
  alias text default '',
  name text default '',
  description text default '',
  task text default '',
  schedule text default '',
  ui text default '',
  deploy_mode char(1) NOT NULL default 'B',
  deploy_schedule char(1) NOT NULL default 'I',
  deploy_api char(1) NOT NULL default 'I',
  deploy_ui char(1) NOT NULL default 'I',
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_pipe_alias ON tbl_pipe (alias);
CREATE INDEX idx_pipe_deploy_mode ON tbl_pipe (deploy_mode);
CREATE INDEX idx_pipe_deploy_schedule ON tbl_pipe (deploy_schedule);
CREATE INDEX idx_pipe_deploy_api ON tbl_pipe (deploy_api);
CREATE INDEX idx_pipe_deploy_ui ON tbl_pipe (deploy_ui);
CREATE INDEX idx_pipe_owned_by ON tbl_pipe (owned_by);
CREATE INDEX idx_pipe_created ON tbl_pipe (created);



--
-- Table structure for table tbl_connection
--

DROP TABLE IF EXISTS tbl_connection;
CREATE TABLE tbl_connection (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  eid_status char(1) NOT NULL default '',
  alias text default '',
  name text default '',
  description text default '',
  connection_type char(40) NOT NULL default '',
  connection_status char(1) NOT NULL default 'U',
  connection_info text default '',
  expires timestamp NULL default NULL,
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_connection_alias ON tbl_connection (alias);
CREATE INDEX idx_connection_owned_by ON tbl_connection (owned_by);
CREATE INDEX idx_connection_created ON tbl_connection (created);



--
-- Table structure for table tbl_process
--

DROP TABLE IF EXISTS tbl_process;
CREATE TABLE tbl_process (
  id int UNSIGNED NOT NULL auto_increment,
  eid varchar(12) NOT NULL default '',
  eid_status char(1) NOT NULL default '',
  parent_eid varchar(12) NOT NULL default '',
  process_mode varchar(1) NOT NULL default '',
  process_hash varchar(40) NOT NULL default '',
  impl_revision varchar(40) NOT NULL default '',
  task text default NULL,
  input text default NULL,
  output text default NULL,
  started_by varchar(12) NOT NULL default '',
  started timestamp NULL default NULL,
  finished timestamp NULL default NULL,
  process_info  text default NULL,
  process_status varchar(1) NOT NULL default '',
  cache_used varchar(1) NOT NULL default '',
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_process_eid ON tbl_process (eid);
CREATE INDEX idx_process_parent_eid ON tbl_process (parent_eid);
CREATE INDEX idx_process_process_hash ON tbl_process (process_hash);
CREATE INDEX idx_process_owned_by ON tbl_process (owned_by);
CREATE INDEX idx_process_created ON tbl_process (created);



--
-- Table structure for table tbl_processlog
--

DROP TABLE IF EXISTS tbl_processlog;
CREATE TABLE tbl_processlog (
  id int UNSIGNED NOT NULL auto_increment,
  eid varchar(12) NOT NULL default '',
  eid_status char(1) NOT NULL default '',
  process_eid varchar(12) NOT NULL default '',
  task_op text default '',
  task_version int NOT NULL default 0,
  task text default NULL,
  input text default NULL,
  output text default NULL,
  started timestamp NULL default NULL,
  finished timestamp NULL default NULL,
  log_type varchar(1) NOT NULL default '',
  message text default '',
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_processlog_eid ON tbl_processlog (eid);
CREATE INDEX idx_processlog_process_eid ON tbl_processlog (process_eid);
CREATE INDEX idx_processlog_owned_by ON tbl_processlog (owned_by);
CREATE INDEX idx_processlog_created ON tbl_processlog (created);



--
-- Table structure for table tbl_stream
--

DROP TABLE IF EXISTS tbl_stream;
CREATE TABLE tbl_stream (
  id int UNSIGNED NOT NULL auto_increment,
  eid varchar(12) NOT NULL default '',
  eid_status char(1) NOT NULL default '',
  parent_eid varchar(12) NOT NULL default '',
  stream_type varchar(3) NOT NULL default '',
  name text default '',
  path text default '',
  size numeric(12,0) default 0,
  hash varchar(40) NOT NULL default '',
  mime_type text default '',
  structure text default '',
  file_created timestamp NULL default NULL,
  file_modified timestamp NULL default NULL,
  connection_eid varchar(12) NOT NULL default '',
  expires timestamp NULL default NULL,
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_stream_parent_eid ON tbl_stream (parent_eid);
CREATE INDEX idx_stream_connection_eid ON tbl_stream (connection_eid);
CREATE INDEX idx_stream_owned_by ON tbl_stream (owned_by);
CREATE INDEX idx_stream_created ON tbl_stream (created);



--
-- Table structure for table tbl_comment
--

DROP TABLE IF EXISTS tbl_comment;
CREATE TABLE tbl_comment (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  eid_status char(1) NOT NULL default '',
  comment text default '',
  owned_by char(12) NOT NULL default '',
  created_by char(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_comment_owned_by ON tbl_comment (owned_by);
CREATE INDEX idx_comment_created ON tbl_comment (created);



--
-- Table structure for table tbl_action
--

DROP TABLE IF EXISTS tbl_action;
CREATE TABLE tbl_action (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',                     -- the eid of the action
  eid_status char(1) NOT NULL default '',               -- the eid status of the action
  action_type char(40) NOT NULL default '',             -- logical name for the action (e.g. "create", helps map mutiple routes to similar action, such as creating an object)
  request_ip char(40) NOT NULL default '',              -- ip address of the request if available
  request_user_agent text default '',                   -- user agent for the request
  request_type char(12) NOT NULL default '',            -- request type (e.g. "HTTP")
  request_method char(12) NOT NULL default '',          -- specific method for the request type (e.g. "PUT", "POST", "DELETE", etc)
  request_route text default '',                        -- the specific route used to request the action (e.g. the url path of the request)
  request_created_by char(12) NOT NULL default '',      -- the user making the request
  request_created timestamp NULL default NULL,          -- timestamp when the request was created
  request_access_code varchar(255) NOT NULL default '', -- access code used to authenticate the request if it exists
  request_params json,                                  -- specific parameters included with the request; note: not all params may be saved (e.g. passwords)
  target_eid char(12) NOT NULL default '',              -- object eid being created, changed, deleted, etc
  target_eid_type char(3) NOT NULL default '',          -- object eid type being created
  target_owned_by char(12) NOT NULL default '',         -- owner eid of the object being created, chagned, deleted, etc
  response_type char(12) NOT NULL default '',           -- response type (e.g. "HTTP")
  response_code char(12) NOT NULL default '',           -- specific code for the response type (e.g. "200", "404", etc)
  response_params json,                                 -- subset of info returned to the user (e.g. error code and message or basic info about object)
  response_created timestamp NULL default NULL,         -- timestamp when the response was created
  owned_by char(12) NOT NULL default '',                -- same as request_created_by
  created_by char(12) NOT NULL default '',              -- same as request_created_by
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_action_action_type ON tbl_action (action_type);
CREATE INDEX idx_action_request_created_by ON tbl_action (request_created_by);
CREATE INDEX idx_action_target_eid ON tbl_action (target_eid);
CREATE INDEX idx_action_target_eid_type ON tbl_action (target_eid_type);
CREATE INDEX idx_action_target_owned_by ON tbl_action (target_owned_by);
CREATE INDEX idx_action_owned_by ON tbl_action (owned_by);
CREATE INDEX idx_action_created ON tbl_action (created);



--
-- Table structure for table tbl_registry
--

DROP TABLE IF EXISTS tbl_registry;
CREATE TABLE tbl_registry (
  id int UNSIGNED NOT NULL auto_increment,
  object_eid char(12) NOT NULL default '',
  name char(80) NOT NULL default '',
  mime_type char(80) NOT NULL default '',
  value_type char(1) default '',
  value longtext default NULL,
  expires timestamp NULL default NULL,
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (object_eid, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table tbl_system
--

DROP TABLE IF EXISTS tbl_system;
CREATE TABLE tbl_system (
  id int UNSIGNED NOT NULL auto_increment,
  name char(80) NOT NULL default '',
  value longtext default NULL,
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



SET character_set_client = @saved_cs_client;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

