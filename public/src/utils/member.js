// actions = 'action.connection.read', 'action.teammember.update', etc.
export const isMemberAllowed = (member, actions) => {
  var rights = _.get(member, 'rights', [])
  return actions.every(val => rights.indexOf(val) > -1);
}
