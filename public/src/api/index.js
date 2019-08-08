import { AxiosResource } from './resources'

const GET = 'get'
const POS = 'post'
const PUT = 'put'
const DEL = 'delete'

export default {
  // PUBLIC API CALLS:

  // auth
  signOut:                 ()                              => AxiosResource(null)[POS] (`/logout`),
  signIn:                  (attrs)                         => AxiosResource(null)[POS] (`/login`, attrs),
  signUp:                  (attrs)                         => AxiosResource(null)[POS] (`/signup`, attrs),
  verifyAccount:           (attrs)                         => AxiosResource(null)[POS] (`/verification`, attrs),
  requestVerification:     (attrs)                         => AxiosResource(null)[POS] (`/requestverification`, attrs),
  forgotPassword:          (attrs)                         => AxiosResource(null)[POS] (`/forgotpassword`, attrs),
  resetPassword:           (attrs)                         => AxiosResource(null)[POS] (`/resetpassword`, attrs),

  // AUTHENTICATED API CALLS:

  // users
  fetchUser:               (team_name)                     => AxiosResource(team_name)[GET] (`/account`),
  updateUser:              (team_name, attrs)              => AxiosResource(team_name)[POS] (`/account`, attrs),
  deleteUser:              (team_name, attrs)              => AxiosResource(team_name)[DEL] (`/account`, attrs),
  changePassword:          (team_name, attrs)              => AxiosResource(team_name)[POS] (`/account/credentials`, attrs),

  // cards
  fetchCards:              (team_name)                     => AxiosResource(team_name)[GET] (`/account/cards`),
  createCard:              (team_name, attrs)              => AxiosResource(team_name)[POS] (`/account/cards`, attrs),
  deleteCard:              (team_name, object_name)        => AxiosResource(team_name)[DEL] (`/account/cards/${object_name}`),

  // tokens
  fetchTokens:             (team_name)                     => AxiosResource(team_name)[GET] (`/auth/keys`),
  createToken:             (team_name)                     => AxiosResource(team_name)[POS] (`/auth/keys`),
  deleteToken:             (team_name, object_name)        => AxiosResource(team_name)[DEL] (`/auth/keys/${object_name}`),

  // teams
  fetchTeams:              (team_name)                     => AxiosResource(team_name)[GET] (`/teams`),

  // pipes
  fetchPipes:              (team_name)                     => AxiosResource(team_name)[GET] (`/pipes`),
  fetchPipe:               (team_name, object_name)        => AxiosResource(team_name)[GET] (`/pipes/${object_name}`),
  createPipe:              (team_name, attrs)              => AxiosResource(team_name)[POS] (`/pipes`, attrs),
  updatePipe:              (team_name, object_name, attrs) => AxiosResource(team_name)[POS] (`/pipes/${object_name}`, attrs),
  deletePipe:              (team_name, object_name)        => AxiosResource(team_name)[DEL] (`/pipes/${object_name}`),

  // connections
  fetchConnections:        (team_name)                     => AxiosResource(team_name)[GET] (`/connections`),
  fetchConnection:         (team_name, object_name)        => AxiosResource(team_name)[GET] (`/connections/${object_name}`),
  createConnection:        (team_name, attrs)              => AxiosResource(team_name)[POS] (`/connections`, attrs),
  updateConnection:        (team_name, object_name, attrs) => AxiosResource(team_name)[POS] (`/connections/${object_name}`, attrs),
  deleteConnection:        (team_name, object_name)        => AxiosResource(team_name)[DEL] (`/connections/${object_name}`),
  testConnection:          (team_name, object_name, attrs) => AxiosResource(team_name)[POS] (`/connections/${object_name}/connect`, attrs),
  disconnectConnection:    (team_name, object_name, attrs) => AxiosResource(team_name)[POS] (`/connections/${object_name}/disconnect`, attrs) ,

  // members
  fetchMembers:            (team_name)                     => AxiosResource(team_name)[GET] (`/members`),
  fetchMember:             (team_name, object_name)        => AxiosResource(team_name)[GET] (`/members/${object_name}`),
  createMember:            (team_name, attrs)              => AxiosResource(team_name)[POS] (`/members`, attrs),
  updateMember:            (team_name, object_name, attrs) => AxiosResource(team_name)[POS] (`/members/${object_name}`, attrs),
  deleteMember:            (team_name, object_name)        => AxiosResource(team_name)[DEL] (`/members/${object_name}`),
  reinviteMember:          (team_name, object_name)        => AxiosResource(team_name)[POS] (`/members/${object_name}/invitations`),
  // TODO: which one of these is not like the other?
  joinTeam:                (team_name, attrs)              => AxiosResource(team_name)[POS] (`/members/join`, attrs),

  // teams
  fetchTeams:              (team_name)                     => AxiosResource(team_name)[GET] (`/teams`),
  fetchTeam:               (team_name, object_name)        => AxiosResource(team_name)[GET] (`/teams/${object_name}`),
  createTeam:              (team_name, attrs)              => AxiosResource(team_name)[POS] (`/teams`, attrs),
  updateTeam:              (team_name, object_name, attrs) => AxiosResource(team_name)[POS] (`/teams/${object_name}`, attrs),
  deleteTeam:              (team_name, object_name)        => AxiosResource(team_name)[DEL] (`/teams/${object_name}`),

  // processes
  fetchProcesses:          (team_name, attrs)              => AxiosResource(team_name)[GET] (`/processes`, attrs),
  fetchProcess:            (team_name, object_name)        => AxiosResource(team_name)[GET] (`/processes/${object_name}`),
  createProcess:           (team_name, attrs)              => AxiosResource(team_name)[POS] (`/processes`, attrs),
  cancelProcess:           (team_name, object_name)        => AxiosResource(team_name)[POS] (`/processes/${object_name}/cancel`),
  runProcess:              (team_name, object_name, cfg)   => AxiosResource(team_name)[POS] (`/processes/${object_name}/run`, cfg),

  // streams
  fetchStream:             (team_name, object_name)        => AxiosResource(team_name)[GET] (`/streams/${object_name}`),

  // vfs
  vfsListFiles:            (team_name, path)               => AxiosResource(team_name)[GET] (`/vfs/list`, { q: path }),

  // validation
  validate:                (team_name, attrs)              => AxiosResource(team_name)[POS] (`/validate`, attrs),

  // INTERNAL API CALLS:

  // admin
  fetchAdminUserProcesses: (team_name, attrs)              => AxiosResource('admin')[GET] (`/info/processes/summary/user`, attrs),
  fetchAdminProcesses:     (team_name, attrs)              => AxiosResource('admin')[GET] (`/info/processes`, attrs),
  fetchAdminTests:         (team_name)                     => AxiosResource('admin')[GET] (`/tests/configure`),
  runAdminTest:            (team_name, attrs)              => AxiosResource('admin')[GET] (`/tests/run`, attrs),
}

/*
  VFS Notes...

  1) List Directory      GET https://localhost/api/me/v2/vfs/list?q=/
  2) Get File            GET https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  3) Put File            PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  4) Create Directory    PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder ?????
  5) Delete Files        DEL https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
*/
