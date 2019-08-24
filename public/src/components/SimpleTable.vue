<template>
  <div style="border: 1px solid #dcdfe6">
    <table>
      <thead v-show="columns.length > 0">
        <tr>
          <th
            :key="col.name"
            v-for="col in columns"
          >
            {{col.name}}
          </th>
        </tr>
      </thead>
      <tbody v-show="rows.length > 0">
        <tr
          :key="row_idx"
          v-for="(row, row_idx) in rows"
        >
          <td
            :key="key"
            v-for="(val, key) in row"
          >{{val}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
  export default {
    props: {
      columns: {
        type: Array,
        default: () => []
      },
      rows: {
        type: Array,
        default: () => []
      }
    }
  }
</script>

<style lang="stylus" scoped>
  table
    border-collapse: collapse
    border-spacing: 0
    position: relative
    left: -1px
    top: -1px
    right: -1px
    bottom: -1px

  th,
  td
    border: 1px solid rgba(#dcdfe6, 0.55)
    padding: 4px 8px
    font-size: 13px
    white-space: nowrap

  th
    background-color: lighten(#dcdfe6, 65%)
</style>
