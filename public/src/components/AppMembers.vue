<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100 bg-nearer-white">
      <Spinner size="large" message="Loading members..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div>
      <div class="h3"></div>
      <div class="center mw-doc pa4 bg-white br2 css-white-box">
        <div class="flex flex-row items-start">
          <h3 class="flex-fill mt0 fw6 f3">Team Members</h3>
          <el-button
            class="ttu fw6"
            type="primary"
            @click="show_add_dialog = true"
          >
            Add Member
          </el-button>
        </div>
        <table class="el-table w-100">
          <thead>
            <tr>
              <th>Name</th>
            </tr>
          </thead>
          <tbody>
            <tr
              :key="member.eid"
              v-for="member in members"
            >
              <td>
                <div class="flex flex-row items-center">
                  <img :src="getGravatarUrl(member)" class="br-100"/>
                  <div class="ml2">
                    <div>{{member.first_name}} {{member.last_name}}</div>
                    <div class="light-silver" style="margin-top: 3px">{{member.email}}</div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <el-dialog
      width="46rem"
      top="4vh"
      title="Add Team Members"
      :modal-append-to-body="false"
      :visible.sync="show_add_dialog"
      @open="onAddDialogOpen"
      @clos="onAddDialogClose"
    >
      <el-form
        ref="form"
        class="el-form--cozy el-form__label-tiny"
        label-position="top"
        :model="add_dialog_model"
      >
        <p class="f5">Enter the email addresses of the people you would like to invite to your team. New team members will get an email with a link to accept the invitation.</p>
        <el-form-item
          key="users"
          prop="users"
          label="Send invites to the following email addresses"
        >
          <el-select
            ref="email-select"
            class="w-100"
            placeholder="Enter email addresses"
            multiple
            filterable
            allow-create
            default-first-option
            popper-class="dn"
            @keydown.native.188="addTag"
            v-model="add_dialog_model.users"
          >
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in add_dialog_model.options"
            />
          </el-select>
        </el-form-item>
      </el-form>
      <div class="mt4 w-100 flex flex-row justify-end">
        <el-button
          class="ttu fw6"
          @click="show_add_dialog = false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="onSubmit"
        >
          Send Invites
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'

  export default {
    metaInfo() {
      return {
        title: 'Members'
      }
    },
    components: {
      Spinner
    },
    data() {
      return {
        show_add_dialog: false,
        add_dialog_model: {
          users: [],
          options: []
        }
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'members_fetching',
        'is_fetched': 'members_fetched',
        'active_team_name': 'active_team_name'
      }),
      members() {
        return this.getAllMembers()
      }
    },
    created() {
      this.tryFetchMembers()
    },
    methods: {
      ...mapGetters([
        'getAllMembers'
      ]),
      getGravatarUrl(member) {
        return 'https://secure.gravatar.com/avatar/' + member.email_hash + '?d=mm&s=32'
      },
      tryFetchMembers() {
        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('v2_action_fetchMembers', { team_name: this.active_team_name }).catch(error => {
            // TODO: add error handling?
          })
        }
      },
      addTag() {
        var $select = this.$refs['email-select']
        var query = _.get($select, '$data.query', '')
        if (query.length > 0) {
          this.add_dialog_model.users = this.add_dialog_model.users.concat([query])
        }
        this.$nextTick(() => $select.$refs.input.focus())
      },
      onAddDialogOpen() {
        this.$nextTick(() => this.$refs['email-select'].focus())
      },
      onAddDialogClose() {
        this.add_dialog_model = {
          users: [],
          options: []
        }
      },
      onSubmit() {
        var timeout = 1

        _.forEach(this.add_dialog_model.users, user => {
          setTimeout(() => {
            var attrs = { member: user }

            this.$store.dispatch('v2_action_createMember', { team_name: this.active_team_name, attrs }).catch(error => {
              // TODO: add error handling?
            })
          }, timeout)

          timeout += 25
        })

        this.show_add_dialog = false
      }
    }
  }
</script>
