/**
 * The file enables `@/data/onboarding/index.js` to import all onboarding files
 * in a one-shot manner. There should not be any reason to edit this file.
 */

const files = require.context('.', false, /\.yml$/)
const items = {}

files.keys().forEach(key => {
  if (key === './index.js') return
  items[key.replace(/(\.\/|\.yml)/g, '')] = files(key)
})

export default items
