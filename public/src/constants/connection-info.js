import * as types from './connection-type'

/* connection info */

const AMAZON_S3 = {
  service_name: 'Amazon S3',
  service_description: 'Amazon Simple Storage Service (S3)',
  connection_type: types.CONNECTION_TYPE_AMAZONS3,
  icon: require('../assets/icon/icon-amazon-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const BOX = {
  service_name: 'Box',
  service_description: 'Secure, share and edit all your files from anywhere',
  connection_type: types.CONNECTION_TYPE_BOX,
  icon: require('../assets/icon/icon-box-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const CUSTOM_API = {
  service_name: 'Custom API',
  service_description: 'Connect to any REST API',
  connection_type: types.CONNECTION_TYPE_HTTP,
  icon: require('../assets/icon/icon-custom-api-128.png'),
  is_service: false,
  is_input: true,
  is_output: false,
  is_storage: false
}

const DOWNLOAD = {
  service_name: 'Download',
  service_description: 'Download files to your computer',
  connection_type: types.CONNECTION_TYPE_DOWNLOAD,
  icon: require('../assets/icon/icon-download-128.png'),
  is_service: false,
  is_input: false,
  is_output: false,
  is_storage: false
}

const DROPBOX = {
  service_name: 'Dropbox',
  service_description: 'Cloud file storage and syncing',
  connection_type: types.CONNECTION_TYPE_DROPBOX,
  icon: require('../assets/icon/icon-dropbox-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const ELASTICSEARCH = {
  service_name: 'Elasticsearch',
  service_description: 'Open source search and analytics',
  connection_type: types.CONNECTION_TYPE_ELASTICSEARCH,
  icon: require('../assets/icon/icon-elasticsearch-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const FIREBASE = {
  service_name: 'Firebase',
  service_description: 'Mobile and web application development platform',
  connection_type: types.CONNECTION_TYPE_FIREBASE,
  icon: require('../assets/icon/icon-firebase-128.png'),
  is_service: true,
  is_input: true,
  is_output: false,
  is_storage: true
}

const GITHUB = {
  service_name: 'GitHub',
  service_description: 'Source control and code management',
  connection_type: types.CONNECTION_TYPE_GITHUB,
  icon: require('../assets/icon/icon-github-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const GMAIL = {
  service_name: 'Gmail',
  service_description: "Google's free email service",
  connection_type: types.CONNECTION_TYPE_GMAIL,
  icon: require('../assets/icon/icon-gmail-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const GOOGLEDRIVE = {
  service_name: 'Google Drive',
  service_description: 'Online document and file storage',
  connection_type: types.CONNECTION_TYPE_GOOGLEDRIVE,
  icon: require('../assets/icon/icon-google-drive-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const GOOGLESHEETS = {
  service_name: 'Google Sheets',
  service_description: 'Online spreadsheets',
  connection_type: types.CONNECTION_TYPE_GOOGLESHEETS,
  icon: require('../assets/icon/icon-google-sheets-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const HOME = {
  service_name: 'Local Storage',
  service_description: '',
  connection_type: types.CONNECTION_TYPE_HOME,
  icon: require('../assets/icon/icon-home-128.png'),
  is_service: false,
  is_input: false,
  is_output: false,
  is_storage: false
}

const HTTP = {
  service_name: 'Web Link',
  service_description: '',
  connection_type: types.CONNECTION_TYPE_HTTP,
  icon: require('../assets/icon/icon-link-128.png'),
  is_service: false,
  is_input: true,
  is_output: false,
  is_storage: false
}

const MAILJET = {
  service_name: 'Mailjet',
  service_description: 'Send email that converts',
  connection_type: types.CONNECTION_TYPE_MAILJET,
  icon: require('../assets/icon/icon-mailjet-128.png'),
  is_service: true,
  is_input: false,
  is_output: false,
  is_storage: false
}

const MYSQL = {
  service_name: 'MySQL',
  service_description: "The world's most popular open source database",
  connection_type: types.CONNECTION_TYPE_MYSQL,
  icon: require('../assets/icon/icon-mysql-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const PIPELINEDEALS = {
  service_name: 'PipelineDeals',
  service_description: 'CRM software to start, develop and grow your business',
  connection_type: types.CONNECTION_TYPE_PIPELINEDEALS,
  icon: require('../assets/icon/icon-pipelinedeals-128.png'),
  is_service: true,
  is_input: true,
  is_output: false,
  is_storage: false
}

const POSTGRES = {
  service_name: 'PostgreSQL',
  service_description: "The world's most advanced open source database",
  connection_type: types.CONNECTION_TYPE_POSTGRES,
  icon: require('../assets/icon/icon-postgres-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const RSS = {
  service_name: 'RSS Feed',
  service_description: '',
  connection_type: types.CONNECTION_TYPE_RSS,
  icon: require('../assets/icon/icon-rss-128.png'),
  is_service: false,
  is_input: true,
  is_output: false,
  is_storage: false
}

const SMTP = {
  service_name: 'Email (SMTP)',
  service_description: '',
  connection_type: types.CONNECTION_TYPE_SMTP,
  icon: require('../assets/icon/icon-email-128.png'),
  is_service: false,
  is_input: true,
  is_output: true,
  is_storage: false
}

const SFTP = {
  service_name: 'SFTP',
  service_description: 'Secure File Transfer Protocol',
  connection_type: types.CONNECTION_TYPE_SFTP,
  icon: require('../assets/icon/icon-ftp-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const SOCRATA = {
  service_name: 'Socrata',
  service_description: 'Open data portal for government data',
  connection_type: types.CONNECTION_TYPE_SOCRATA,
  icon: require('../assets/icon/icon-socrata-128.png'),
  is_service: true,
  is_input: false,
  is_output: false,
  is_storage: false
}

const STDIN = {
  service_name: 'Stdin',
  service_description: 'Standard In',
  connection_type: types.CONNECTION_TYPE_STDIN,
  icon: require('../assets/icon/icon-console-128.png'),
  is_service: false,
  is_input: true,
  is_output: false,
  is_storage: false
}

const STDOUT = {
  service_name: 'Stdout',
  service_description: 'Standard Out',
  connection_type: types.CONNECTION_TYPE_STDOUT,
  icon: require('../assets/icon/icon-console-128.png'),
  is_service: false,
  is_input: false,
  is_output: true,
  is_storage: false
}

const TWILIO = {
  service_name: 'Twilio',
  service_description: 'Programmatically make and receive phone calls and send and receive text messages',
  connection_type: types.CONNECTION_TYPE_TWILIO,
  icon: require('../assets/icon/icon-twilio-128.png'),
  is_service: true,
  is_input: true,
  is_output: true,
  is_storage: true
}

const UPLOAD = {
  service_name: 'File Upload',
  service_description: 'Upload files from your computer',
  connection_type: types.CONNECTION_TYPE_UPLOAD,
  icon: require('../assets/icon/icon-upload-128.png'),
  is_service: false,
  is_input: true,
  is_output: false,
  is_storage: false
}

/* exports */

// TODO: what are we going to do with these?
//export const CONNECTION_INFO_STDIN         = STDIN
//export const CONNECTION_INFO_STDOUT        = STDOUT
//export const CONNECTION_INFO_UPLOAD        = UPLOAD
//export const CONNECTION_INFO_DOWNLOAD      = DOWNLOAD
//export const CONNECTION_INFO_FIREBASE      = FIREBASE
//export const CONNECTION_INFO_HTTP          = HTTP
//export const CONNECTION_INFO_MAILJET       = MAILJET
//export const CONNECTION_INFO_RSS           = RSS
//export const CONNECTION_INFO_SOCRATA       = SOCRATA
//export const CONNECTION_INFO_PIPELINEDEALS = PIPELINEDEALS

// go out of alphabetical order here so the order is correct in the pipe add modal
export const CONNECTION_INFO_CUSTOM_API    = CUSTOM_API
export const CONNECTION_INFO_AMAZON_S3     = AMAZON_S3
export const CONNECTION_INFO_BOX           = BOX
export const CONNECTION_INFO_DROPBOX       = DROPBOX
export const CONNECTION_INFO_ELASTICSEARCH = ELASTICSEARCH
export const CONNECTION_INFO_SMTP          = SMTP
export const CONNECTION_INFO_GITHUB        = GITHUB
export const CONNECTION_INFO_GMAIL         = GMAIL
export const CONNECTION_INFO_GOOGLEDRIVE   = GOOGLEDRIVE
export const CONNECTION_INFO_GOOGLESHEETS  = GOOGLESHEETS
export const CONNECTION_INFO_HOME          = HOME
export const CONNECTION_INFO_MYSQL         = MYSQL
export const CONNECTION_INFO_POSTGRES      = POSTGRES
export const CONNECTION_INFO_SFTP          = SFTP
export const CONNECTION_INFO_TWILIO        = TWILIO
