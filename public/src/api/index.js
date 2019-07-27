import { AxiosResource } from './resources'

const GET = 'get'
const POS = 'post'
const PUT = 'put'
const DEL = 'delete'

export default {
  // PUBLIC API CALLS:

  // auth
  v2_logout:               function()                              { return AxiosResource(null)[POS] (`/logout`)                                            },
  v2_resetPassword:        function(attrs)                         { return AxiosResource(null)[POS] (`/resetpassword`, attrs)                              },

  // AUTHENTICATED API CALLS:

  // users
  v2_fetchUser:            function(team_name)                     { return AxiosResource(team_name)[GET] (`/account`)                                      },
  v2_updateUser:           function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/account`, attrs)                               },
  v2_deleteUser:           function(team_name, attrs)              { return AxiosResource(team_name)[DEL] (`/account`, attrs)                               },
  v2_changePassword:       function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/account/credentials`, attrs)                   },

  // cards
  v2_fetchCards:           function(team_name)                     { return AxiosResource(team_name)[GET] (`/account/cards`)                                },
  v2_createCard:           function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/account/cards`, attrs)                         },
  v2_deleteCard:           function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/account/cards/${object_name}`)                 },

  // tokens
  v2_fetchTokens:          function(team_name)                     { return AxiosResource(team_name)[GET] (`/auth/keys`)                                    },
  v2_createToken:          function(team_name)                     { return AxiosResource(team_name)[POS] (`/auth/keys`)                                    },
  v2_deleteToken:          function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/auth/keys/${object_name}`)                     },

  // teams
  v2_fetchTeams:           function(team_name)                     { return AxiosResource(team_name)[GET] (`/teams`)                                        },

  // pipes
  v2_fetchPipes:           function(team_name)                     { return AxiosResource(team_name)[GET] (`/pipes`)                                        },
  v2_fetchPipe:            function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/pipes/${object_name}`)                         },
  v2_createPipe:           function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/pipes`, attrs)                                 },
  v2_updatePipe:           function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/pipes/${object_name}`, attrs)                  },
  v2_deletePipe:           function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/pipes/${object_name}`)                         },

  // connections
  v2_fetchConnections:     function(team_name)                     { return AxiosResource(team_name)[GET] (`/connections`)                                  },
  v2_fetchConnection:      function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/connections/${object_name}`)                   },
  v2_createConnection:     function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/connections`, attrs)                           },
  v2_updateConnection:     function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/connections/${object_name}`, attrs)            },
  v2_deleteConnection:     function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/connections/${object_name}`)                   },
  v2_testConnection:       function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/connections/${object_name}/connect`, attrs)    },
  v2_disconnectConnection: function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/connections/${object_name}/disconnect`, attrs) },

  // members
  v2_fetchMembers:         function(team_name)                     { return AxiosResource(team_name)[GET] (`/members`)                                      },
  v2_fetchMember:          function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/members/${object_name}`)                       },
  v2_createMember:         function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/members`, attrs)                               },
  v2_updateMember:         function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/members/${object_name}`, attrs)                },
  v2_deleteMember:         function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/members/${object_name}`)                       },
  v2_reinviteMember:       function(team_name, object_name)        { return AxiosResource(team_name)[POS] (`/members/${object_name}/invitations`)           },

  // teams
  v2_fetchTeams:           function(team_name)                     { return AxiosResource(team_name)[GET] (`/teams`)                                        },
  v2_fetchTeam:            function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/teams/${object_name}`)                         },
  v2_createTeam:           function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/teams`, attrs)                                 },
  v2_updateTeam:           function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/teams/${object_name}`, attrs)                  },
  v2_deleteTeam:           function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/teams/${object_name}`)                         },

  // processes
  v2_fetchProcesses:       function(team_name, attrs)              { return AxiosResource(team_name)[GET] (`/processes`, attrs)                             },
  v2_fetchProcess:         function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/processes/${object_name}`)                     },
  v2_createProcess:        function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/processes`, attrs)                             },
  v2_cancelProcess:        function(team_name, object_name)        { return AxiosResource(team_name)[POS] (`/processes/${object_name}/cancel`)              },
  v2_runProcess:           function(team_name, object_name, cfg)   { return AxiosResource(team_name)[POS] (`/processes/${object_name}/run`, {}, cfg)        },
  v2_fetchProcessLog:      function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/processes/${object_name}/log`)                 },
  v2_fetchProcessSummary:  function(team_name)                     { return AxiosResource(team_name)[GET] (`/processes/summary`)                            },

  // streams
  v2_fetchStream:          function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/streams/${object_name}`)                       },

  // vfs
  v2_vfsListFiles:         function(team_name, path)               { return AxiosResource(team_name)[GET] (`/vfs/list`, { q: path })                        },

  // validation
  v2_validate:             function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/validate`, attrs)                              },

  // INTERNAL API CALLS:

  // admin
  v2_fetchAdminProcesses:  function(team_name, attrs)              { return AxiosResource('admin')[GET] (`/info/processes`, attrs)                          },
  v2_fetchAdminTests:      function(team_name)                     { return AxiosResource('admin')[GET] (`/tests/configure`)                                },
  v2_runAdminTest:         function(team_name, attrs)              { return AxiosResource('admin')[GET] (`/tests/run`, attrs)                               },
}

/*
  VFS Notes...

  1) List Directory      GET https://localhost/api/me/v2/vfs/list?q=/
  2) Get File            GET https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  3) Put File            PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  4) Create Directory    PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder ?????
  5) Delete Files        DEL https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
*/
