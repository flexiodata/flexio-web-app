<template>
  <div>
    <div v-if="!is_editing_plan">
      <div class="mb3 f7 silver ttu fw6">Your Current Plan</div>
      <div class="blankslate" v-if="!hasPlan(current_plan_id)">
        <em>No plan has been selected</em>
        <div class="mt3">
          <el-button
            type="primary"
            class="ttu fw6"
            @click="is_editing_plan = true"
          >
            Choose a plan
          </el-button>
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
          </div>
          <div class="ph2 pv2">
            <span class="f1">${{current_plan['Price']}}</span><span class="f6">/user/mo</span>
          </div>
          <div class="ph2 pv2">
            <el-button
              type="primary"
              class="ttu fw6"
              @click="is_editing_plan = true"
            >
              Change Plan
            </el-button>
          </div>
        </div>
      </div>
      <div class="tr">
        <FreeTrialNotice class="mt2 f7 dark-green" />
      </div>
      <div class="mt4 mb3 f7 silver ttu fw6">Number of seats</div>
      <p class="f6">You have <strong>{{plan_info.seat_cnt}} seat(s)</strong> on your current plan.</p>
      <el-form
        class="mt3 el-form--cozy el-form__label-tiny"
        :model="$data"
        @submit.prevent.native
        v-if="is_editing_seats"
      >
        <el-form-item
          key="seat_options"
          prop="seat_options"
        >
          <el-select
            placeholder="Choose the number of seats"
            v-model="plan_info.seat_cnt"
          >
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in seat_options"
            />
          </el-select>
        </el-form-item>
        <div class="dib">
          <ButtonBar
            @cancel-click="is_editing_seats = false"
            @submit-click="updateSeats"
          />
        </div>
      </el-form>
      <div class="mt3" v-else>
        <el-button
          type="primary"
          class="ttu fw6"
          @click="is_editing_seats = true"
        >
          Manage seats
        </el-button>
      </div>
    </div>
    <div v-else>
      <div class="mb3 f7 silver ttu fw6">Choose a plan</div>
      <div class="flex flex-column flex-row-l items-stretch justify-between nl2 nr2">
        <div
          class="flex-fill mh2 mb3 mb0-l ph3 tc br3 cursor-default"
          style="box-shadow: inset 0 -4px 12px rgba(0,0,0,0.075)"
          :class="isCurrentPlan(plan, current_plan_id) ? 'bg-blue white' : 'bg-nearer-white'"
          :key="plan['id']"
          v-for="plan in plans"
        >
          <div class="mv4 fw6">{{plan['Name']}}</div>
          <div class="mv4">
            <span class="f1">${{plan['Price']}}</span><span class="f6">/user/mo</span>
          </div>
          <div class="mv4 mb3">
            <div>{{plan['Executions']}} executions</div>
            <div class="mt2">{{plan['Members']}} </div>
            <div class="mt2">{{plan['Teams']}} </div>
          </div>
          <div class="mv3 pt2 pb1">
            <div v-if="isCurrentPlan(plan, current_plan_id)">
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
          @click="is_editing_plan = false"
        >
          I don't want to change my plan
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '@/api'
  import plans from '@/data/usage-plans.yml'
  import { isProduction } from '@/utils'
  import FreeTrialNotice from '@/components/FreeTrialNotice'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultPlanInfo = () => {
    return {
      subscription_id: '',
      subscription_item_id: '',
      plan_id: '',
      seat_cnt: 1
    }
  }

  const getDefaultState = () => {
    var my_plans = _.filter(plans, (p) => { return p['id'] != 'enterprise' })

    return {
      is_fetching: false,
      is_editing_plan: false,
      is_editing_seats: false,
      seat_options,
      plans: my_plans,
      plan_info: getDefaultPlanInfo(),
      plan_error: '',
    }
  }

  var seat_options = []
  for (var i = 1; i < 100; ++i) {
    seat_options.push({ label: '' + i, val: i })
  }

  export default {
    components: {
      FreeTrialNotice,
      ButtonBar
    },
    data() {
      return getDefaultState()
    },
    computed: {
      current_plan_id() {
        return _.get(this.plan_info, 'plan_id', '')
      },
      current_plan() {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan = _.find(this.plans, (p) => {
          return p[plan_key] == this.current_plan_id
        })

        return _.defaultTo(plan, {})
      }
    },
    mounted() {
      this.fetchPlan()
    },
    methods: {
      fetchPlan() {
        this.is_fetching = true

        api.fetchPlan().then(response => {
          this.plan_info = _.assign({}, response.data)
          this.plan_error = ''
        }).catch(error => {
          this.plan_info = {}
          this.plan_error = JSON.stringify(error)
        }).finally(() => {
          this.is_fetching = false
        })
      },
      hasPlan(plan_id) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan_ids = _.map(this.plans, p => p[plan_key])

        return plan_ids.indexOf(plan_id) != -1
      },
      isCurrentPlan(plan, plan_id) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        return plan[plan_key] == plan_id
      },
      isPlanGreater(plan1, plan2) {
        if (plan1 && plan2) {
          return parseFloat(plan1['Price']) > parseFloat(plan2['Price'])
        } else {
          return true
        }
      },
      choosePlan(plan) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan_id = plan[plan_key]
        this.plan_info = _.assign({}, this.plan_info, { plan_id })

        var payload = _.omit(this.plan_info, ['subscription_id'])

        api.updatePlan('me', payload).then(response => {
          this.plan_info = _.assign({}, response.data)
          this.is_editing_plan = false
        })
      },
      updateSeats() {
        var payload = _.omit(this.plan_info, ['subscription_id'])

        api.updatePlan('me', payload).then(response => {
          this.plan_info = _.assign({}, response.data)
          this.is_editing_seats = false
        })
      }
    }
  }
</script>
