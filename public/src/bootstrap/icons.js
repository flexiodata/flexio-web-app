/**
 * The file enables us to import all PNG files in the /assets/icon folder
 * in a one-shot manner. There should not be any reason to edit this file.
 */

const files = require.context('../assets/icon', false, /\.png$/)

const paths = {}

files.keys().forEach(key => {
  paths[key.replace(/(\.\/|\.js)/g, '')] = files(key)
})

export default paths
