
const isNumber = (value) => {
  return !isNaN(parseFloat(value)) && isFinite(value)
}

const isHiddenInDOM = (el) => {
  return (el.offsetParent === null)
}

const pluralize = (cnt, many_str, one_str, zero_str) => {
  cnt = parseInt(''+cnt)
  if (cnt > 1)  return many_str
  if (cnt == 1) return one_str
  if (cnt == 0) return zero_str ? zero_str : many_str
  return ''
}

const slugify = (str) => {
  str = str.replace(/\W/g, ' ')
  str = str.trim()
  str = str.replace(/\s+/g, '-')
  str = str.toLowerCase()
  return str
}

const afterNth = (str, char, cnt) => {
  if (!isNumber(cnt)) { cnt = 1 }
  var retval = str.substr(str.indexOf('/') + 1)
  return cnt <= 1 ? retval : afterNth(retval, char, cnt-1)
}

const afterFirst = (str, char) => {
  return afterNth(str, char, 1)
}

const fallbackCss = (el_id, href) => {
  var tmp_el = document.createElement('div')
  tmp_el.className = 'dn' // use Tachyons as our test case (if it's not loaded, it's likely nothing else is either)

  // append test element to document body
  document.body.appendChild(tmp_el)

  if (tmp_el && !isHiddenInDOM(tmp_el)) {
    var head_el = document.head || document.getElementsByTagName('head')[0]

    // create link tag
    var link_el = document.createElement('link')
    link_el.type = 'text/css'
    link_el.rel = 'stylesheet'

    // add link tag to the <head>
    head_el.appendChild(link_el)
    link_el.href = href
  }

  document.body.removeChild(tmp_el)
}

const sanitizeMasked = (obj) => {
  return _.omitBy(obj, (val, key) => { return val === '*****' })
}

const btoaUnicode = (str) => {
  return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
    return String.fromCharCode(parseInt(p1, 16))
  }))
}

const atobUnicode = (str) => {
  return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
  }).join(''))
}

export default {
  isNumber,
  isHiddenInDOM,
  pluralize,
  slugify,
  afterNth,
  afterFirst,
  fallbackCss,
  sanitizeMasked,
  btoaUnicode,
  atobUnicode
}
