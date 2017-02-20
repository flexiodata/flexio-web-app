(function() {

    _.mixin({
        // _.move -- takes an array and moves the item at the 'from' index
        // to the 'to' index; great for use with jQuery.sortable()
        move: function (array, from_index, to_index) {
            if (to_index < 0) { return array; } // handle lower bound
            if (to_index >= array.length) { return array; } // handle lower bound
            array.splice(to_index, 0, array.splice(from_index, 1)[0] );
            return array;
        },

        isOk: function(val) {
            return _.isNull(val) || _.isUndefined(val) ? false : true;
        },

        // _.isArrayEqual -- takes two arrays and compares them to each other
        isArrayEqual: function (arr1, arr2) {
            // if either array is a falsy value, we're done
            if (!arr1 || !arr2)
                return false;

            // if the lengths don't match, we're done
            if (arr1.length != arr2.length)
                return false;

            var cnt = arr1.length;
            for (var i = 0; i < cnt; i++)
            {
                // check if we have nested arrays
                if (arr1[i] instanceof Array && arr2[i] instanceof Array)
                {
                    // recurse into the nested arrays
                    if (!_.equals(arr1[i], arr2[i]))
                        return false;
                }
                 else if (_.isObject(arr1[i]) && _.isObject(arr2[i]))
                {
                    if (!_.isEqual(arr1[i], arr2[i]))
                        return false;
                }
                 else if (arr1[i] != arr2[i])
                {
                    return false;
                }
            }

            return true;
        },

        // credit David Walsh for these four functions; ref. https://davidwalsh.name/nested-objects
        objectify: function(obj, splits, create) {
            var result = obj;
            for(var i = 0, s; result && (s = splits[i]); i++) {
                result = (s in result ? result[s] : (create ? result[s] = {} : undefined));
            }

            return result;
        },

        // creates an object if it doesn't already exist
        setNested: function(obj, name, value) {
            var splits = name.split('.'),
                s = splits.pop(),
                result = _.objectify(obj, splits, true);

            return result && s ? (result[s] = value) : undefined;
        },

        getNested: function(obj, name, create) {
            return _.objectify(obj, name.split('.'), create);
        },

        existsNested: function(obj, name) {
            return _.getNested(obj, name, false) !== undefined;
        },

        stringifyNestedObjects: function(obj) {
            if (obj && _.isObject(obj))
            {
                var old_obj = obj,
                    new_obj = _.clone(obj);

                // stringify JSON objects
                _.each(new_obj, function(val, key) {
                    if (_.isObject(val))
                        new_obj[key] = $.JSON.encode(val);
                });

                return new_obj;
            }

            return {};
        },

        stringifyNestedArrays: function(obj) {
            if (obj && _.isObject(obj))
            {
                var old_obj = obj,
                    new_obj = _.clone(obj);

                // stringify JSON objects
                _.each(new_obj, function(val, key) {
                    if ($.isArray(val))
                        new_obj[key] = $.JSON.encode(val);
                });

                return new_obj;
            }

            return {};
        },

        // plucks keys from objects in an array of objects
        pluckMany: function(arr, key) {
            var res_arr = [];

            _.each(arr, function(item) {
                if (_.isObject(item))
                    res_arr.push(_.map(item, key));
            });

            return res_arr;
        },

        // picks keys from objects in an array of objects
        pickMany: function(arr) {
            var res_arr = [],
                keys = _.rest(arguments, 1);

            _.each(arr, function(item) {
                if (_.isObject(item))
                    res_arr.push(_.pick(item, keys));
            });

            return res_arr;
        },

        withoutObject: function(arr, obj) {
            var res_arr = [];

            _.each(arr, function(item) {
                if (!_.isEqual(item, obj))
                    res_arr.push(item);
            });

            return res_arr;
        },

        camelCaseToHyphen: function(str) {
            if (typeof str != 'string')
                return '';

            return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
        },

        // _.jsonFromQueryString -- returns a JSON object that represents the query string
        jsonFromQueryString: function(str) {
            var retval = {};
            if (str === undefined)
                str = window.location.search;
            var params = str.slice(str.indexOf('?') + 1).split('&');
            for (var i = 0; i < params.length; i++)
            {
                var param = params[i].split('='),
                    key = param[0],
                    val = param[1];

                if (key !== undefined && val !== undefined && key.length > 0)
                    retval[key] = decodeURIComponent(val) || '';
            }
            return retval;
        },

        // _.jsonToQueryString -- returns a string that represents the JSON object
        jsonToQueryString: function(obj) {
            return _.map(obj, function(val, key) {
                return key + '=' + encodeURIComponent(val);
            }).join('&');
        },

        // _.jsonAppendValue -- appends a value to an existing key in the specified object;
        //   e.g. appending { id: 2 } to { id: [1] } will result in { id: [1,2] }
        jsonAppendValue: function(params, append_params) {
            var retval = _.extend({}, params);
            _.each(append_params, function(val, key) {
                if (!_.has(params, key))
                {
                    retval[key] = [val];
                }
                 else
                {
                    retval[key].push(val);
                    retval[key] = _.uniq(retval[key]);
                }
            });
            return retval;
        },

        // _.jsonRemoveValue -- removes a value from an existing key in the specified object;
        //   e.g. removing { id: 2 } from { id: [1,2] } will result in { id: [1] }
        jsonRemoveValue: function(params, remove_params) {
            var retval = _.extend({}, params);
            _.each(remove_params, function(val, key) {
                if (_.has(params, key))
                {
                    retval[key] = _.without(retval[key], val);
                    if (retval[key].length == 0)
                        delete retval[key];
                }
            });
            return retval;
        }
    });

}).call(this);
