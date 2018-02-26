<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center" v-if="showHeader">
        <span class="flex-fill f4">{{our_title}}</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="close"></i>
      </div>
    </div>

    <div>
      <form novalidate @submit.prevent="submit">
        <div class="flex flex-column">
          <div class="flex-fill">
            <ui-textbox
              autocomplete="off"
              label="Name"
              floating-label
              help=" "
              required
              v-deferred-focus
              :error="errors.first('name')"
              :invalid="errors.has('name')"
              v-model="edit_pipe.name"
              v-validate
              data-vv-name="name"
              data-vv-value-path="edit_pipe.name"
              data-vv-rules="required"
            />
          </div>
          <div class="flex-fill flex flex-row items-center">
            <ui-textbox
              class="flex-fill"
              autocomplete="off"
              spellcheck="false"
              label="Alias"
              help=" "
              :placeholder="alias_placeholder"
              :error="ename_error"
              :invalid="ename_error.length > 0"
              v-model="edit_pipe.ename"
            />
            <div
              class="hint--bottom-left hint--large cursor-default"
              aria-label="Pipes can be referenced via an alias in the Flex.io command line interface (CLI), all SDKs as well as the REST API. Aliases are unique across the app, so we recommend prefixing your alias with your username (e.g., username-foo)."
            >
              <i class="material-icons blue md-24">info</i>
            </div>
          </div>
        </div>
        <ui-textbox
          label="Description"
          floating-label
          help=" "
          :multi-line="true"
          :rows="1"
          v-model="edit_pipe.description"
        />
      </form>
    </div>

    <div class="mt4 w-100 flex flex-row justify-end" v-if="showFooter">
      <el-button class="ttu b" type="plain" @click="close">Cancel</el-button>
      <el-button class="ttu b" type="primary" @click="submit" :disabled="has_errors">{{submit_label}}</el-button>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import * as mt from '../constants/member-type'
  import * as at from '../constants/action-type'
  import Validation from './mixins/validation'

  const defaultRights = () => {
    return {
      [mt.MEMBER_TYPE_OWNER]: {
        [at.ACTION_TYPE_READ]: true,
        [at.ACTION_TYPE_WRITE]: true,
        [at.ACTION_TYPE_EXECUTE]: true,
        [at.ACTION_TYPE_DELETE]: true
      },
      [mt.MEMBER_TYPE_MEMBER]: {
        [at.ACTION_TYPE_READ]: true,
        [at.ACTION_TYPE_WRITE]: true,
        [at.ACTION_TYPE_EXECUTE]: true,
        [at.ACTION_TYPE_DELETE]: false
      },
      [mt.MEMBER_TYPE_PUBLIC]: {
        [at.ACTION_TYPE_READ]: false,
        [at.ACTION_TYPE_WRITE]: false,
        [at.ACTION_TYPE_EXECUTE]: false,
        [at.ACTION_TYPE_DELETE]: false
      }
    }
  }

  const defaultAttrs = () => {
    return {
      eid: null,
      name: 'New Pipe',
      ename: '',
      description: '',
      rights: defaultRights()
    }
  }

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'show-header': {
        type: Boolean,
        default: true
      },
      'show-footer': {
        type: Boolean,
        default: true
      },
      'pipe': {
        type: Object,
        default: () => { return {} }
      },
      'mode': {
        type: String,
        default: 'add'
      }
    },
    mixins: [Validation],
    watch: {
      'pipe': function(val, old_val) {
        this.edit_pipe = _.cloneDeep(val)
        this.updatePipe(val)
      },
      'edit_pipe.ename': function(val, old_val) {
        var ename = val

        this.validateEname(ename, (response, errors) => {
          this.ss_errors = ename.length > 0 && _.size(errors) > 0
            ? _.assign({}, errors)
            : _.assign({})
        })
      }
    },
    data() {
      return {
        ss_errors: {},
        edit_pipe: _.assign({}, defaultAttrs(), this.pipe)
      }
    },
    computed: {
      eid() {
        return _.get(this, 'edit_pipe.eid', '')
      },
      our_title() {
        if (this.title.length > 0)
          return this.title

        return this.mode == 'edit'
          ? 'Edit "' + _.get(this.pipe, 'name') + '" Pipe'
          : 'New Pipe'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create pipe'
      },
      active_username() {
        return _.get(this.getActiveUser(), 'user_name', '')
      },
      alias_placeholder() {
        return 'username-my-alias'
      },
      ename_error() {
        if (this.mode == 'edit' && _.get(this.edit_pipe, 'ename') === _.get(this.pipe, 'ename'))
          return ''

        return _.get(this.ss_errors, 'ename.message', '')
      },
      has_client_errors() {
        var errors = _.get(this.errors, 'errors', [])
        return _.size(errors) > 0
      },
      has_errors() {
        return this.has_client_errors || this.ename_error.length > 0
      }
    },
    mounted() {
      if (this.mode != 'edit')
      {
        var ename = _.get(this.edit_pipe, 'ename', '')

        this.validateEname(ename, (response, errors) => {
          this.ss_errors = ename.length > 0 && _.size(errors) > 0
            ? _.assign({}, errors)
            : _.assign({})
        })
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      setPipeAttributes(attrs) {
        this.edit_pipe = _.assign({}, defaultAttrs(), attrs)
      },
      open(attrs) {
        this.reset(attrs)
        this.$emit('open')
      },
      close() {
        this.$emit('close')
      },
      submit() {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          var ename = _.get(this.edit_pipe, 'ename', '')

          this.validateEname(ename, (response, errors) => {
            this.ss_errors = ename.length > 0 && _.size(errors) > 0
              ? _.assign({}, errors)
              : _.assign({})

            if (this.ename_error.length == 0)
              this.$nextTick(() => { this.$emit('submit', this.edit_pipe) })
          })
        })
      },
      reset(attrs) {
        this.ss_errors = {}
        this.edit_pipe = _.assign({}, defaultAttrs(), attrs)
      },
      updatePipe(attrs) {
        this.edit_pipe = _.assign({}, this.edit_pipe, attrs)
      }
    }
  }
</script>
