import _ from 'lodash'
import api from '../../api'

const state = {
  def: {
    title: 'Copy files from Google Drive to Dropbox',
    description: 'Copy files from Google Drive to Dropbox programmatically or on a regularly scheduled basis',
    keywords: [
      'flex.io',
      'web apps',
      'programmatic',
      'automation',
      'syncing',
      'integrations'
    ],
    connections: [
      'googledrive',
      'dropbox'
    ],
    intro: "# Copy files from Google Drive to Dropbox\n\nWant an easier way to programmatically copy files from Google Drive to Dropbox? With this integration, files can be bulk copied from Google Drive to Dropbox in the original file format (e.g. PDF, PNG or DOCX). You'll never need to worry about keeping cloud storage folders in sync again.\n\n### How it Works\n\nWhen this pipe is run programmatically or on a regularly scheduled basis, Flex.io copies the specified files or folders from Google Drive to Dropbox so you can keep them in sync between your cloud services.\n\n### What You Need\n\nGoogle Drive account\nDropbox account\n",
    prompts: [
      {
        variable: 'connection1',
        connection_type: 'googledrive',
        ui: 'connection-chooser'
      },
      {
        variable: 'read_path',
        connection: 'prompts.connection1',
        ui: 'file-chooser'
      },
      {
        variable: 'connection2',
        connection_type: 'dropbox',
        ui: 'connection-chooser'
      },
      {
        variable: 'write_path',
        connection: 'prompts.connection2',
        ui: 'file-chooser'
      }
    ],
    task: [
      {
        op: 'read',
        connection: '${connection1}',
        path: '${read_path}'
      },
      {
        op: 'write',
        connection: '${connection2}',
        path: '${write_path}'
      }
    ]
  },
  title: '',
  prompts: [],
  active_prompt: {},
  active_prompt_idx: null
}

const mutations = {
  BUILDER__INIT_ITEMS (state) {
    var prompts = _.get(state, 'def.prompts', [])

    state.title = state.def.title
    state.prompts = _.map(prompts, p => {
      if (p.ui == 'connection-chooser')
        return _.assign(p, { connection_eid: null })

      return p
    })
    state.active_prompt_idx = 0
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  },

  BUILDER__UPDATE_ACTIVE_ITEM (state, attrs) {
    state.active_prompt = _.assign({}, state.active_prompt, attrs)

    var idx = 0
    state.prompts = _.map(state.prompts, p => {
      if (idx == state.active_prompt_idx) {
        idx++
        return state.active_prompt
      } else {
        idx++
        return p
      }
    })
  },

  BUILDER__GO_PREV_ITEM (state) {
    state.active_prompt_idx--
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  },

  BUILDER__GO_NEXT_ITEM (state) {
    state.active_prompt_idx++
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  }
}

const actions = {}

const getters = {}

export default {
  state,
  mutations,
  actions,
  getters
}

