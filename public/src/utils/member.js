import member_roles from '@/data/member-roles.yml'

// object_type = 'connection', 'member', etc.
// actions = 'read', 'write', etc. or 'rwd', 'r', etc.
export const isMemberAllowed = (member, object_type, actions) => {
  var member_role = _.get(member, 'role', '')
  var role = _.find(member_roles, r => r.type == member_role)
  var rights = _.get(role, 'rights', {})

  // `member` is not a member of this team; bail out
  if (!member) {
    return false
  }

  // `member` is the owner of this team; we're done
  if (member_role == 'O') {
    return true
  }

  // allow shorthand
  switch (actions) {
    case 'read':    actions = 'r'; break;
    case 'write':   actions = 'w'; break;
    case 'delete':  actions = 'd'; break;
    case 'execute': actions = 'x'; break;
  }

  // check actions against the role's UNIX-style permission string
  var actions_arr = actions.split('')
  var rights_arr = _.get(rights, object_type, '').split('')

  // we'll only return true if every action passed this function is in the rights array
  return actions_arr.every(val => rights_arr.indexOf(val) > -1);
}
