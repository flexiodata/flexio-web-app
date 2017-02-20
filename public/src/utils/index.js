
var pluralize = (cnt, many_str, one_str, zero_str) => {
  cnt = parseInt(''+cnt);
  if (cnt > 1)  return many_str;
  if (cnt == 1) return one_str;
  if (cnt == 0) return zero_str ? zero_str : many_str;
  return '';
}

export default {
  pluralize: pluralize
}
