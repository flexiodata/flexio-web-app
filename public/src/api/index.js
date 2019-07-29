import { AxiosResource } from './resources'

const GET = 'get'
const POS = 'post'
const PUT = 'put'
const DEL = 'delete'

export default {
  // PUBLIC API CALLS:

  // auth
  logout:               function()                              { return AxiosResource(null)[POS] (`/logout`)                                            },
  resetPassword:        function(attrs)                         { return AxiosResource(null)[POS] (`/resetpassword`, attrs)                              },

  // AUTHENTICATED API CALLS:

  // users
  fetchUser:            function(team_name)                     { return AxiosResource(team_name)[GET] (`/account`)                                      },
  updateUser:           function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/account`, attrs)                               },
  deleteUser:           function(team_name, attrs)              { return AxiosResource(team_name)[DEL] (`/account`, attrs)                               },
  changePassword:       function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/account/credentials`, attrs)                   },

  // cards
  fetchCards:           function(team_name)                     { return AxiosResource(team_name)[GET] (`/account/cards`)                                },
  createCard:           function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/account/cards`, attrs)                         },
  deleteCard:           function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/account/cards/${object_name}`)                 },

  // tokens
  fetchTokens:          function(team_name)                     { return AxiosResource(team_name)[GET] (`/auth/keys`)                                    },
  createToken:          function(team_name)                     { return AxiosResource(team_name)[POS] (`/auth/keys`)                                    },
  deleteToken:          function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/auth/keys/${object_name}`)                     },

  // teams
  fetchTeams:           function(team_name)                     { return AxiosResource(team_name)[GET] (`/teams`)                                        },

  // pipes
  fetchPipes:           function(team_name)                     { return AxiosResource(team_name)[GET] (`/pipes`)                                        },
  fetchPipe:            function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/pipes/${object_name}`)                         },
  createPipe:           function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/pipes`, attrs)                                 },
  updatePipe:           function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/pipes/${object_name}`, attrs)                  },
  deletePipe:           function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/pipes/${object_name}`)                         },

  // connections
  fetchConnections:     function(team_name)                     { return AxiosResource(team_name)[GET] (`/connections`)                                  },
  fetchConnection:      function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/connections/${object_name}`)                   },
  createConnection:     function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/connections`, attrs)                           },
  updateConnection:     function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/connections/${object_name}`, attrs)            },
  deleteConnection:     function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/connections/${object_name}`)                   },
  testConnection:       function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/connections/${object_name}/connect`, attrs)    },
  disconnectConnection: function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/connections/${object_name}/disconnect`, attrs) },

  // members
  fetchMembers:         function(team_name)                     { return AxiosResource(team_name)[GET] (`/members`)                                      },
  fetchMember:          function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/members/${object_name}`)                       },
  createMember:         function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/members`, attrs)                               },
  updateMember:         function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/members/${object_name}`, attrs)                },
  deleteMember:         function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/members/${object_name}`)                       },
  reinviteMember:       function(team_name, object_name)        { return AxiosResource(team_name)[POS] (`/members/${object_name}/invitations`)           },

  // teams
  fetchTeams:           function(team_name)                     { return AxiosResource(team_name)[GET] (`/teams`)                                        },
  fetchTeam:            function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/teams/${object_name}`)                         },
  createTeam:           function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/teams`, attrs)                                 },
  updateTeam:           function(team_name, object_name, attrs) { return AxiosResource(team_name)[POS] (`/teams/${object_name}`, attrs)                  },
  deleteTeam:           function(team_name, object_name)        { return AxiosResource(team_name)[DEL] (`/teams/${object_name}`)                         },

  // processes
  fetchProcesses:       function(team_name, attrs)              { return AxiosResource(team_name)[GET] (`/processes`, attrs)                             },
  fetchProcess:         function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/processes/${object_name}`)                     },
  createProcess:        function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/processes`, attrs)                             },
  cancelProcess:        function(team_name, object_name)        { return AxiosResource(team_name)[POS] (`/processes/${object_name}/cancel`)              },
  runProcess:           function(team_name, object_name, cfg)   { return AxiosResource(team_name)[POS] (`/processes/${object_name}/run`, {}, cfg)        },
  fetchProcessLog:      function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/processes/${object_name}/log`)                 },
  fetchProcessSummary:  function(team_name)                     { return AxiosResource(team_name)[GET] (`/processes/summary`)                            },

  // streams
  fetchStream:          function(team_name, object_name)        { return AxiosResource(team_name)[GET] (`/streams/${object_name}`)                       },

  // vfs
  vfsListFiles:         function(team_name, path)               { return AxiosResource(team_name)[GET] (`/vfs/list`, { q: path })                        },

  // validation
  validate:             function(team_name, attrs)              { return AxiosResource(team_name)[POS] (`/validate`, attrs)                              },

  // INTERNAL API CALLS:

  // admin
  fetchAdminProcesses:  function(team_name, attrs)              { return AxiosResource('admin')[GET] (`/info/processes`, attrs)                          },
  fetchAdminTests:      function(team_name)                     { return AxiosResource('admin')[GET] (`/tests/configure`)                                },
  runAdminTest:         function(team_name, attrs)              { return AxiosResource('admin')[GET] (`/tests/run`, attrs)                               },
}

/*
  VFS Notes...

  1) List Directory      GET https://localhost/api/me/v2/vfs/list?q=/
  2) Get File            GET https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  3) Put File            PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  4) Create Directory    PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder ?????
  5) Delete Files        DEL https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
*/
