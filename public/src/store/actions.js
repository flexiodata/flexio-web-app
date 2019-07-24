const files = require.context('./modules', false, /\.js$/)
const modules_names = []

files.keys().forEach(key => {
  if (key === './index.js') return
  modules_names.push(key.replace(/(\.\/|\.js)/g, ''))
})

export default {
  'resetState' ({ commit }) {
    commit('RESET_STATE')
    _.each(modules_names, module => { commit(`${module}/RESET_STATE`) })
  },
}
