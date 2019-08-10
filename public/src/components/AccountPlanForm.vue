<template>
  <div>
    <div v-if="!is_editing">
      <div class="mb3 f7 silver ttu fw6">Your Current Plan</div>
      <div
        class="mv2 f6 br2 pv2 ph3 bg-nearer-white ba b--black-05"
        v-if="!hasPlan(current_plan_name)"
      >
        <div class="flex flex-row items-center justify-between">
          <div class="flex-fill ph2 pv2 f4 fw6">No plan has been selected</div>
          <div class="ph2 pv2">
            <el-button
              type="primary"
              class="ttu fw6"
              @click="is_editing = true"
            >
              Choose a plan
            </el-button>
          </div>
        </div>
      </div>
      <div
        class="mv2 f6 br2 pv2 ph3 bg-nearer-white ba b--black-05"
        v-else
      >
        <div class="flex flex-column flex-row-l items-center justify-between">
          <div class="ph2 pv2 f4 fw6 tc">{{current_plan['Name']}}</div>
          <div class="ph2 pv2 tc">
            <div>{{current_plan['Executions']}} executions</div>
            <!--
            <div class="mt1 o-40" style="font-size: 12px; line-height: 1.25">+ ${{current_plan['Extra Executions']}} per additional execution</div>
            -->
          </div>
          <div class="ph2 pv2">
            <span class="f1">${{current_plan['Price']}}</span><span class="f6">/mo</span>
          </div>
          <div class="ph2 pv2">
            <el-button
              type="primary"
              class="ttu fw6"
              @click="is_editing = true"
            >
              Change Plan
            </el-button>
          </div>
        </div>
      </div>
      <FreeTrialNotice class="mt2 f7 dark-green" />
    </div>
    <div v-else>
      <div class="mb3 f7 silver ttu fw6">Choose a plan</div>
      <div class="flex flex-column flex-row-l items-stretch justify-between nl2 nr2">
        <div
          class="flex-fill mh2 mb3 mb0-l ph3 tc br3 cursor-default"
          style="box-shadow: inset 0 -4px 12px rgba(0,0,0,0.075)"
          :class="isPlanNameSame(plan['Name'], current_plan_name) ? 'bg-blue white' : 'bg-nearer-white'"
          :key="plan['Name']"
          v-for="plan in plans"
        >
          <div class="mv4 fw6">{{plan['Name']}}</div>
          <div class="nt2 nb2">
            <span class="f1">${{plan['Price']}}</span><span class="f6">/mo</span>
          </div>
          <div class="mt4 mb3">
            <div>{{plan['Executions']}} executions</div>
            <!--
            <div class="mt1 o-40" style="font-size: 12px; line-height: 1.25">+ ${{plan['Extra Executions']}} per additional execution</div>
            -->
          </div>
          <div class="mv3 pt2 pb1">
            <div v-if="isPlanNameSame(plan['Name'], current_plan_name)">
              <i class="el-icon-success f2" style="color: #fff"></i>
            </div>
            <el-button
              type="primary"
              class="w-100 mw5 ttu fw6"
              @click="choosePlan(plan)"
              v-else-if="isPlanGreater(plan, current_plan)"
            >
              Upgrade now
            </el-button>
            <el-button
              plain
              class="w-100 ttu fw6"
              style="background-color: transparent; border-color: transparent"
              @click="choosePlan(plan)"
              v-else
            >
              Choose
            </el-button>
          </div>
        </div>
      </div>
      <div class="flex flex-row justify-end mt1">
        <el-button
          type="text"
          @click="is_editing = false"
        >
          I don't want to change my plan
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import plans from '@/data/usage-plans.yml'
  import FreeTrialNotice from '@/components/FreeTrialNotice'

  export default {
    components: {
      FreeTrialNotice
    },
    data() {
      var my_plans = _.filter(plans, (p) => { return p['Name'] != 'Enterprise' })

      return {
        is_editing: false,
        plans: my_plans,
        current_plan_name: ''
      }
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
      }),
      current_plan() {
        return _.find(this.plans, (p) => {
          return p['Name'].toLowerCase() == this.current_plan_name
        })
      }
    },
    created() {
      this.current_plan_name = _.get(this.getActiveUser(), 'usage_tier', '')
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      hasPlan(plan_name) {
        var plan_names = _.map(this.plans, (p) => {
          return p['Name'].toLowerCase()
        })

        return plan_names.indexOf(plan_name.toLowerCase()) != -1
      },
      isPlanNameSame(plan_name1, plan_name2) {
        return plan_name1.toLowerCase() == plan_name2.toLowerCase()
      },
      isPlanGreater(plan1, plan2) {
        if (plan1 && plan2) {
          return parseFloat(plan1['Price']) > parseFloat(plan2['Price'])
        } else {
          return true
        }
      },
      choosePlan(plan) {
        var new_plan_name = plan['Name'].toLowerCase()
        var eid = this.active_user_eid
        var attrs = {
          usage_tier: new_plan_name
        }

        this.$store.dispatch('users/update', { eid, attrs }).then(response => {
          this.current_plan_name = new_plan_name
          this.is_editing = false
        })
      }
    }
  }
</script>
