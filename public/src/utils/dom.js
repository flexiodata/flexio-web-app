
export const isHiddenInDOM = (el) => {
  return (el.offsetParent === null)
}

export const fallbackCss = (el_id, href) => {
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
