import * as types from './connection-type'

/* connection info */

const AMAZON_S3 = {
  is_service: true,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_AMAZONS3,
  icon: require('../assets/icon/icon-amazon-128.png'),
  service_name: 'Amazon S3',
  service_description: 'Amazon Simple Storage Service (S3)'
}

/*
const BLANK_PIPE = {
  is_service: false,
  is_input: true,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_BLANK_PIPE,
  icon: require('../assets/icon/icon-pipe-128.png'),
  service_name: 'Blank Pipe',
  service_description: ''
}
*/

const BOX = {
  is_service: true,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_BOX,
  icon: require('../assets/icon/icon-dropbox-128.png'),
  service_name: 'Box',
  service_description: 'Cloud file storage and syncing'
}

const DOWNLOAD = {
  is_service: false,
  is_input: false,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_DOWNLOAD,
  icon: require('../assets/icon/icon-download-128.png'),
  service_name: 'Download',
  service_description: 'Download files to your computer'
}

const DROPBOX = {
  is_service: true,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_DROPBOX,
  icon: require('../assets/icon/icon-dropbox-128.png'),
  service_name: 'Dropbox',
  service_description: 'Cloud file storage and syncing'
}

const ELASTICSEARCH = {
  is_service: true,
  is_input: false,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_ELASTICSEARCH,
  icon: require('../assets/icon/icon-elasticsearch-128.png'),
  service_name: 'Elasticsearch',
  service_description: 'Open source search and analytics'
}

const EMAIL = {
  is_service: false,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_EMAIL,
  icon: require('../assets/icon/icon-email-128.png'),
  service_name: 'Email',
  service_description: ''
}

const GOOGLEDRIVE = {
  is_service: true,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_GOOGLEDRIVE,
  icon: require('../assets/icon/icon-google-drive-128.png'),
  service_name: 'Google Drive',
  service_description: 'Online document and file storage'
}

const GOOGLESHEETS = {
  is_service: true,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_GOOGLESHEETS,
  icon: require('../assets/icon/icon-google-sheets-128.png'),
  service_name: 'Google Sheets',
  service_description: 'Online spreadsheets'
}

const HTTP = {
  is_service: false,
  is_input: true,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_HTTP,
  icon: require('../assets/icon/icon-link-128.png'),
  service_name: 'Web Link',
  service_description: ''
}

const MAILJET = {
  is_service: true,
  is_input: false,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_MAILJET,
  icon: require('../assets/icon/icon-mailjet-128.png'),
  service_name: 'Mailjet',
  service_description: 'Send email that converts'
}

const MYSQL = {
  is_service: true,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_MYSQL,
  icon: require('../assets/icon/icon-mysql-128.png'),
  service_name: 'MySQL',
  service_description: "The world's most popular open source database"
}

const PIPELINEDEALS = {
  is_service: true,
  is_input: true,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_PIPELINEDEALS,
  icon: require('../assets/icon/icon-pipelinedeals-128.png'),
  service_name: 'PipelineDeals',
  service_description: 'CRM software to start, develop and grow your business'
}

const POSTGRES = {
  is_service: true,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_POSTGRES,
  icon: require('../assets/icon/icon-postgres-128.png'),
  service_name: 'PostgreSQL',
  service_description: "The world's most advanced open source database"
}

const RSS = {
  is_service: false,
  is_input: true,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_RSS,
  icon: require('../assets/icon/icon-rss-128.png'),
  service_name: 'RSS Feed',
  service_description: ''
}

const SFTP = {
  is_service: true,
  is_input: true,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_SFTP,
  icon: require('../assets/icon/icon-ftp-128.png'),
  service_name: 'SFTP',
  service_description: 'Secure File Transfer Protocol'
}

const SOCRATA = {
  is_service: true,
  is_input: false,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_SOCRATA,
  icon: require('../assets/icon/icon-socrata-128.png'),
  service_name: 'Socrata',
  service_description: 'Open data portal for government data'
}

const STDIN = {
  is_service: false,
  is_input: true,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_STDIN,
  icon: require('../assets/icon/icon-console-128.png'),
  service_name: 'Stdin',
  service_description: 'Standard In'
}

const STDOUT = {
  is_service: false,
  is_input: false,
  is_output: true,
  connection_type: types.CONNECTION_TYPE_STDOUT,
  icon: require('../assets/icon/icon-console-128.png'),
  service_name: 'Stdout',
  service_description: 'Standard Out'
}

const UPLOAD = {
  is_service: false,
  is_input: true,
  is_output: false,
  connection_type: types.CONNECTION_TYPE_UPLOAD,
  icon: require('../assets/icon/icon-upload-128.png'),
  service_name: 'File Upload',
  service_description: 'Upload files from your computer'
}

/* exports */

// go out of alphabetical order here so the order is correct in the pipe add modal
export const CONNECTION_INFO_STDIN         = STDIN
// export const CONNECTION_INFO_UPLOAD        = UPLOAD
export const CONNECTION_INFO_STDOUT        = STDOUT
export const CONNECTION_INFO_EMAIL         = EMAIL
//export const CONNECTION_INFO_DOWNLOAD      = DOWNLOAD
export const CONNECTION_INFO_AMAZON_S3     = AMAZON_S3
//export const CONNECTION_INFO_BLANK_PIPE    = BLANK_PIPE
export const CONNECTION_INFO_DROPBOX       = DROPBOX
export const CONNECTION_INFO_BOX           = BOX
export const CONNECTION_INFO_ELASTICSEARCH = ELASTICSEARCH
export const CONNECTION_INFO_GOOGLEDRIVE   = GOOGLEDRIVE
export const CONNECTION_INFO_GOOGLESHEETS  = GOOGLESHEETS
export const CONNECTION_INFO_HTTP          = HTTP
//export const CONNECTION_INFO_MAILJET       = MAILJET
export const CONNECTION_INFO_MYSQL         = MYSQL
export const CONNECTION_INFO_POSTGRES      = POSTGRES
export const CONNECTION_INFO_RSS           = RSS
export const CONNECTION_INFO_SFTP          = SFTP
//export const CONNECTION_INFO_SOCRATA       = SOCRATA
//export const CONNECTION_INFO_PIPELINEDEALS = PIPELINEDEALS
