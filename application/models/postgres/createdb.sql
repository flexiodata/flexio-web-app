

--
-- Table structure for table tbl_object
-- note: require any specified enames to be unique, but allow blanks
--

DROP TABLE IF EXISTS tbl_object;
CREATE TABLE tbl_object (
  id serial,
  eid varchar(12) NOT NULL default '',
  eid_type varchar(3) NOT NULL default '',
  eid_status varchar(1) NOT NULL default '',
  ename varchar(80) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE UNIQUE INDEX idx_ename ON tbl_object (ename) WHERE ename != '';



--
-- Table structure for table tbl_association
--

DROP TABLE IF EXISTS tbl_association;
CREATE TABLE tbl_association (
  id serial,
  source_eid varchar(12) NOT NULL default '',
  target_eid varchar(12) NOT NULL default '',
  association_type varchar(3) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (source_eid,association_type,target_eid),
  UNIQUE (target_eid,association_type,source_eid)
);



--
-- Table structure for table tbl_user
--

DROP TABLE IF EXISTS tbl_user;
CREATE TABLE tbl_user (
    id serial,
    eid varchar(12) NOT NULL,
    user_name varchar(80) NOT NULL default '',
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
    locale_language varchar(30) NOT NULL default 'en_US',
    locale_decimal varchar(1) NOT NULL default '.',
    locale_thousands varchar(1) NOT NULL default ',',
    locale_dateformat varchar(30) NOT NULL default 'm/d/Y',
    timezone varchar(30) NOT NULL default 'UTC',
    password varchar(80) NOT NULL default '',
    verify_code varchar(40) NOT NULL default '',
    config json,
    created timestamp NULL default NULL,
    updated timestamp NULL default NULL,
    PRIMARY KEY (id),
    UNIQUE (eid),
    UNIQUE (user_name),
    UNIQUE (email)
);



--
-- Table structure for table tbl_token
--

DROP TABLE IF EXISTS tbl_token;
CREATE TABLE tbl_token (
    id serial,
    eid varchar(12) NOT NULL,
    user_eid varchar(12) NOT NULL,
    access_code varchar(255) NOT NULL default '',
    secret_code varchar(255) NOT NULL default '',
    created timestamp NULL default NULL,
    updated timestamp NULL default NULL,
    PRIMARY KEY (id),
    UNIQUE (eid),
    UNIQUE (access_code)
);

CREATE INDEX idx_token_user_eid ON tbl_token (user_eid);



--
-- Table structure for table tbl_acl
--

DROP TABLE IF EXISTS tbl_acl;
CREATE TABLE tbl_acl (
  id serial,
  eid varchar(12) NOT NULL,
  object_eid varchar(12) NOT NULL default '',
  access_type varchar(3) NOT NULL default '',
  access_code varchar(255) NOT NULL default '',
  actions json,
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_acl_object_eid ON tbl_acl (object_eid);
CREATE INDEX idx_acl_access_code ON tbl_acl (access_code);



--
-- Table structure for table tbl_project
--

DROP TABLE IF EXISTS tbl_project;
CREATE TABLE tbl_project (
  id serial,
  eid varchar(12) NOT NULL default '',
  name text default '',
  description text default '',
  display_icon text default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);



--
-- Table structure for table tbl_pipe
--

-- schedule_status values
--    A - active
--    I - inactive

DROP TABLE IF EXISTS tbl_pipe;
CREATE TABLE tbl_pipe (
  id serial,
  eid varchar(12) NOT NULL default '',
  name text default '',
  description text default '',
  display_icon text default '',
  task json,
  input json,
  output json,
  schedule text default '',
  schedule_status varchar(1) NOT NULL default 'I',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_pipe_schedule_status ON tbl_pipe (schedule_status);



--
-- Table structure for table tbl_connection
--

DROP TABLE IF EXISTS tbl_connection;
CREATE TABLE tbl_connection (
  id serial,
  eid varchar(12) NOT NULL default '',
  name text default '',
  description text default '',
  display_icon text default '',
  connection_type varchar(40) NOT NULL default '',
  connection_status varchar(1) NOT NULL default 'U',
  connection_info text default '',
  expires timestamp NULL default NULL,
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);



--
-- Table structure for table tbl_process
--

DROP TABLE IF EXISTS tbl_process;
CREATE TABLE tbl_process (
  id serial,
  eid varchar(12) NOT NULL default '',
  parent_eid varchar(12) NOT NULL default '',
  process_mode varchar(1) NOT NULL default '',
  process_hash varchar(40) NOT NULL default '',
  impl_revision varchar(40) NOT NULL default '',
  task json,
  input json,
  output json,
  started_by varchar(12) NOT NULL default '',
  started timestamp NULL default NULL,
  finished timestamp NULL default NULL,
  process_info json,
  process_status varchar(1) NOT NULL default '',
  cache_used varchar(1) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_process_eid ON tbl_process (eid);
CREATE INDEX idx_process_parent_eid ON tbl_process (parent_eid);
CREATE INDEX idx_process_process_hash ON tbl_process (process_hash);



--
-- Table structure for table tbl_processlog
--

DROP TABLE IF EXISTS tbl_processlog;
CREATE TABLE tbl_processlog (
  id serial,
  eid varchar(12) NOT NULL default '',
  process_eid varchar(12) NOT NULL default '',
  task_op text default '',
  task_version int NOT NULL default 0,
  task json,
  input json,
  output json,
  started timestamp NULL default NULL,
  finished timestamp NULL default NULL,
  log_type varchar(1) NOT NULL default '',
  message text default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_processlog_eid ON tbl_processlog (eid);
CREATE INDEX idx_processlog_process_eid ON tbl_processlog (process_eid);



--
-- Table structure for table tbl_stream
--

DROP TABLE IF EXISTS tbl_stream;
CREATE TABLE tbl_stream (
  id serial,
  eid varchar(12) NOT NULL default '',
  parent_eid varchar(12) NOT NULL default '',
  stream_type varchar(3) NOT NULL default '',
  name text default '',
  path text default '',
  size numeric(12,0) default NULL,
  hash varchar(40) NOT NULL default '',
  mime_type text default '',
  structure json,
  file_created timestamp NULL default NULL,
  file_modified timestamp NULL default NULL,
  connection_eid varchar(12) NOT NULL default '',
  expires timestamp NULL default NULL,
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_stream_parent_eid ON tbl_stream (parent_eid);
CREATE INDEX idx_stream_connection_eid ON tbl_stream (connection_eid);



--
-- Table structure for table tbl_comment
--

DROP TABLE IF EXISTS tbl_comment;
CREATE TABLE tbl_comment (
  id serial,
  eid varchar(12) NOT NULL default '',
  comment text default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);



--
-- Table structure for table tbl_action
--

DROP TABLE IF EXISTS tbl_action;
CREATE TABLE tbl_action (
  id serial,
  user_eid varchar(12) NOT NULL default '',
  subject_eid varchar(12) NOT NULL default '',
  object_eid varchar(12) NOT NULL default '',
  action varchar(80) NOT NULL default '',
  params json,
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id)
);

CREATE INDEX idx_action_user_eid ON tbl_action (user_eid);
CREATE INDEX idx_action_subject_eid ON tbl_action (subject_eid);
CREATE INDEX idx_action_object_eid ON tbl_action (object_eid);
CREATE INDEX idx_action_action ON tbl_action (action);
CREATE INDEX idx_action_created ON tbl_action (created);



--
-- Table structure for table tbl_registry
--

DROP TABLE IF EXISTS tbl_registry;
CREATE TABLE tbl_registry (
  id serial,
  object_eid varchar(12) NOT NULL default '',
  name varchar(80) NOT NULL default '',
  mime_type varchar(80) NOT NULL default '',
  value_type varchar(1) default '',
  value text default NULL,
  expires timestamp NULL default NULL,
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (object_eid, name)
);



--
-- Table structure for table tbl_system
--

DROP TABLE IF EXISTS tbl_system;
CREATE TABLE tbl_system (
  id serial,
  name varchar(80) NOT NULL default '',
  value text default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (name)
);
