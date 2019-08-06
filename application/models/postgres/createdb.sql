

--
-- Table structure for table tbl_object
--

DROP TABLE IF EXISTS tbl_object;
CREATE TABLE tbl_object (
  id serial,
  eid varchar(12) NOT NULL default '',
  eid_type varchar(3) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE (eid)
);



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
  eid_status varchar(1) NOT NULL default '',
  username varchar(80) NOT NULL default '',
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
  stripe_customer_id varchar(20) NOT NULL default '',
  usage_tier varchar(20) NOT NULL default '',
  referrer text default '',
  config json,
  role varchar(1) NOT NULL default '',
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE UNIQUE INDEX idx_user_username ON tbl_user (username) WHERE username != '';
CREATE UNIQUE INDEX idx_user_email ON tbl_user (email) WHERE email != '';
CREATE UNIQUE INDEX idx_user_verify_code ON tbl_user (verify_code) WHERE verify_code != '';
CREATE INDEX idx_user_owned_by ON tbl_user (owned_by);
CREATE INDEX idx_user_created_by ON tbl_user (created_by);
CREATE INDEX idx_user_created ON tbl_user (created);



--
-- Table structure for table tbl_teammember
--

-- member_status values:
--    A - active
--    I - inactive

DROP TABLE IF EXISTS tbl_teammember;
CREATE TABLE tbl_teammember (
  id serial,
  member_eid varchar(12) NOT NULL default '',
  member_status varchar(1) NOT NULL default 'I',
  rights text default '',
  role varchar(1) NOT NULL default '',
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (member_eid,owned_by),
  UNIQUE (owned_by,member_eid)
);

CREATE INDEX idx_teammember_created_by ON tbl_teammember (created_by);



--
-- Table structure for table tbl_token
--

DROP TABLE IF EXISTS tbl_token;
CREATE TABLE tbl_token (
  id serial,
  eid varchar(12) NOT NULL,
  eid_status varchar(1) NOT NULL default '',
  access_code varchar(255) NOT NULL default '',
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid),
  UNIQUE (access_code)
);

CREATE INDEX idx_token_owned_by ON tbl_token (owned_by);
CREATE INDEX idx_token_created_by ON tbl_token (created_by);
CREATE INDEX idx_token_created ON tbl_token (created);


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
  id serial,
  eid varchar(12) NOT NULL default '',
  eid_status varchar(1) NOT NULL default '',
  name text default '',
  title text default '',
  description text default '',
  task json,
  schedule text default '',
  ui json,
  deploy_mode varchar(1) NOT NULL default 'B',
  deploy_schedule varchar(1) NOT NULL default 'I',
  deploy_email varchar(1) NOT NULL default 'I',
  deploy_api varchar(1) NOT NULL default 'I',
  deploy_ui varchar(1) NOT NULL default 'I',
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_pipe_name ON tbl_pipe (name);
CREATE INDEX idx_pipe_deploy_mode ON tbl_pipe (deploy_mode);
CREATE INDEX idx_pipe_deploy_schedule ON tbl_pipe (deploy_schedule);
CREATE INDEX idx_pipe_deploy_email ON tbl_pipe (deploy_email);
CREATE INDEX idx_pipe_deploy_api ON tbl_pipe (deploy_api);
CREATE INDEX idx_pipe_deploy_ui ON tbl_pipe (deploy_ui);
CREATE INDEX idx_pipe_owned_by ON tbl_pipe (owned_by);
CREATE INDEX idx_pipe_created_by ON tbl_pipe (created_by);
CREATE INDEX idx_pipe_created ON tbl_pipe (created);



--
-- Table structure for table tbl_connection
--

DROP TABLE IF EXISTS tbl_connection;
CREATE TABLE tbl_connection (
  id serial,
  eid varchar(12) NOT NULL default '',
  eid_status varchar(1) NOT NULL default '',
  name text default '',
  title text default '',
  description text default '',
  connection_type varchar(40) NOT NULL default '',
  connection_status varchar(1) NOT NULL default 'U',
  connection_info text default '',
  expires timestamp NULL default NULL,
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_connection_name ON tbl_connection (name);
CREATE INDEX idx_connection_owned_by ON tbl_connection (owned_by);
CREATE INDEX idx_connection_created_by ON tbl_connection (created_by);
CREATE INDEX idx_connection_created ON tbl_connection (created);



--
-- Table structure for table tbl_process
--

DROP TABLE IF EXISTS tbl_process;
CREATE TABLE tbl_process (
  id serial,
  eid varchar(12) NOT NULL default '',
  eid_status varchar(1) NOT NULL default '',
  parent_eid varchar(12) NOT NULL default '',
  pipe_info json,
  process_mode varchar(1) NOT NULL default '',
  task json,
  input json,
  output json,
  triggered_by varchar(1) NOT NULL default '',
  started_by varchar(12) NOT NULL default '',
  started timestamp NULL default NULL,
  finished timestamp NULL default NULL,
  process_info json,
  process_status varchar(1) NOT NULL default '',
  cache_used varchar(1) NOT NULL default '',
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_process_eid ON tbl_process (eid);
CREATE INDEX idx_process_parent_eid ON tbl_process (parent_eid);
CREATE INDEX idx_process_triggered_by ON tbl_process (triggered_by);
CREATE INDEX idx_process_owned_by ON tbl_process (owned_by);
CREATE INDEX idx_process_created_by ON tbl_process (created_by);
CREATE INDEX idx_process_created ON tbl_process (created);



--
-- Table structure for table tbl_processlog
--

DROP TABLE IF EXISTS tbl_processlog;
CREATE TABLE tbl_processlog (
  id serial,
  eid varchar(12) NOT NULL default '',
  eid_status varchar(1) NOT NULL default '',
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
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_processlog_eid ON tbl_processlog (eid);
CREATE INDEX idx_processlog_process_eid ON tbl_processlog (process_eid);
CREATE INDEX idx_processlog_owned_by ON tbl_processlog (owned_by);
CREATE INDEX idx_processlog_created_by ON tbl_processlog (created_by);
CREATE INDEX idx_processlog_created ON tbl_processlog (created);



--
-- Table structure for table tbl_stream
--

DROP TABLE IF EXISTS tbl_stream;
CREATE TABLE tbl_stream (
  id serial,
  eid varchar(12) NOT NULL default '',
  eid_status varchar(1) NOT NULL default '',
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
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_stream_parent_eid ON tbl_stream (parent_eid);
CREATE INDEX idx_stream_connection_eid ON tbl_stream (connection_eid);
CREATE INDEX idx_stream_owned_by ON tbl_stream (owned_by);
CREATE INDEX idx_stream_created_by ON tbl_stream (created_by);
CREATE INDEX idx_stream_created ON tbl_stream (created);



--
-- Table structure for table tbl_comment
--

DROP TABLE IF EXISTS tbl_comment;
CREATE TABLE tbl_comment (
  id serial,
  eid varchar(12) NOT NULL default '',
  eid_status varchar(1) NOT NULL default '',
  comment text default '',
  owned_by varchar(12) NOT NULL default '',
  created_by varchar(12) NOT NULL default '',
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_comment_owned_by ON tbl_comment (owned_by);
CREATE INDEX idx_comment_created_by ON tbl_comment (created_by);
CREATE INDEX idx_comment_created ON tbl_comment (created);



--
-- Table structure for table tbl_action
--

DROP TABLE IF EXISTS tbl_action;
CREATE TABLE tbl_action (
  id serial,
  eid varchar(12) NOT NULL default '',                  -- the eid of the action
  eid_status varchar(1) NOT NULL default '',            -- the eid status of the action
  action_type varchar(40) NOT NULL default '',          -- logical name for the action (e.g. "create", helps map mutiple routes to similar action, such as creating an object)
  request_ip varchar(40) NOT NULL default '',           -- ip address of the request if available
  request_user_agent text default '',                   -- user agent for the request
  request_type varchar(12) NOT NULL default '',         -- request type (e.g. "HTTP")
  request_method varchar(12) NOT NULL default '',       -- specific method for the request type (e.g. "PUT", "POST", "DELETE", etc)
  request_route text default '',                        -- the specific route used to request the action (e.g. the url path of the request)
  request_access_code varchar(255) NOT NULL default '', -- the token used to
  request_created_by varchar(12) NOT NULL default '',   -- the user making the request
  request_created timestamp NULL default NULL,          -- timestamp when the request was created
  request_params json,                                  -- specific parameters included with the request; note: not all params may be saved (e.g. passwords)
  target_eid varchar(12) NOT NULL default '',           -- object eid being created, changed, deleted, etc
  target_eid_type varchar(3) NOT NULL default '',       -- object eid type being created
  target_owned_by varchar(12) NOT NULL default '',      -- owner eid of the object being created, changed, deleted, etc
  response_type varchar(12) NOT NULL default '',        -- response type (e.g. "HTTP")
  response_code varchar(12) NOT NULL default '',        -- specific code for the response type (e.g. "200", "404", etc)
  response_params json,                                 -- subset of info returned to the user (e.g. error code and message or basic info about object)
  response_created timestamp NULL default NULL,         -- timestamp when the response was created
  owned_by varchar(12) NOT NULL default '',             -- same as request_created_by
  created_by varchar(12) NOT NULL default '',           -- same as request_created_by
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);

CREATE INDEX idx_action_action_type ON tbl_action (action_type);
CREATE INDEX idx_action_request_created_by ON tbl_action (request_created_by);
CREATE INDEX idx_action_target_eid ON tbl_action (target_eid);
CREATE INDEX idx_action_target_eid_type ON tbl_action (target_eid_type);
CREATE INDEX idx_action_target_owned_by ON tbl_action (target_owned_by);
CREATE INDEX idx_action_owned_by ON tbl_action (owned_by);
CREATE INDEX idx_action_created_by ON tbl_action (created_by);
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

