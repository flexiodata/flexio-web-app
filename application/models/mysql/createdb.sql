

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
-- note: in the postgres implementation, the index on ename is
-- a unique, conditional index; here, we use a simple index and
-- uniqueness is enforced by the model implementation
--

DROP TABLE IF EXISTS tbl_object;
CREATE TABLE tbl_object (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  eid_type char(3) NOT NULL default '',
  eid_status char(1) NOT NULL default '',
  ename varchar(40) NOT NULL default '',
  rights text default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid),
  UNIQUE KEY (ename)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_ename ON tbl_object (ename);



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
    user_name char(80) NOT NULL default '',
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
    config text default '',
    created timestamp NULL default NULL,
    updated timestamp NULL default NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (eid),
    UNIQUE KEY (user_name),
    UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table tbl_token
--

DROP TABLE IF EXISTS tbl_token;
CREATE TABLE tbl_token (
    id int UNSIGNED NOT NULL auto_increment,
    eid char(12) NOT NULL,
    user_eid char(12) NOT NULL,
    access_code varchar(255) NOT NULL default '',
    secret_code varchar(255) NOT NULL default '',
    created timestamp NULL default NULL,
    updated timestamp NULL default NULL,
    PRIMARY KEY (id),
    UNIQUE (eid),
    UNIQUE (access_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_token_user_eid ON tbl_token (user_eid);



--
-- Table structure for table tbl_project
--

DROP TABLE IF EXISTS tbl_project;
CREATE TABLE tbl_project (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  name text default '',
  description text default '',
  display_icon text default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table tbl_pipe
--

-- schedule_status values
--    A - active
--    I - inactive

DROP TABLE IF EXISTS tbl_pipe;
CREATE TABLE tbl_pipe (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  name text default '',
  description text default '',
  display_icon text default '',
  task text default '',
  input text default '',
  output text default '',
  schedule text default '',
  schedule_status char(1) NOT NULL default 'I',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_pipe_schedule_status ON tbl_pipe (schedule_status);



--
-- Table structure for table tbl_connection
--

DROP TABLE IF EXISTS tbl_connection;
CREATE TABLE tbl_connection (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  name text default '',
  description text default '',
  display_icon text default '',
  connection_type char(40) NOT NULL default '',
  host text NOT NULL default '',
  port int NOT NULL,
  username text NOT NULL default '',
  password text NOT NULL default '',
  token text NOT NULL default '',
  refresh_token text NOT NULL default '',
  token_expires timestamp NULL default NULL,
  database text NOT NULL default '',
  connection_status char(1) NOT NULL default 'U',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table tbl_process
--

DROP TABLE IF EXISTS tbl_process;
CREATE TABLE tbl_process (
  id int UNSIGNED NOT NULL auto_increment,
  eid varchar(12) NOT NULL default '',
  parent_eid varchar(12) NOT NULL default '',
  process_eid varchar(12) NOT NULL default '',
  process_mode varchar(1) NOT NULL default '',
  process_hash varchar(40) NOT NULL default '',
  impl_revision varchar(40) NOT NULL default '',
  task_type text default '',
  task_version int NOT NULL default 0,
  task text default NULL,
  input text default NULL,
  output text default NULL,
  input_params text default NULL,
  output_params text default NULL,
  started_by varchar(12) NOT NULL default '',
  started timestamp NULL default NULL,
  finished timestamp NULL default NULL,
  process_info  text default NULL,
  process_status varchar(1) NOT NULL default '',
  cache_used varchar(1) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_process_eid ON tbl_process (eid);
CREATE INDEX idx_process_parent_eid ON tbl_process (parent_eid);
CREATE INDEX idx_process_process_eid ON tbl_process (process_eid);
CREATE INDEX idx_process_process_hash ON tbl_process (process_hash);
CREATE INDEX idx_process_task_type ON tbl_process (task_type);



--
-- Table structure for table tbl_stream
--

DROP TABLE IF EXISTS tbl_stream;
CREATE TABLE tbl_stream (
  id int UNSIGNED NOT NULL auto_increment,
  eid varchar(12) NOT NULL default '',
  name text default '',
  path text default '',
  size numeric(12,0) default 0,
  hash varchar(40) NOT NULL default '',
  mime_type text default '',
  structure text default '',
  file_created timestamp NULL default NULL,
  file_modified timestamp NULL default NULL,
  connection_eid varchar(12) NOT NULL default '',
  cache_path text default '',
  cache_connection_eid varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_stream_connection_eid ON tbl_stream (connection_eid);



--
-- Table structure for table tbl_comment
--

DROP TABLE IF EXISTS tbl_comment;
CREATE TABLE tbl_comment (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  comment text default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table tbl_action
--

DROP TABLE IF EXISTS tbl_action;
CREATE TABLE tbl_action (
  id int UNSIGNED NOT NULL auto_increment,
  user_eid char(12) NOT NULL default '',
  request_method text default '',
  url_params text default '',
  query_params text default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id)
);

CREATE INDEX idx_action_user_eid ON tbl_action (user_eid);



--
-- Table structure for table tbl_notification
--

DROP TABLE IF EXISTS tbl_notification;
CREATE TABLE tbl_notification (
  id int UNSIGNED NOT NULL auto_increment,
  eid char(12) NOT NULL default '',
  user_eid char(12) NOT NULL default '',
  source_user_eid char(12) NOT NULL default '',
  object_eid char(12) NOT NULL default '',
  subject_eid char(12) NOT NULL default '',
  notice_type char(20) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (eid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX idx_notification_user_eid ON tbl_notification (user_eid);
CREATE INDEX idx_notification_source_user_eid ON tbl_notification (source_user_eid);
CREATE INDEX idx_notification_object_eid ON tbl_notification (object_eid);
CREATE INDEX idx_notification_subject_eid ON tbl_notification (subject_eid);
CREATE INDEX idx_notification_notice_type ON tbl_notification (notice_type);
CREATE INDEX idx_notification_created ON tbl_notification (created);



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
