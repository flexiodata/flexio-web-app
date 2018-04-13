import _ from 'lodash'

const state = {
  def: {
    name: "Copy files from Google Drive to Dropbox",
    description: "Copy files from Google Drive to Dropbox programmatically or on a regularly scheduled basis",
    keywords: [
      "flex.io",
      "web apps",
      "programmatic",
      "automation",
      "syncing",
      "integrations"
    ],
    connections: [
      "googledrive",
      "dropbox"
    ],
    intro: "# Copy files from Google Drive to Dropbox\n\nWant an easier way to programmatically copy files from Google Drive to Dropbox? With this integration, files can be bulk copied from Google Drive to Dropbox in the original file format (e.g. PDF, PNG or DOCX). You'll never need to worry about keeping cloud storage folders in sync again.\n\n### How it Works\n\nWhen this pipe is run programmatically or on a regularly scheduled basis, Flex.io copies the specified files or folders from Google Drive to Dropbox so you can keep them in sync between your cloud services.\n\n### What You Need\n\nGoogle Drive account\nDropbox account\n",
    variables: {
      connection1: {
        type: "connection",
        connection_type: "googledrive"
      },
      read_path: {
        type: "string",
        val: "/input-folder/file.txt",
        runtime: true
      },
      connection2: {
        type: "connection",
        connection_type: "dropbox"
      },
      write_path: {
        type: "string",
        val: "/output-folder/file.txt",
        runtime: true
      }
    },
    task: [
      {
        op: "read",
        connection: "${connection1}",
        path: "${read_path}"
      },
      {
        op: "write",
        connection: "${connection2}",
        path: "${write_path}"
      }
    ]
  },
  items: [],
  active_item: {}
}

const mutations = {
  BUILDER__INIT_ITEMS (state) {
    var idx = 0
    var variables = _.get(state, 'def.variables', {})

    state.items = _.mapValues(variables, (item) => {
      return _.assign({}, item, { idx: idx++ })
    })
    state.active_item = _.find(state.items, { idx: 0 })
  },

  BUILDER__GO_PREV_ITEM (state) {
    var active_idx = _.get(state, 'active_item.idx', 0)
    var item = _.find(state.items, { idx: active_idx - 1 })

    if (!_.isNil(item))
      state.active_item = _.assign({}, item)
  },

  BUILDER__GO_NEXT_ITEM (state) {
    var active_idx = _.get(state, 'active_item.idx', 1000)
    var item = _.find(state.items, { idx:  active_idx + 1 })

    if (!_.isNil(item))
      state.active_item = _.assign({}, item)
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

