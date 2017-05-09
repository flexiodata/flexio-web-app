export const HOSTNAME = window.location.hostname

// try to find variables that match ${...} for each parameter
export const VARIABLE_REGEX = /\$\{((string|boolean|integer|number|connection|column)[ ])?([a-z_-][a-z0-9_-]*)[ ]*(:([^}]*))?\}/gi
