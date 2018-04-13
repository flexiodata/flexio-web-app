<template>
  <div class="bg-nearer-white pv4">
    <div class="center mw7">
      <h1 class="f3 mid-gray mb4">{{output.name}}</h1>
      <div class="css-dashboard-box pa5 tc">
        Action box
      </div>
    </div>
  </div>
</template>

<script>
  var def = {
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
  }

  export default {
    watch: {
      active_idx: {
        handler: 'updateActiveItem',
        immediate: true
      }
    },
    data() {
      return {
        def,
        active_idx: -1,
        active_item: {},
        output: {
          name: def.name,
          task: {
            op: 'sequence',
            params: {
              items: []
            }
          }
        }
      }
    },
    computed: {
      variable_keys() {
        return _.keys(_.get(this.def, 'variables', {}))
      },
      active_key() {
        return _.get(this.variable_keys, '['+this.active_idx+']', '')
      }
    },
    methods: {
      updateActiveItem(idx) {
        var item = _.get(this.def, this.active_key, {})
        this.active_item = _.assign({}, item)
      },
      goPrev() {
        this.active_idx = Math.max(this.active_idx - 1, 0)
      },
      goNext() {
        this.active_idx = Math.min(this.active_idx + 1, _.size(this.variable_keys) - 1)
      }
    }
  }
</script>
