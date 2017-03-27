
var mirror_el = null
var char_el = null

// styles that could influence size of the mirrored element
var mirror_styles = [
  // box styles
  'box-sizing',
  'height',
  'width',
  'padding-bottom',
  'padding-left',
  'padding-right',
  'padding-top',

   // font styles
  'font-family',
  'font-size',
  'font-style' ,
  'font-variant',
  'font-weight',

  // spacing styles
  'word-spacing',
  'letter-spacing',
  'line-height',
  'text-decoration',
  'text-indent',
  'text-transform',

  // text direction
  'direction'
]

var clearMirrorEl = function()
{
  if (mirror_el === null)
    return

  while (mirror_el.firstChild) {
    mirror_el.removeChild(mirror_el.firstChild)
  }

  char_el = null
}

var copyStyles = function(el)
{
  var el_styles = window.getComputedStyle(el, '')

  for (var i = 0, style; style = mirror_styles[i]; i++) {
    mirror_el.style[style] = el_styles[style]
  }
}

// XBrowser caret position
// Adapted from http://stackoverflow.com/questions/263743/how-to-get-caret-position-in-textarea
var getTextareaCaretIndex = function(el)
{
  if (el.selectionStart)
    return el.selectionStart

  if (document.selection)
  {
    el.focus()

    var r = document.selection.createRange()
    if (r == null)
      return 0

    var re = this.el.createTextRange()
    var rc = re.duplicate()
    re.moveToBookmark(r.getBookmark())
    rc.setEndPoint('EndToStart', re)
    return rc.text.length
  }

  return 0
}

var updateMeasureElement = function(el, char_idx)
{
  // copy styles
  copyStyles(el)

  // empty out mirror element's contents
  clearMirrorEl()

  // update content and insert caret
  if (char_idx === undefined)
    char_idx = getTextareaCaretIndex(el)

  var str = el.value
  var pre = document.createTextNode(str.substring(0, char_idx))
  var post = document.createTextNode(str.substring(char_idx))
  char_el = document.createElement('span')

  char_el.style.cssText = 'position: absolute'
  char_el.innerHTML = '&nbsp;'

  mirror_el.appendChild(pre)
  mirror_el.appendChild(char_el)
  mirror_el.appendChild(post)

  // match vertical scroll position
  mirror_el.scrollTop = el.scrollTop
}

export default {
  init: function(el) {
    if (el.nodeName.toLowerCase() !== 'textarea')
      return

    this.el = el

    mirror_el = document.createElement('div')
    mirror_el.setAttribute('style', '' +
      'position: absolute; ' +
      'overflow: auto; ' +
      'white-space: pre-wrap; ' +
      'word-wrap: break-word; ' +
      'top: 0; ' +
      'left: 0;' +
      'left: -9999px;'
    )

    document.body.appendChild(mirror_el)
  },

  destroy: function() {
    clearMirrorEl()

    // this check here doesn't really solve the issue where the parent node
    // of the mirror elements after the first one are null
    if (mirror_el.parentNode)
      document.body.removeChild(mirror_el)
  },

  getCharCoordinates: function(char_idx) {
    updateMeasureElement(this.el, char_idx)

    // TODO: handle RTL
    return {
      left: Math.floor(char_el.offsetLeft),
      top: Math.floor(char_el.offsetTop)
    }
  },

  getCaretCoordinates: function() {
    return this.getCharCoordinates()
  },

  getCaretIndex: function() {
    return getTextareaCaretIndex(this.el)
  },

  getComputedHeight: function() {
    updateMeasureElement(this.el)

    var tmp = document.createTextNode('H')
    mirror_el.appendChild(tmp)
    mirror_el.style.height = ''

    var h = mirror_el.offsetHeight
    mirror_el.removeChild(tmp)
    return h
  }
}
