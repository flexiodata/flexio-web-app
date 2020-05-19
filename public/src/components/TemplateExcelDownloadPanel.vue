<template>
  <div>
    <div class="center mw7 mt5-l">
      <div class="h1"></div>
      <div class="flex flex-row items-center">
        <img src="../assets/icon/icon-excel-128.png" alt="Microsoft Excel" style="height: 48px" />
        <div class="ml3 fw6 f4">Microsoft Excel 365</div>
      </div>
      <div class="h2"></div>
      <div class="flex flex-row">
        <div class="flex-fill">
          <h2 class="fw4 f2">Download spreadsheet</h2>
          <p>Would you like to download <strong>{{title}}</strong>?</p>
          <div class="mt4">
            <a
              class="link dib tc border-box no-select ttu fw6 ph4 pv2a lh-title white bg-blue br2 darken-10"
              :href="url"
            >
              Download
            </a>
          </div>
        </div>
        <div class="tc">
          <i class="material-icons moon-gray" style="font-size: 9rem">cloud_download</i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      title: {
        type: String,
        required: true
      },
      url: {
        type: String,
        required: true
      }
    }
  }
</script>
