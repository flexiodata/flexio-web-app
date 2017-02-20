import api from '../../api'

// ----------------------------------------------------------------------- //

export const sendEmailSupport = ({ commit }, { attrs }) => {
  return api.sendEmailSupport({ attrs })
}
