<template>
  <div class="css-ios-toggle" @click.stop @dblclick.stop>
    <input :id="uid" :checked="checked" @click="click" @dblclick="dblclick" type="checkbox">
    <label :for="uid"></label>
  </div>
</template>

<script>
  export default {
    props: ['checked'],
    computed: {
      uid() {
        return _.uniqueId('toggle-')
      }
    },
    methods: {
      click: function(evt) {
        this.$emit('click', evt)
      },
      dblclick: function(evt) {
        this.$emit('dblclick', evt)
      }
    }
  }
</script>

<style lang="less">
  // iOS Checkbox Toggle Switch
  // ref. https://proto.io/freebies/onoff/

  @size: 1.5rem;
  @size-sm: 1.25rem;
  @size-lg: 2rem;

  @bw: 2px;

  @bg-color: #fff;
  @off-color: #ddd;
  @on-color: #009900;

  .css-ios-toggle {
    @s: @size;
    @w: 2.75em;

    box-sizing: border-box;
    position: relative;
    width: @w;
    user-select: none;

    input[type="checkbox"] {
      display: none;
    }

    label {
      display: block;
      overflow: hidden;
      cursor: pointer;
      padding: 0;
      height: @s;
      line-height: @s;
      border-radius: @s;
      border: @bw solid @off-color;
      background-color: @bg-color;
      transition: background-color 0.15s linear;
    }

    label:before {
      content: "";
      display: block;
      margin: 0px;
      background: @bg-color;
      position: absolute;
      top: 0;
      bottom: 0;
      width: @s;
      right: @w - @s;
      border-radius: @s;
      border: @bw solid @off-color;
      transition: all 0.15s linear;
    }

    input[type="checkbox"]:checked + label {
      background-color: @on-color;
    }

    input[type="checkbox"]:checked + label,
    input[type="checkbox"]:checked + label:before {
      border-color: @on-color;
    }

    input[type="checkbox"]:checked + label:before {
      right: 0;
      margin: 0;
    }
  }

  .css-ios-toggle.css-ios-toggle-sm {
    @s: @size-sm;
    @w: 2.25rem;

    width: @w;

    label {
      height: @s;
      line-height: @s;
      border-radius: @s;
    }

    label:before {
      width: @s;
      right: @w - @s;
      border-radius: @s;
    }
  }

  .css-ios-toggle.css-ios-toggle-lg {
    @s: @size-lg;
    @w: 3.75rem;

    width: @w;

    label {
      height: @s;
      line-height: @s;
      border-radius: @s;
    }

    label:before {
      width: @s;
      right: @w - @s;
      border-radius: @s;
    }
  }
</style>
