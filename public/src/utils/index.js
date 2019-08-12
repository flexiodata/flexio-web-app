
export const isNumber = val => {
  return !isNaN(parseFloat(val)) && isFinite(val)
}

export const afterNth = (str, char, cnt) => {
  if (!isNumber(cnt)) { cnt = 1 }
  var retval = str.substr(str.indexOf(char) + 1)
  return cnt <= 1 ? retval : afterNth(retval, char, cnt-1)
}

export const afterFirst = (str, char) => {
  return afterNth(str, char, 1)
}

export const pluralize = (cnt, many_str, one_str, zero_str) => {
  cnt = parseInt(''+cnt)
  if (cnt > 1)  return many_str
  if (cnt == 1) return one_str
  if (cnt == 0) return zero_str ? zero_str : many_str
  return ''
}

export const slugify = str => {
  str = str.replace(/\W/g, ' ')
  str = str.trim()
  str = str.replace(/\s+/g, '-')
  str = str.toLowerCase()
  return str
}

export const isValidEmail = str => {
  return /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(str)
}

export const getFullName = obj => {
  var fn = _.get(obj, 'first_name', '')
  var ln = _.get(obj, 'last_name', '')
  return ln.length > 0 ? `${fn} ${ln}` : `${fn}`
}

export const sanitizeMasked = obj => {
  return _.omitBy(obj, (val, key) => { return val === '*****' })
}

export const btoaUnicode = str => {
  return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
    return String.fromCharCode(parseInt(p1, 16))
  }))
}

export const atobUnicode = str => {
  return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
  }).join(''))
}

export const isProduction = () => {
  return window.location.hostname == 'www.flex.io' ? true : false
}
