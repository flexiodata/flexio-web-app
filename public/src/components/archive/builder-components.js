/**
 * The file enables `@/components/builder-components.js` to import all BuilderItem
 * components a one-shot manner. There should not be any reason to edit this file.
 */

const files = require.context('.', false, /BuilderItem[\w-]+\.vue$/)
const components = {}

files.keys().forEach(key => {
  if (key === './BuilderItem.vue') return
  components[key.replace(/(\.\/|\.vue)/g, '')] = files(key).default
})

export default components
