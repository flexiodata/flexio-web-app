/**
 * The file enables us to import all YAML files in this folder
 * in a one-shot manner. There should not be any reason to edit this file.
 */

const files = require.context('.', false, /\.yml$/)
const items = {}

files.keys().forEach(key => {
  if (key === './index.js') return
  items[key.replace(/(\.\/|\.yml)/g, '')] = files(key)
})

export default items
