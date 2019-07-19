import { AxiosResource } from './resources'

const V2_GET = 'get'
const V2_POS = 'post'
const V2_PUT = 'put'
const V2_DEL = 'delete'

export default {
  // users
  v2_fetchUser:            function(team_name)                     { return AxiosResource(team_name)[V2_GET] (`/account`)                                      },
  v2_updateUser:           function(team_name, attrs)              { return AxiosResource(team_name)[V2_POS] (`/account`, attrs)                               },
  v2_deleteUser:           function(team_name, attrs)              { return AxiosResource(team_name)[V2_DEL] (`/account`, attrs)                               },
  v2_changePassword:       function(team_name, attrs)              { return AxiosResource(team_name)[V2_POS] (`/account/credentials`, attrs)                   },

  // cards
  v2_fetchCards:           function(team_name)                     { return AxiosResource(team_name)[V2_GET] (`/account/cards`)                                },
  v2_createCard:           function(team_name, attrs)              { return AxiosResource(team_name)[V2_POS] (`/account/cards`, attrs)                         },
  v2_deleteCard:           function(team_name, object_name)        { return AxiosResource(team_name)[V2_DEL] (`/account/cards/${object_name}`)                 },

  // tokens
  v2_fetchTokens:          function(team_name)                     { return AxiosResource(team_name)[V2_GET] (`/auth/keys`)                                    },
  v2_createToken:          function(team_name)                     { return AxiosResource(team_name)[V2_POS] (`/auth/keys`)                                    },
  v2_deleteToken:          function(team_name, object_name)        { return AxiosResource(team_name)[V2_DEL] (`/auth/keys/${object_name}`)                     },

  // connections
  v2_fetchConnections:     function(team_name)                     { return AxiosResource(team_name)[V2_GET] (`/connections`)                                  },
  v2_fetchConnection:      function(team_name, object_name)        { return AxiosResource(team_name)[V2_GET] (`/connections/${object_name}`)                   },
  v2_createConnection:     function(team_name, attrs)              { return AxiosResource(team_name)[V2_POS] (`/connections`, attrs)                           },
  v2_updateConnection:     function(team_name, object_name, attrs) { return AxiosResource(team_name)[V2_POS] (`/connections/${object_name}`, attrs)            },
  v2_deleteConnection:     function(team_name, object_name)        { return AxiosResource(team_name)[V2_DEL] (`/connections/${object_name}`)                   },
  v2_testConnection:       function(team_name, object_name, attrs) { return AxiosResource(team_name)[V2_POS] (`/connections/${object_name}/connect`, attrs)    },
  v2_disconnectConnection: function(team_name, object_name, attrs) { return AxiosResource(team_name)[V2_POS] (`/connections/${object_name}/disconnect`, attrs) },

  // members
  v2_fetchMembers:         function(team_name)                     { return AxiosResource(team_name)[V2_GET] (`/members`)                                      },
  v2_fetchMember:          function(team_name, object_name)        { return AxiosResource(team_name)[V2_GET] (`/members/${object_name}`)                       },
  v2_createMember:         function(team_name, attrs)              { return AxiosResource(team_name)[V2_POS] (`/members`, attrs)                               },
  v2_updateMember:         function(team_name, object_name, attrs) { return AxiosResource(team_name)[V2_POS] (`/members/${object_name}`, attrs)                },
  v2_deleteMember:         function(team_name, object_name)        { return AxiosResource(team_name)[V2_DEL] (`/members/${object_name}`)                       },

  // teams
  v2_fetchTeams:           function(team_name)                     { return AxiosResource(team_name)[V2_GET] (`/teams`)                                        },

  // pipes
  v2_fetchPipes:           function(team_name)                     { return AxiosResource(team_name)[V2_GET] (`/pipes`)                                        },
  v2_fetchPipe:            function(team_name, object_name)        { return AxiosResource(team_name)[V2_GET] (`/pipes/${object_name}`)                         },
  v2_createPipe:           function(team_name, attrs)              { return AxiosResource(team_name)[V2_POS] (`/pipes`, attrs)                                 },
  v2_updatePipe:           function(team_name, object_name, attrs) { return AxiosResource(team_name)[V2_POS] (`/pipes/${object_name}`, attrs)                  },
  v2_deletePipe:           function(team_name, object_name)        { return AxiosResource(team_name)[V2_DEL] (`/pipes/${object_name}`)                         },

  // processes
  v2_fetchProcesses:       function(team_name, attrs)              { return AxiosResource(team_name)[V2_GET] (`/processes`, attrs)                             },
  v2_fetchProcess:         function(team_name, object_name)        { return AxiosResource(team_name)[V2_GET] (`/processes/${object_name}`)                     },
  v2_createProcess:        function(team_name, attrs)              { return AxiosResource(team_name)[V2_POS] (`/processes`, attrs)                             },
  v2_cancelProcess:        function(team_name, object_name)        { return AxiosResource(team_name)[V2_POS] (`/processes/${object_name}/cancel`)              },
  v2_runProcess:           function(team_name, object_name, cfg)   { return AxiosResource(team_name)[V2_POS] (`/processes/${object_name}/run`, {}, cfg)        },
  v2_fetchProcessLog:      function(team_name, object_name)        { return AxiosResource(team_name)[V2_GET] (`/processes/${object_name}/log`)                 },
  v2_fetchProcessSummary:  function(team_name)                     { return AxiosResource(team_name)[V2_GET] (`/processes/summary`)                            },

  // streams
  v2_fetchStream:          function(team_name, object_name)        { return AxiosResource(team_name)[V2_GET] (`/streams/${object_name}`)                       },

  // vfs
  v2_vfsListFiles:         function(team_name, path)               { return AxiosResource(team_name)[V2_GET] (`/vfs/list`, { q: path })                        },

  // validation
  v2_validate:             function(team_name, attrs)              { return AxiosResource(team_name)[V2_POS] (`/validate`, attrs)                              },

  // auth
  v2_logout:               function()                              { return AxiosResource(null)[V2_POS] (`/logout`)                                            },
  v2_resetPassword:        function(attrs)                         { return AxiosResource(null)[V2_POS] (`/resetpassword`, attrs)                              },

  // admin
  v2_fetchAdminProcesses:  function(attrs)                         { return AxiosResource('admin')[V2_GET] (`/info/processes`, attrs)                          },
  v2_fetchAdminTests:      function()                              { return AxiosResource('admin')[V2_GET] (`/tests/configure`)                                },
  v2_runAdminTest:         function(attrs)                         { return AxiosResource('admin')[V2_GET] (`/tests/run`, attrs)                               },
}

/*
  VFS Notes...

  1) List Directory      GET https://localhost/api/me/v2/vfs/list?q=/
  2) Get File            GET https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  3) Put File            PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  4) Create Directory    PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder ?????
  5) Delete Files        DEL https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
*/
