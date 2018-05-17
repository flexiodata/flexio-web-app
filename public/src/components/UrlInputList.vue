<template>
  <div @click="focus">
    <table class="w-100">
      <tbody class="lh-copy">
        <tr
          class="darken-05 hide-child"
          v-for="(item, index) in items"
        >
          <td class="ph1 pv0 w-100 f6">{{item.path}}</td>
          <td class="ph1 pv0">
            <div
              class="pointer f3 lh-solid b child black-30 hover-black-60"
              @click="deleteUrl(index)"
            >
              &times;
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <input type="text"
      ref="input"
      class="input-reset border-box w-100 mh0 mv1 ph1 pv0 f6" placeholder="Enter or paste URLs here..." spellcheck="false"
      @keydown.esc="clearInput"
      @keyup.enter.space="addUrl"
      @input="onInputChange"
      v-model.trim="url_str"
    ></input>
  </div>
</template>

<script>

  export default {
    data() {
      return {
        items: [],
        url_str: ''
      }
    },
    methods: {
      getSelectedItems() {
        return this.items
      },
      fireSelectionChangeEvent() {
        this.$emit('selection-change', this.items)
      },
      finishEdit() {
        if (this.url_str.length > 0)
          this.addUrl()
      },
      focus() {
        this.$refs['input'].focus()
      },
      reset() {
        this.items = [].concat([])
      },
      clearInput() {
        this.url_str = ''
      },
      addUrl() {
        if (this.url_str.length == 0)
          return

        this.items.push({ path: this.url_str })
        this.url_str = ''
        this.$refs['input'].focus()

        this.fireSelectionChangeEvent()
      },
      deleteUrl(idx) {
        this.items = _.filter(this.items, function(item, item_idx) {
          return item_idx != idx
        })

        this.fireSelectionChangeEvent()
      },
      onInputChange() {
        if (_.includes(this.url_str, ' '))
        {
          var items = _
            .chain(this.url_str)
            .split(' ')
            .map((val) => { return { path: val } })
            .value()

          this.items = [].concat(this.items, items)
          this.url_str = ''

          this.fireSelectionChangeEvent()
        }
      }
    }
  }
</script>
