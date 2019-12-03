import * as types from './connection-type'

/* connection info */

const AMAZON_S3 = {
  service_name: 'Amazon S3',
  service_description: 'Amazon Simple Storage Service (S3)',
  connection_type: types.CONNECTION_TYPE_AMAZONS3,
  icon: require('../assets/icon/icon-amazon-128.png'),
  is_oauth: false,
  is_storage: true,
  is_filesystem: true,
  is_function_mount: true,
}

const BOX = {
  service_name: 'Box',
  service_description: 'Secure, share and edit all your files from anywhere',
  connection_type: types.CONNECTION_TYPE_BOX,
  icon: require('../assets/icon/icon-box-128.png'),
  is_oauth: true,
  is_storage: true,
  is_filesystem: true,
  is_function_mount: true,
}

const CAPSULECRM = {
  service_name: 'Capsule CRM',
  service_description: 'Build stronger customer relationships, make more sales and save time',
  connection_type: types.CONNECTION_TYPE_CAPSULECRM,
  icon: require('../assets/icon/icon-capsulecrm-128.png'),
  is_oauth: true,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const CRUNCHBASE = {
  service_name: 'Crunchbase',
  service_description: 'Discover innovative companies and the people behind them',
  connection_type: types.CONNECTION_TYPE_CRUNCHBASE,
  icon: require('../assets/icon/icon-crunchbase-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const DROPBOX = {
  service_name: 'Dropbox',
  service_description: 'Cloud file storage and syncing',
  connection_type: types.CONNECTION_TYPE_DROPBOX,
  icon: require('../assets/icon/icon-dropbox-128.png'),
  is_oauth: true,
  is_storage: true,
  is_filesystem: true,
  is_function_mount: true,
}

const FLEX = {
  service_name: 'Flex.io',
  service_description: '',
  connection_type: types.CONNECTION_TYPE_FLEX,
  icon: require('../assets/icon/icon-flexio-128.png'),
  is_oauth: false,
  is_storage: true,
  is_filesystem: true,
  is_function_mount: false,
}

const GITHUB = {
  service_name: 'GitHub',
  service_description: 'Source control and code management',
  connection_type: types.CONNECTION_TYPE_GITHUB,
  icon: require('../assets/icon/icon-github-128.png'),
  is_oauth: true,
  is_storage: true,
  is_filesystem: true,
  is_function_mount: true,
}

const GOOGLEDRIVE = {
  service_name: 'Google Drive',
  service_description: 'Online document and file storage',
  connection_type: types.CONNECTION_TYPE_GOOGLEDRIVE,
  icon: require('../assets/icon/icon-google-drive-128.png'),
  is_oauth: true,
  is_storage: true,
  is_filesystem: true,
  is_function_mount: true,
}

const GOOGLESHEETS = {
  service_name: 'Google Sheets',
  service_description: 'Online spreadsheets',
  connection_type: types.CONNECTION_TYPE_GOOGLESHEETS,
  icon: require('../assets/icon/icon-google-sheets-128.png'),
  is_oauth: true,
  is_storage: true,
  is_filesystem: false,
  is_function_mount: false,
}

const HUBSPOT = {
    service_name: 'HubSpot',
    service_description: 'There’s a better way to grow',
    connection_type: types.CONNECTION_TYPE_HUBSPOT,
    icon: require('../assets/icon/icon-hubspot-128.png'),
    is_oauth: true,
    is_storage: false,
    is_filesystem: false,
    is_function_mount: false,
  }

const INTERCOM = {
  service_name: 'Intercom',
  service_description: "",
  connection_type: types.CONNECTION_TYPE_INTERCOM,
  icon: require('../assets/icon/icon-intercom-128.png'),
  is_oauth: true,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const KEYRING = {
  service_name: 'Keyring',
  service_description: 'Simple Keypair Storage',
  connection_type: types.CONNECTION_TYPE_KEYRING,
  icon: require('../assets/icon/icon-keyring-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const LINKEDIN = {
  service_name: 'LinkedIn',
  service_description: "",
  connection_type: types.CONNECTION_TYPE_LINKEDIN,
  icon: require('../assets/icon/icon-linkedin-128.png'),
  is_oauth: true,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const MYSQL = {
  service_name: 'MySQL',
  service_description: "The world's most popular open source database",
  connection_type: types.CONNECTION_TYPE_MYSQL,
  icon: require('../assets/icon/icon-mysql-128.png'),
  is_oauth: false,
  is_storage: true,
  is_filesystem: false,
  is_function_mount: false,
}

const POSTGRES = {
  service_name: 'PostgreSQL',
  service_description: "The world's most advanced open source database",
  connection_type: types.CONNECTION_TYPE_POSTGRES,
  icon: require('../assets/icon/icon-postgres-128.png'),
  is_oauth: false,
  is_storage: true,
  is_filesystem: false,
  is_function_mount: false,
}

const PRODUCTHUNT = {
  service_name: 'Product Hunt',
  service_description: "Discover your next favorite thing",
  connection_type: types.CONNECTION_TYPE_PRODUCTHUNT,
  icon: require('../assets/icon/icon-producthunt-128.png'),
  is_oauth: true,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const PIPEDRIVE = {
  service_name: 'Pipedrive',
  service_description: "",
  connection_type: types.CONNECTION_TYPE_PIPEDRIVE,
  icon: require('../assets/icon/icon-pipedrive-128.png'),
  is_oauth: true,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const SHOPIFY = {
  service_name: 'Shopify',
  service_description: "The all-in-one commerce platform to start, run, and grow a business",
  connection_type: types.CONNECTION_TYPE_SHOPIFY,
  icon: require('../assets/icon/icon-shopify-128.png'),
  is_oauth: true,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const SFTP = {
  service_name: 'SFTP',
  service_description: 'Transfer files using the SSH (secure shell) File Transfer Protocol',
  connection_type: types.CONNECTION_TYPE_SFTP,
  icon: require('../assets/icon/icon-ftp-128.png'),
  is_oauth: false,
  is_storage: true,
  is_filesystem: true,
  is_function_mount: false,
}

const TWITTER = {
  service_name: 'Twitter',
  service_description: "",
  connection_type: types.CONNECTION_TYPE_TWITTER,
  icon: require('../assets/icon/icon-twitter-128.png'),
  is_oauth: true,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

/* exports */

// go out of alphabetical order here so the order is correct in the function add modal
export const CONNECTION_INFO_FLEX                 = FLEX
export const CONNECTION_INFO_AMAZON_S3            = AMAZON_S3
export const CONNECTION_INFO_BOX                  = BOX
export const CONNECTION_INFO_DROPBOX              = DROPBOX
export const CONNECTION_INFO_GITHUB               = GITHUB
//export const CONNECTION_INFO_TWITTER              = TWITTER
export const CONNECTION_INFO_GOOGLEDRIVE          = GOOGLEDRIVE
export const CONNECTION_INFO_GOOGLESHEETS         = GOOGLESHEETS
export const CONNECTION_INFO_HUBSPOT              = HUBSPOT
export const CONNECTION_INFO_INTERCOM             = INTERCOM
//export const CONNECTION_INFO_LINKEDIN             = LINKEDIN
export const CONNECTION_INFO_CRUNCHBASE           = CRUNCHBASE
export const CONNECTION_INFO_MYSQL                = MYSQL
export const CONNECTION_INFO_POSTGRES             = POSTGRES
export const CONNECTION_INFO_PRODUCTHUNT          = PRODUCTHUNT
export const CONNECTION_INFO_SHOPIFY              = SHOPIFY
export const CONNECTION_INFO_CAPSULECRM           = CAPSULECRM
export const CONNECTION_INFO_PIPEDRIVE            = PIPEDRIVE
export const CONNECTION_INFO_SFTP                 = SFTP
export const CONNECTION_INFO_KEYRING              = KEYRING




/*
const API = {
  service_name: 'API',
  service_description: 'Connect to any REST API',
  connection_type: types.CONNECTION_TYPE_HTTP,
  icon: require('../assets/icon/icon-custom-api-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const STDIN = {
  service_name: 'Stdin',
  service_description: 'Standard In',
  connection_type: types.CONNECTION_TYPE_STDIN,
  icon: require('../assets/icon/icon-console-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const STDOUT = {
  service_name: 'Stdout',
  service_description: 'Standard Out',
  connection_type: types.CONNECTION_TYPE_STDOUT,
  icon: require('../assets/icon/icon-console-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const UPLOAD = {
  service_name: 'File Upload',
  service_description: 'Upload files from your computer',
  connection_type: types.CONNECTION_TYPE_UPLOAD,
  icon: require('../assets/icon/icon-upload-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const DOWNLOAD = {
  service_name: 'Download',
  service_description: 'Download files to your computer',
  connection_type: types.CONNECTION_TYPE_DOWNLOAD,
  icon: require('../assets/icon/icon-download-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const ELASTICSEARCH = {
  service_name: 'Elasticsearch',
  service_description: 'Open source search and analytics',
  connection_type: types.CONNECTION_TYPE_ELASTICSEARCH,
  icon: require('../assets/icon/icon-elasticsearch-128.png'),
  is_oauth: false,
  is_storage: true,
  is_filesystem: false,
  is_function_mount: false,
}

const FIREBASE = {
  service_name: 'Firebase',
  service_description: 'Mobile and web application development platform',
  connection_type: types.CONNECTION_TYPE_FIREBASE,
  icon: require('../assets/icon/icon-firebase-128.png'),
  is_oauth: false,
  is_storage: true,
  is_filesystem: false,
  is_function_mount: false,
}

const GOOGLECLOUDSTORAGE = {
  service_name: 'Google Cloud Storage',
  service_description: 'Unified object storage for developers and enterprises',
  connection_type: types.CONNECTION_TYPE_GOOGLECLOUDSTORAGE,
  icon: require('../assets/icon/icon-google-cloud-storage-128.png'),
  is_oauth: true,
  is_storage: true,
  is_filesystem: false,
  is_function_mount: false,
}

const GMAIL = {
  service_name: 'Gmail',
  service_description: "Google's free email service",
  connection_type: types.CONNECTION_TYPE_GMAIL,
  icon: require('../assets/icon/icon-gmail-128.png'),
  is_oauth: true,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const MAILJET = {
  service_name: 'Mailjet',
  service_description: 'Send email that converts',
  connection_type: types.CONNECTION_TYPE_MAILJET,
  icon: require('../assets/icon/icon-mailjet-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const RSS = {
  service_name: 'RSS Feed',
  service_description: '',
  connection_type: types.CONNECTION_TYPE_RSS,
  icon: require('../assets/icon/icon-rss-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const SMTP = {
  service_name: 'Email (SMTP)',
  service_description: 'Send and receive email using the Simple Mail Transfer Protocol',
  connection_type: types.CONNECTION_TYPE_SMTP,
  icon: require('../assets/icon/icon-email-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const SOCRATA = {
  service_name: 'Socrata',
  service_description: 'Open data portal for government data',
  connection_type: types.CONNECTION_TYPE_SOCRATA,
  icon: require('../assets/icon/icon-socrata-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const PIPELINEDEALS = {
  service_name: 'PipelineDeals',
  service_description: 'CRM software to start, develop and grow your business',
  connection_type: types.CONNECTION_TYPE_PIPELINEDEALS,
  icon: require('../assets/icon/icon-pipelinedeals-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}

const TWILIO = {
  service_name: 'Twilio',
  service_description: 'Programmatically make and receive phone calls and send and receive text messages',
  connection_type: types.CONNECTION_TYPE_TWILIO,
  icon: require('../assets/icon/icon-twilio-128.png'),
  is_oauth: false,
  is_storage: false,
  is_filesystem: false,
  is_function_mount: false,
}
*/

// TODO: what are we going to do with these?
//export const CONNECTION_INFO_API                  = API
//export const CONNECTION_INFO_STDIN                = STDIN
//export const CONNECTION_INFO_STDOUT               = STDOUT
//export const CONNECTION_INFO_UPLOAD               = UPLOAD
//export const CONNECTION_INFO_DOWNLOAD             = DOWNLOAD
//export const CONNECTION_INFO_ELASTICSEARCH        = ELASTICSEARCH
//export const CONNECTION_INFO_FIREBASE             = FIREBASE
//export const CONNECTION_INFO_MAILJET              = MAILJET
//export const CONNECTION_INFO_GOOGLECLOUDSTORAGE   = GOOGLECLOUDSTORAGE
//export const CONNECTION_INFO_GMAIL                = GMAIL
//export const CONNECTION_INFO_RSS                  = RSS
//export const CONNECTION_INFO_SMTP                 = SMTP
//export const CONNECTION_INFO_SOCRATA              = SOCRATA
//export const CONNECTION_INFO_PIPELINEDEALS        = PIPELINEDEALS
//export const CONNECTION_INFO_TWILIO               = TWILIO

