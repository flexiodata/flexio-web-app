(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global.CommandBarParser = factory());
}(this, (function () { 'use strict';

  // methods
  function CommandBarParser() {

    this.parse = function(str)
    {
      var keyword = this.getKeyword(str).toLowerCase()

      this.json = null
      this.errors = []

      if (this.keywords.hasOwnProperty(keyword))
      {
        this.json = this.keywords[keyword].call(this, str)
        this.command = keyword
      }
       else
      {
        this.errors.push({ "code": "unknown_command", "message": "Unknown command: '" + keyword +"'", "offset": 0, "length": keyword.length });
      }
    }

    this.toJSON = function(cmdbar)
    {
      this.parse(cmdbar)
      if (this.errors.length > 0)
        return null;
          else
        return this.json
    }

    this.validate = function(cmdbar)
    {
      this.parse(cmdbar)

      // check if there are any invalid/unknown arguments
      if (this.args.hasOwnProperty(this.command))
      {
        var args = this.args[this.command];

        var match, offset, re = /([a-zA-Z]+):/g;
        while (match = re.exec(cmdbar))
        {
          if ((offset = this.findToplevel(cmdbar, match[0], 0)) != -1)
          {
            if (!this.contains(args,match[1]))
            {
              this.errors.push({ "code": "unknown_argument", "message": "Unknown argument: '" + match[1] +"'", offset: offset, length: match[0].length })
            }
          }
        }
      }

      if (this.errors.length == 0)
        return true
         else
        return this.errors
    }

    this.toCmdbar = function(json)
    {
      if (!json)
        return '';
      if (!json.hasOwnProperty('type'))
        return ''; // missing template type
      var type = ''+json['type'];

      if (!this.templates.hasOwnProperty(type))
        return ''; // unknown template type

      return this.templates[type].call(this, json);
    }




    // private methods

    this.getKeyword = function(str)
    {
      var idx = str.search(/[\t ]/);
      if (idx == -1)
        return str;
         else
        return str.substr(0, idx);
    }


    this.findToplevel = function(str, needle, offset)
    {
      var ch, i = offset, k, len = str.length, needle_length = needle.length;
      var paren_level = 0;
      var brace_level = 0;
      var bracket_level = 0;
      var quote_char = 0;
      var is_keyword = (needle[0].toLowerCase() != needle[0].toUpperCase() ? true:false);

      needle = needle.toLowerCase();

      while (i < len)
      {
        ch = str[i];
        if (ch == '"' || ch == "'")
        {
          if (quote_char == ch)
          {
            quote_char = 0;
            ++i;
            continue;
          }
        }

        if (quote_char != 0)
        {
          ++i;
          continue;
        }

        if (paren_level+brace_level+bracket_level == 0)
        {
          if (is_keyword)
          {
            if (i == 0 || "(){}[]: \t".indexOf(str[i-1]) != -1)
            {
              if (str.substr(i,needle_length).toLowerCase() == needle)
              {
                if (i+needle_length >= len || "(){}[]: \t".indexOf(str[i+needle_length]) != -1)
                {
                  return i;
                }
              }
            }
          }
           else
          {
            if (str.substr(i,needle_length) == needle)
            {
              return i;
            }
          }
        }

        switch (ch)
        {
          case '(': paren_level++;    break;
          case ')': paren_level--;    break;
          case '{': brace_level++;    break;
          case '}': brace_level--;    break;
          case '[': bracket_level++;  break;
          case ']': bracket_level--;  break;
        }

        ++i;
      }

      return -1;
    }

    this.split = function(str, keywords)
    {
      var i, j, offset, offsets = [], endings = [], results = {};
      for (i = 0; i < keywords.length; ++i)
      {
        offset = this.findToplevel(str, keywords[i], 0);
        offsets.push(offset);
        if (offset != -1)
        {
          endings.push(offset);
        }
      }

      for (i = 0; i < offsets.length; ++i)
      {
        offset = offsets[i];
        if (offset != -1)
        {
          // find next offset for ending
          var ending = str.length;
          for (j = 0; j < endings.length; ++j)
          {
            if (endings[j] > offset && endings[j] < ending)
              ending = endings[j];
          }

          offset += keywords[i].length;
          if (offset < str.length && str[offset] == ':')
            offset++;
          while (offset < str.length && str[offset] == ' ')
            offset++;
          while (ending > offset && str[ending-1] == ' ')
            ending--;
          results[ keywords[i] ] = { "value": str.substr(offset, ending-offset),
                                     "offset": offset,
                                     "length": Math.max(1,ending-offset) }
        }
      }

      return results;
    }

    this.getArgumentFromPosition = function(str, keywords, loc)
    {
      var i, j, offset, offsets = [], endings = [];
      for (i = 0; i < keywords.length; ++i)
      {
        offset = this.findToplevel(str, keywords[i], 0);
        if (offset + keywords[i].length < str.length)
        {
          offsets.push(offset);
          if (offset != -1)
          {
            endings.push(offset);
          }
        }
      }

      var cur = -1;
      var ret = null;
      for (i = 0; i < offsets.length; ++i)
      {
        if (loc > offsets[i] && offsets[i] > cur)
        {
          cur = offsets[i];
          ret = keywords[i];
        }
      }

      if (ret === null)
      {
        return null;
      }

      return { "arg": ret, "offset": cur };
    }

    this.partialStringLookup = function(arr, str)
    {
      if (str.length == 0)
        return -1;

      var partial = -1;
      for (var a = 0; a < arr.length; ++a)
      {
        if (arr[a].substr(0, str.length) == str)
        {
          return a;
        }
      }
      return -1;
    }

    this.dequoteColumn = function(str)
    {
      var res = (''+str).trim();
      if (res.length >= 2 && res[0] == '[' && res[res.length-1] == ']')
      {
        // remove quoting brackets
        res = res.substr(1, res.length-2);
      }
      return res;
    }

    this.parseColumns = this.parseFilenames = this.parseCommaList = function(str)
    {
      var off = 0, end, results = []
      while (off < str.length)
      {
        end = this.findToplevel(str, ',', off)
        if (end == -1)
        {
          end = str.length
        }
        results.push(this.dequoteColumn(str.substr(off, end-off).trim()))
        off = end+1
      }

      return results
    }

    this.parseListObject = function(str)
    {
      try
      {
        return JSON.parse(str);
      }
      catch(e)
      {
      }
      return null;
    }

    this.parseList = function(str)
    {
      var off = 0, end, chunk, obj, arrow, ret = [];
      while (off < str.length)
      {
        end = this.findToplevel(str, ',', off);
        if (end == -1)
        {
          end = str.length;
        }
        chunk = str.substr(off, end-off).trim();

        arrow = chunk.indexOf('=>');
        if (arrow != -1)
        {
          try
          {
            var key = chunk.substr(0, arrow).trim();
            var value = chunk.substr(arrow+2).trim();
            if (key.length >= 2 && key[0] == '"' && key[key.length-1] == '"')
              key = JSON.parse(key);
            if (value.length >= 2 && value[0] == '"' && value[value.length-1] == '"')
              value = JSON.parse(value);
            ret.push({ "key": key, "value": value });
          }
          catch(e)
          {
            ret.push(chunk);
          }
        }
         else if (chunk.length >= 2 && chunk[0] == '{' && chunk[chunk.length-1] == '}')
        {
          obj = this.parseListObject(chunk);
          if (obj instanceof Object)
            ret.push(obj);
             else
            ret.push(chunk); // couldn't parse object, use string
        }
         else
        {
          ret.push(chunk);
        }
        off = end+1;
      }
      return ret;
    }

    this.append = function(str1, str2)
    {
      var res = str1;
      if (res.length > 0)
        res += ' ';
      return res + str2;
    }

    this.quoteColumnIfNecessary = function(col)
    {
      if (col.indexOf(' ') != -1)
        return '[' + col + ']';
         else
        return col;
    }

    this.toBoolean = function(val)
    {
           if (val === true || val === 1 || val === "true")   return true;
      else if (val === false || val === 0 || val === "false") return false;
      else if (val) return true;
      else          return false;
    }

    this.contains = function (arr,value)
    {
      for (var i = 0; i < arr.length; ++i)
      {
        if (arr[i] == value)
          return true;
      }
      return false;
    }

    // keyword parsers are specified here
    this.args = {};
    this.hints = {};
    this.keywords = {};
    this.templates = {};









    this.hints.convert = {
      "from":      [ "delimited", "json", "pdf", "table", "text" ],
      "to":        [ "delimited", "json", "pdf", "table", "text" ],
      "delimiter": [ "comma", "none", "pipe", "semicolon", "tab" ],
      "qualifier": [ "none", "single-quote", "double-quote" ],
      "header":    [ "true", "false" ]
    };
    this.args.convert = ['from','to','delimiter','qualifier','header'];
    this.keywords.convert = function(str)
    {
      var json =
        {
            "type": "flexio.convert",
            "params": { }
        };

      // convert from: delimited to: table delimiter: "," qualifier: '"' header: 1

      var params = this.split(str, this.args.convert)
      var from_format = '';
      var to_format = '';
      var delimiter, qualifier;

      if (params.hasOwnProperty('from'))
      {
        from_format = params['from'].value;

        json.params.input = {};

        if (from_format == 'delimited')
          json.params.input.format = 'delimited';
        else if (from_format == 'json')
          json.params.input.format = 'json';
        else if (from_format == 'table')
          json.params.input.format = 'table';
        else if (from_format == 'pdf')
          json.params.input.format = 'pdf';
        else
          this.errors.push({ "code":     "invalid_value",
                             "message":  "Invalid value: '" + from_format +"'",
                             "offset":   params['from'].offset,
                             "length":   params['from'].length })

      }

      if (from_format == '' && (params.hasOwnProperty('delimiter') || params.hasOwnProperty('qualifier') || params.hasOwnProperty('header')))
      {
        // no from format specified -- implicit format is delimited
        json.params.input = { "format": "delimited" };
        from_format = 'delimited';
      }

      if (params.hasOwnProperty('to'))
      {
        to_format = params['to'].value;

        json.params.output = {};
        if (to_format == 'delimited')
          json.params.output.format = 'delimited';
        else if (to_format == 'json')
          json.params.output.format = 'json';
        else if (to_format == 'table')
          json.params.output.format = 'table';
        else if (to_format == 'text')
          json.params.output.format = 'text';
        else
          this.errors.push({ "code":     "invalid_value",
                             "message":  "Invalid value: '" + to_format +"'",
                             "offset":   params['to'].offset,
                             "length":   params['to'].length })
      }

      if (params.hasOwnProperty('delimiter'))
      {
        delimiter = params['delimiter'].value.trim();

        if (delimiter == 'none')
        {
          if (from_format == 'delimited') json.params.input.delimiter  = "{none}";
          if (to_format   == 'delimited') json.params.output.delimiter = "{none}";
        }
        else if (delimiter == 'comma' || delimiter == '","' || delimiter == "','" || delimiter == ',')
        {
          if (from_format == 'delimited') json.params.input.delimiter  = "{comma}";
          if (to_format   == 'delimited') json.params.output.delimiter = "{comma}";
        }
        else if (delimiter == 'tab')
        {
          if (from_format == 'delimited') json.params.input.delimiter  = "{tab}";
          if (to_format   == 'delimited') json.params.output.delimiter = "{tab}";
        }
        else if (delimiter == 'pipe')
        {
          if (from_format == 'delimited') json.params.input.delimiter  = "{pipe}";
          if (to_format   == 'delimited') json.params.output.delimiter = "{pipe}";
        }
        else if (delimiter == 'semicolon')
        {
          if (from_format == 'delimited') json.params.input.delimiter  = "{semicolon}";
          if (to_format   == 'delimited') json.params.output.delimiter = "{semicolon}";
        }
         else
        {
          try
          {
            var delimiter = JSON.parse(delimiter);
            if (from_format == 'delimited') json.params.input.delimiter  = delimiter;
            if (to_format   == 'delimited') json.params.output.delimiter = delimiter;
          }
          catch (e)
          {
          }
        }
      }

      if (params.hasOwnProperty('qualifier'))
      {
        qualifier = params['qualifier'].value.trim();

        if (qualifier == 'none')
        {
          if (from_format == 'delimited') json.params.input.qualifier  = "{none}";
          if (to_format   == 'delimited') json.params.output.qualifier = "{none}";
        }
         else if (qualifier == 'double-quote' || qualifier == "'\"'" )
        {
          if (from_format == 'delimited') json.params.input.qualifier  = "{double-quote}";
          if (to_format   == 'delimited') json.params.output.qualifier = "{double-quote}";
        }
         else if (qualifier == 'single-quote' || qualifier == "\"'\"")
        {
          if (from_format == 'delimited') json.params.input.qualifier  = "{single-quote}";
          if (to_format   == 'delimited') json.params.output.qualifier = "{single-quote}";
        }
         else
        {
          try
          {
            // custom qualifier must be a single char enclosed in ' or "
            if (qualifier.length == 0)
              throw {}
            if (qualifier[0] == "'") // json can only parse double-quoted strings
              qualifier = qualifier.replace(/"/g,"\\\"").replace(/'/g,"\"");

            var qualifier = JSON.parse(qualifier);
            if (qualifier.length != 1)
              throw {}
            if (from_format == 'delimited') json.params.input.qualifier  = qualifier;
            if (to_format   == 'delimited') json.params.output.qualifier = qualifier;
          }
          catch (e)
          {
            this.errors.push({ "code":     "invalid_value",
                               "message":  "Invalid value: " + qualifier,
                               "offset":   params['qualifier'].offset,
                               "length":   params['qualifier'].length })
          }
        }
      }

      if (params.hasOwnProperty('header'))
      {
        var header = params['header'].value;

        if (header != 'true' && header != 'false')
        {
            this.errors.push({ "code":     "invalid_value",
                               "message":  "Invalid value: '" + header + "'",
                               "offset":   params['header'].offset,
                               "length":   params['header'].length })
        }

        if (from_format == 'delimited') json.params.input.header  = (header == 'true' ? true : false);
        if (to_format   == 'delimited') json.params.output.header = (header == 'true' ? true : false);
      }

      return json;
    };

    this.templates["flexio.convert"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var input = json.params.hasOwnProperty('input') ? json.params.input : {};
      var output = json.params.hasOwnProperty('output') ? json.params.output : {};
      var input_format, output_format = '';
      var res = "convert";

      if (input.hasOwnProperty('format'))
      {
        input_format = input.format;
        if (input.format == 'delimited')
          res = this.append(res, "from: delimited");
        else if (input.format == 'table')
          res = this.append(res, 'from: table')
        else if (input.format == 'json')
          res = this.append(res, 'from: json');
        else if (input.format == 'pdf')
          res = this.append(res, 'from: pdf');
        else if (input.format == 'text')
          res = this.append(res, 'from: text');
      }

      if (output.hasOwnProperty('format'))
      {
        output_format = output.format;
        if (output.format == 'delimited')
          res = this.append(res, "to: delimited");
        else if (output.format == 'table')
          res = this.append(res, 'to: table')
        else if (output.format == 'json')
          res = this.append(res, 'to: json');
        else if (output.format == 'pdf')
          res = this.append(res, 'to: pdf');
        else if (output.format == 'text')
          res = this.append(res, 'to: text');
      }

      if (input_format == 'delimited' || output_format == 'delimited')
      {
        var obj = (input_format == 'delimited' ? input : output);
        if (obj.hasOwnProperty('delimiter'))
        {
          var delimiter = obj.delimiter;
          if (delimiter == '{none}')
            res = this.append(res, "delimiter: none");
          else if (delimiter == '{comma}')
            res = this.append(res, "delimiter: comma");
          else if (delimiter == '{tab}')
            res = this.append(res, "delimiter: tab");
          else if (delimiter == '{pipe}')
            res = this.append(res, "delimiter: pipe");
          else if (delimiter == '{semicolon}')
            res = this.append(res, "delimiter: semicolon");
          else
            res = this.append(res, "delimiter: " + JSON.stringify(delimiter));
        }

        if (obj.hasOwnProperty('qualifier'))
        {
          var qualifier = obj.qualifier;
          if (qualifier == '{none}' || qualifier == '')
            res = this.append(res, "qualifier: none");
          else if (qualifier == '{single-quote}' || qualifier == "'")
            res = this.append(res, "qualifier: single-quote");
          else if (qualifier == '{double-quote}' || qualifier == '"')
            res = this.append(res, "qualifier: double-quote");
          else
            res = this.append(res, "qualifier: " + JSON.stringify(qualifier));
        }

        if (obj.hasOwnProperty('header'))
        {
          res = this.append(res, "header: " + (obj.header ? "true":"false"));
        }
      }

      return res;
    }





    this.args.calc = [ 'name', 'formula', 'type', 'decimal' ];
    this.hints.calc = {
      "type":        [ 'text', 'numeric', 'integer', 'date', 'datetime', 'boolean' ]
    };
    this.keywords.calc = function(str)
    {
      var json =
        {
            "type": "flexio.calc",
            "params": { }
        }

      var params = this.split(str, this.args.calc);

      if (params.hasOwnProperty('name'))
      {
        json.params.name = params['name'].value;
      }
       else
      {
          this.errors.push({ "code":     "missing_parameter",
                             "message":  "Missing parameter 'name:'",
                             "offset":   str.length-1,
                             "length":   1 })
      }

      if (params.hasOwnProperty('type'))
      {
        json.params.type = params['type'].value;

        if (!this.contains(this.hints.calc['type'], json.params.type))
        {
          this.errors.push({ "code":     "invalid_value",
                             "message":  "Invalid value: '" + json.params.type +"'",
                             "offset":   params['type'].offset,
                             "length":   params['type'].length })
        }
      }


      if (params.hasOwnProperty('decimal'))
      {
        json.params.decimals = parseInt(params['decimal'].value);
      }

      if (params.hasOwnProperty('formula'))
      {
        json.params.expression = params['formula'].value;
      }
       else
      {
          this.errors.push({ "code":     "missing_parameter",
                             "message":  "Missing parameter 'formula:'",
                             "offset":   str.length-1,
                             "length":   1 })
      }


      return json;
    };


    this.templates["flexio.calc"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = 'calc';

      if (json.params.hasOwnProperty('expression'))
      {
        res = this.append(res, "formula: " + json.params.expression);
      }

      if (json.params.hasOwnProperty('name'))
      {
        res = this.append(res, "name: " + json.params.name);
      }

      if (json.params.hasOwnProperty('type'))
      {
        res = this.append(res, "type: " + json.params.type);
      }

      if (json.params.hasOwnProperty('decimals') && parseInt(json.params.decimals) > 0)
      {
        res = this.append(res, "decimal: " + parseInt(json.params.decimals));
      }

      return res;
    }







    this.args.email = ['to','from','subject','body','html','reply-to','data'];
    this.hints.email = {
      "data":       [ 'none','body','attachment' ]
    };
    this.keywords.email = function(str)
    {
      var json =
        {
          "type": "flexio.email",
          "params": {
          }
        };

      var params = this.split(str, this.args.email);

      if (params.hasOwnProperty('to'))
      {
        json.params.to = this.parseCommaList(params['to'].value);
      }

      if (params.hasOwnProperty('from'))
      {
        json.params.from = params['from'].value;
      }

      if (params.hasOwnProperty('subject'))
      {
        json.params.subject = params['subject'].value;
      }

      if (params.hasOwnProperty('body'))
      {
        json.params.body_text = params['body'].value;
      }

      if (params.hasOwnProperty('html'))
      {
        json.params.body_html = params['html'].value;
      }

      if (params.hasOwnProperty('data'))
      {
        json.params.data = params['data'].value;
      }

      return json;
    };


    this.templates["flexio.email"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "email";

      if (json.params.hasOwnProperty('from'))
      {
        res = this.append(res, "from: " + json.params.from);
      }

      if (json.params.hasOwnProperty('to'))
      {
        if (Array.isArray(json.params.to))
        {
          var str = '';
          for (var i = 0; i < json.params.to.length; ++i)
          {
            if (str.length > 0)
            {
              str += ', ';
            }
            str += json.params.to[i];
          }

          res = this.append(res, "to: " + str);
        }
         else
        {
          res = this.append(res, "to: " + json.params.to);
        }
      }

      if (json.params.hasOwnProperty('subject'))
      {
        res = this.append(res, "subject: " + json.params.subject);
      }

      if (json.params.hasOwnProperty('body_text'))
      {
        res = this.append(res, "body: " + json.params.body_text);
      }

      if (json.params.hasOwnProperty('body_html'))
      {
        res = this.append(res, "html: " + json.params.body_html);
      }

      if (json.params.hasOwnProperty('data'))
      {
        res = this.append(res, "data: " + json.params.data);
      }

      return res;
    };





    this.args.execute = ['lang'/*, 'code'*/];
    this.hints.execute = {
      "lang":       [ /*'javascript',*/ 'python', 'html' ]
    };
    this.keywords.execute = function(str)
    {
      var json =
        {
          "type": "flexio.execute",
          "params": { }
        };

      var params = this.split(str, this.args.execute);

      if (params.hasOwnProperty('lang'))
      {
        json.params.lang = params['lang'].value;

        if (!this.contains(this.hints.execute['lang'], json.params.lang))
        {
          this.errors.push({ "code":     "invalid_value",
                             "message":  "Invalid value: '" + json.params.lang +"'",
                             "offset":   params['lang'].offset,
                             "length":   params['lang'].length })
        }
      }
       else
      {
          this.errors.push({ "code":     "missing_parameter",
                             "message":  "Missing parameter 'lang:'",
                             "offset":   str.length-1,
                             "length":   1 })
      }

      if (params.hasOwnProperty('code'))
      {
        json.params.code = params['code'].value;
      }

      return json;
    };


    this.templates["flexio.execute"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "execute";

      if (json.params.hasOwnProperty('lang'))
      {
        res = this.append(res, "lang: " + json.params['lang']);
      }

      if (json.params.hasOwnProperty('code'))
      {
        res = this.append(res, "code: " + json.params['code']);
      }

      return res;
    };





    this.args.input = ['from','location','file'];
    this.keywords.input = function(str)
    {
      var json =
        {
          "type": "flexio.input",
          "params": { }
        };



      var params = this.split(str, this.args.input);

      if (params.hasOwnProperty('url'))
      {
        json.params.items.push({"name": "file1", "path": params['url'].value});
        json.params.connection.connection_type = "http";
      }

      if (params.hasOwnProperty('from'))
      {
        json.params.connection = params['from'].value;
      }

      if (params.hasOwnProperty('location'))
      {
        json.params.location = params['location'].value;
      }

      if (params.hasOwnProperty('file'))
      {
        var arr = this.parseList(params['file'].value);

        json.params.items = [];

        for (var i = 0; i < arr.length; ++i)
        {
          if (arr[i] instanceof Object)
          {
            if (arr[i].hasOwnProperty('key') && arr[i].hasOwnProperty('value'))
            {
              // arrow syntax
              json.params.items.push({"name": arr[i].value, "path": arr[i].key});
            }
            else if (arr[i].hasOwnProperty('path'))
            {
              var name, path = arr[i].path;
              if (arr[i].hasOwnProperty('name'))
                name = arr[i].name;
                 else
                name = path;
              json.params.items.push({"name": name, "path": path});
            }

          }
           else
          {
            // simple string
            json.params.items.push({"path": arr[i]});
          }
        }
      }

      return json;
    };


    this.templates["flexio.input"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "input";

      if (json.params.hasOwnProperty('connection'))
      {
        res = this.append(res, "from: " + json.params.connection);
      }

      if (json.params.hasOwnProperty('location'))
      {
        res = this.append(res, "location: " + json.params.location);
      }

      var i, items = [];
      if (json.params.hasOwnProperty('items') && Array.isArray(json.params.items))
      {

        var str = '';
        for (i = 0; i < json.params.items.length; ++i)
        {
          if (str.length > 0)
          {
            str += ', ';
          }

          if (json.params.items[i].hasOwnProperty('path'))
          {
            str += json.params.items[i].path;

            if (json.params.items[i].hasOwnProperty('name') && json.params.items[i].name != json.params.items[i].path)
            {
              str += " => " + json.params.items[i].name;
            }
          }


        }

        res = this.append(res, "file: " + str);
      }


      return res;
    };




    this.args.fail = ['code','message'];
    this.hints.fail = {
      "code":       [ 'general', 'unimplemented', 'unauthorized' ]
    };
    this.keywords.fail = function(str)
    {
      var json =
        {
          "type": "flexio.fail",
          "params": { }
        };

      var params = this.split(str, this.args.fail);

      if (params.hasOwnProperty('code'))
      {
        json.params.code = params['code'].value;
      }

      if (params.hasOwnProperty('message'))
      {
        json.params.message = params['message'].value;
      }

      return json;
    };


    this.templates["flexio.fail"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "fail";

      if (json.params.hasOwnProperty('code'))
      {
        res = this.append(res, "code: " + json.params['code']);
      }

      if (json.params.hasOwnProperty('message'))
      {
        res = this.append(res, "message: " + json.params['message']);
      }

      return res;
    };





    this.args.comment = [];
    this.hints.comment = {};
    this.keywords.comment = function(str)
    {
      var json =
        {
          "type": "flexio.comment",
          "params": { }
        };

      return json;
    };


    this.templates["flexio.comment"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "comment";
      return res;
    };





    this.args.limit = ['sample','value'];
    this.hints.limit = {
      "sample":       [ 'top', 'bottom', 'random' ]
    };
    this.keywords.limit = function(str)
    {
      var json =
        {
          "type": "flexio.limit",
          "params": { }
        };

      var params = this.split(str, this.args.limit);

      if (params.hasOwnProperty('sample'))
      {
        json.params.sample = params['sample'].value;
      }

      if (params.hasOwnProperty('value'))
      {
        json.params.value = parseInt(params['value'].value);
      }
       else
      {
          this.errors.push({ "code":     "missing_parameter",
                             "message":  "Missing parameter 'value:'",
                             "offset":   str.length-1,
                             "length":   1 })
      }

      return json;
    };


    this.templates["flexio.limit"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "limit";

      if (json.params.hasOwnProperty('sample'))
      {
        res = this.append(res, "sample: " + json.params['sample']);
      }

      if (json.params.hasOwnProperty('value'))
      {
        res = this.append(res, "value: " + json.params['value']);
      }

      return res;
    };



    this.args.merge = [];
    this.hints.merge = {
    };
    this.keywords.merge = function(str)
    {
      var json =
        {
          "type": "flexio.merge",
          "params": { }
        };

      return json;
    };


    this.templates["flexio.merge"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "merge";
      return res;
    };



    this.args.sleep= ['value'];
    this.keywords.sleep = function(str)
    {
      var json =
        {
          "type": "flexio.sleep",
          "params": { }
        };

      var params = this.split(str, this.args.sleep);

      if (params.hasOwnProperty('value'))
      {
        json.params.value = parseInt(params['value'].value);
      }

      return json;
    };


    this.templates["flexio.sleep"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "sleep";

      if (json.params.hasOwnProperty('value'))
      {
        res = this.append(res, "value: " + json.params['value']);
      }

      return res;
    };




    this.args.output = ['to', 'location', 'file'];
    this.keywords.output = function(str)
    {
      var json =
        {
          "type": "flexio.output",
          "params": {
          }
        };

      var params = this.split(str, this.args.output);

      if (params.hasOwnProperty('to'))
      {
        json.params.connection = params['to'].value;
      }

      if (params.hasOwnProperty('location'))
      {
        json.params.location = params['location'].value;
      }

      if (params.hasOwnProperty('file'))
      {
        var arr = this.parseList(params['file'].value);

        json.params.items = [];

        for (var i = 0; i < arr.length; ++i)
        {
          if (arr[i] instanceof Object)
          {
            if (arr[i].hasOwnProperty('key') && arr[i].hasOwnProperty('value'))
            {
              // arrow syntax
              json.params.items.push({"name": arr[i].key, "path": arr[i].value});
            }
            else if (arr[i].hasOwnProperty('name'))
            {
              var name = arr[i].name, path;
              if (arr[i].hasOwnProperty('path'))
                path = arr[i].path;
                 else
                path = name;
              json.params.items.push({"name": name, "path": path});
            }

          }
           else
          {
            // simple string
            json.params.items.push({"name": arr[i]});
          }
        }
      }

      return json;
    };


    this.templates["flexio.output"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "output";

      if (json.params.hasOwnProperty('connection'))
      {
        res = this.append(res, "to: " + json.params.connection);
      }

      if (json.params.hasOwnProperty('location'))
      {
        res = this.append(res, "location: " + json.params.location);
      }

      var i, items = [];
      if (json.params.hasOwnProperty('items') && Array.isArray(json.params.items))
      {

        var str = '';
        for (i = 0; i < json.params.items.length; ++i)
        {
          if (str.length > 0)
          {
            str += ', ';
          }

          if (json.params.items[i].hasOwnProperty('name'))
          {
            str += json.params.items[i].name;

            if (json.params.items[i].hasOwnProperty('path') && json.params.items[i].name != json.params.items[i].path)
            {
              str += " => " + json.params.items[i].path;
            }
          }


        }

        res = this.append(res, "file: " + str);
      }

      return res;
    }








    this.args.select = ['col','file'];
    this.keywords.select = function(str)
    {
      var json =
        {
          "type": "flexio.select",
          "params": {
          }
        };

      var params = this.split(str, this.args.select);

      if (params.hasOwnProperty('file'))
      {
        json.params.files = [];

        var i, files = this.parseList(params['file'].value);
        for (i = 0; i < files.length; ++i)
        {
          json.params.files.push(files[i]);
        }
      }

      if (params.hasOwnProperty('col'))
      {
        json.params.columns = [];
        var i, columns = this.parseColumns(params['col'].value)
        for (i = 0; i < columns.length; ++i)
        {
          json.params.columns.push(columns[i]);
        }
      }

      return json;
    };


    this.templates["flexio.select"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "select";

      if (json.params.hasOwnProperty('files'))
      {
        var str = '';
        for (var i = 0; i < json.params.files.length; ++i)
        {
          if (i > 0)
          {
            str += ', ';
          }
          str += this.quoteColumnIfNecessary(json.params.files[i]);

        }

        res = this.append(res, "file: " + str);
      }

      if (json.params.hasOwnProperty('columns'))
      {
        var str = '';
        for (var i = 0; i < json.params.columns.length; ++i)
        {
          if (i > 0)
          {
            str += ', ';
          }
          str += this.quoteColumnIfNecessary(json.params.columns[i]);

        }

        res = this.append(res, "col: " + str);
      }

      return res;
    }



    this.args.rename = ['col', 'file'];
    this.keywords.rename = function(str)
    {
      var json =
        {
          "type": "flexio.rename",
          "params": {
          }
        };

      var params = this.split(str, this.args.rename);

      if (params.hasOwnProperty('file'))
      {
        var arr = this.parseList(params['file'].value);

        json.params.files = [];
        for (var i = 0; i < arr.length; ++i)
        {
          if (arr[i] instanceof Object)
          {
            if (arr[i].hasOwnProperty('key') && arr[i].hasOwnProperty('value'))
            {
              // arrow syntax
              json.params.files.push({"name": arr[i].key, "new_name": arr[i].value});
            }
            else if (arr[i].hasOwnProperty('name') && arr[i].hasOwnProperty('new_name'))
            {
              var name = arr[i].name;
              var new_name = arr[i].new_name;
              json.params.files.push({"name": name, "new_name": new_name});
            }
          }
        }
      }

      if (params.hasOwnProperty('col'))
      {
        var arr = this.parseList(params['col'].value)

        json.params.columns = [];
        for (var i = 0; i < arr.length; ++i)
        {
          if (arr[i] instanceof Object)
          {
            if (arr[i].hasOwnProperty('key') && arr[i].hasOwnProperty('value'))
            {
              // arrow syntax
              json.params.columns.push({"name": arr[i].key, "new_name": arr[i].value});
            }
            else if (arr[i].hasOwnProperty('name') && arr[i].hasOwnProperty('new_name'))
            {
              var name = arr[i].name;
              var new_name = arr[i].new_name;
              json.params.columns.push({"name": name, "new_name": new_name});
            }
          }
        }
      }

      return json;
    };


    this.templates["flexio.rename"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "rename";

      if (json.params.hasOwnProperty('files'))
      {
        var str = '';
        var first = true;
        for (var i = 0; i < json.params.files.length; ++i)
        {
          if (first === false)
            str += ', ';

          if (json.params.files[i].hasOwnProperty('name') && json.params.files[i].hasOwnProperty('new_name'))
          {
            str += this.quoteColumnIfNecessary(json.params.files[i].name);
            str += "=>";
            str += this.quoteColumnIfNecessary(json.params.files[i].new_name);
          }

          first = false;
        }

        res = this.append(res, "file: " + str);
      }

      if (json.params.hasOwnProperty('columns'))
      {
        var str = '';
        var first = true;
        for (var i = 0; i < json.params.columns.length; ++i)
        {
          if (first === false)
            str += ', ';

          if (json.params.columns[i].hasOwnProperty('name') && json.params.columns[i].hasOwnProperty('new_name'))
          {
            str += this.quoteColumnIfNecessary(json.params.columns[i].name);
            str += "=>";
            str += this.quoteColumnIfNecessary(json.params.columns[i].new_name);
          }

          first = false;
        }

        res = this.append(res, "col: " + str);
      }

      return res;
    }




    this.args.sort = ['col'];
    this.keywords.sort = function(str)
    {
      var json =
        {
            "type": "flexio.sort",
            "params": {
                "order": []
            }
        };

      var params = this.split(str, this.args.sort);

      if (params.hasOwnProperty('col'))
      {
        var i, columns = this.parseColumns(params['col'].value)
        for (i = 0; i < columns.length; ++i)
        {
          var col = columns[i].trim();
          var desc = false;
          var regex = /\sdesc$/i;
          if (col.match(regex))
          {
            col = col.replace(regex,'').trim();
            desc = true;
          }
          col = this.dequoteColumn(col);
          json.params.order.push({ expression: col, direction: desc?'desc':'asc' });
        }
      }

      return json;
    };

    this.templates["flexio.sort"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "sort";

      if (json.params.hasOwnProperty('order'))
      {
        res = this.append(res, "col:")

        var cols = json.params.order;

        for (var i = 0; i < cols.length; ++i)
        {
          if (cols[i].hasOwnProperty('expression'))
          {
            res = this.append(res, cols[i].expression);

            if (cols[i].hasOwnProperty('direction'))
            {
              if (cols[i].direction === 'desc')
                res = this.append(res, cols[i].direction);
            }
          }

          if (i < cols.length-1)
            res = res += ',';
        }
      }

      return res;
    }






    this.args.transform = ['col','case','clean','trim']
    this.hints.transform= {
      "case":      [ "none", "lower", "upper", "proper", "first-letter" ],
      "trim":      [ "leading", "trailing", "leading-trailing" ]
    };
    this.keywords.transform = function(str)
    {
      var json =
      {
            "type": "flexio.transform",
            "params": {
            }
        };

      var params = this.split(str, this.args.transform);

      if (params.hasOwnProperty('col'))
      {
        var columns = this.parseColumns(params['col'].value)

        json.params.columns = columns;
      }

      json.params.operations = [];

      if (params.hasOwnProperty('case'))
      {
        var xcase = params['case'].value;
        if (xcase == 'lower' || xcase == 'upper')
        {
          json.params.operations.push({ "operation":"case", "case":xcase });
        }
      }

      if (params.hasOwnProperty('trim'))
      {
        var arg = params['trim'].value;
        if (arg == 'leading' || arg == 'trailing' || arg == 'leading-trailing')
        {
          json.params.operations.push({ "operation":"trim", "location": arg });
        }
      }

      if (params.hasOwnProperty('clean'))
      {
        var location = params.hasOwnProperty('location') ? params.location : 'all';
        if (location == 'all' || location == 'leading' || location == 'trailing' || location == 'leading-trailing')
        {
          var arg = params['clean'].value;
          json.params.operations.push({ "operation":"clean", "location": location });
        }
      }

      return json;
    };

    this.templates["flexio.transform"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "transform";

      if (json.params.hasOwnProperty('columns'))
      {
        var str = '';
        for (var i = 0; i < json.params.columns.length; ++i)
        {
          if (i > 0)
          {
            str += ', ';
          }
          str += this.quoteColumnIfNecessary(json.params.columns[i]);
        }

        res = this.append(res, "col: " + str);
      }

      var lookupOperation = function(op)
      {
          if (!json.params.hasOwnProperty('operations') || !Array.isArray(json.params.operations))
            return null;
          for (var i = 0; i < json.params.operations.length; ++i)
          {
            if (json.params.operations[i].hasOwnProperty('operation') && json.params.operations[i].operation == op)
              return json.params.operations[i];
          }
          return null;
      }


      var op = lookupOperation('trim');
      if (op && op.hasOwnProperty('location'))
      {
        res = this.append(res, "trim: " + op.location);
      }


      var op = lookupOperation('case');
      if (op && op.hasOwnProperty('case'))
      {
        res = this.append(res, "case: " + op.case);
      }





      return res;
    };















    this.args.filter = ['where','exclude'];
    this.keywords.filter = function(str)
    {
      var json =
        {
          "type": "flexio.filter",
          "params": {
          }
        };

      var params = this.split(str, this.args.filter);

      if (params.hasOwnProperty('where'))
      {
        json.params.where = params['where'].value;
      }

      if (params.hasOwnProperty('exclude'))
      {
        json.params.exclude = this.toBoolean(params['exclude'].value);
      }

      return json;
    };

    this.templates["flexio.filter"] = function(json)
    {
      if (!json || !json.hasOwnProperty('params'))
        return '';

      var res = "filter";

      if (json.params.hasOwnProperty('where'))
      {
        res = this.append(res, "where: " + json.params.where);
      }

      if (json.params.hasOwnProperty('exclude'))
      {
        res = this.append(res, "exclude: " + (json.params.exclude ? 'true' : 'false'));
      }

      return res;
    }







    this.getHintableCommands = function() {
      return [
        'input',
        'output',
        'convert',
        'limit',
        'select',
        'rename',
        'sort',
        'filter',
        'calc',
        'email',
        'execute',
        'transform',
        'merge',
        'comment'
      ]
    },

    this.getVerbArguments = function(verb) {
      // get a list of arguments for a given verb.
      // e.g. for "calc" this would return [ 'name', 'formula', 'type', 'decimal' ] etc.
      return this.args.hasOwnProperty(verb) ? this.args[verb] : null;
    }

    this.getHints = function(str, idx, options) {

      var connections = [];
      var columns = [];

      if (!(options instanceof Object))
        options = {};

      if (!options.hasOwnProperty('columns'))
        options.columns = [];
      for (var i = 0; i < options.columns.length; ++i)
      {
        if (options.columns[i].hasOwnProperty('name'))
          columns.push(options.columns[i].name);
      }

      if (!options.hasOwnProperty('connections'))
        options.connections = [];
      for (var i = 0; i < options.connections.length; ++i)
      {
        if (options.connections[i].hasOwnProperty('ename') && options.connections[i].ename.length > 0)
        {
          connections.push(options.connections[i].ename);
        }
        else if (options.connections[i].hasOwnProperty('eid'))
        {
          connections.push(options.connections[i].eid);
        }
      }



      var first_space = str.indexOf(' ')

      if (first_space == -1 || idx < first_space)
      {
        var current_word = str.substr(0,idx);

        return {
          type: 'commands',
          arg: null,
          current_word: current_word,
          offset: 0,
          items: this.getHintableCommands()
        }
      }
       else if (idx > first_space && idx > 2)
      {

        var cmd = str.substr(0, first_space).toLowerCase();
        if (this.args.hasOwnProperty(cmd))
        {
          var args = this.args[cmd];
          var chunk = str.substr(0,idx);

          var last_separator = Math.max(chunk.lastIndexOf(' '), chunk.lastIndexOf(':'), chunk.lastIndexOf(','));
          var current_word = (last_separator == -1) ? '' : chunk.substr(last_separator+1).toLowerCase();

          var previous_separator = last_separator;
          while (previous_separator > 0 && (chunk[previous_separator] == ' ' || chunk[previous_separator] == ':'))
            previous_separator--;
          var previous_separator = (previous_separator == -1) ? -1 : Math.max(chunk.lastIndexOf(' ',previous_separator), chunk.lastIndexOf(':',previous_separator));
          var last_word = chunk.substr(previous_separator+1, last_separator - previous_separator).trim().toLowerCase();

          var argpos = this.getArgumentFromPosition(str, this.args[cmd], idx);
          var arg = (argpos === null ? "" : argpos.arg.toLowerCase());
          var offset = (argpos === null ? -1 : argpos.offset);

          var s = { "arg": arg, "last": last_word, "current": current_word };
          //console.log(JSON.stringify(s));

          // first, see if there are any specified hints for this combo
          if (this.hints.hasOwnProperty(cmd) && this.hints[cmd].hasOwnProperty(arg))
          {
            var partial = this.partialStringLookup(this.hints[cmd][arg], current_word);

            if ((last_word == arg || last_word == arg+":") && (partial != -1 || current_word == ''))
            {
              return {
                type: 'values',
                arg: arg,
                current_word: current_word,
                offset: (idx - current_word.length),
                items: this.hints[cmd][arg]
              }
            }
          }

          // specific handler for connections
          if (connections.length > 0 && (cmd == "input" && arg == "from") || (cmd == "output" && arg == "to"))
          {
            //var partial = this.partialStringLookup(connections, current_word);
            //console.log("last:"+last_word + " current:"+ current_word + " " + partial);

            if (last_word == arg || last_word == arg+":")// && (partial != -1 || current_word == ''))
            {
              return {
                type: 'connections',
                arg: arg,
                current_word: current_word,
                offset: (idx - current_word.length),
                items: options.connections
              }
            }
          }

          // specific handler for columns
          if (arg == "col" && columns.length > 0)
          {
            var partial = this.partialStringLookup(columns, current_word);
            //console.log("last:"+last_word + " current:"+ current_word + " " + partial);
            if (partial != -1 || current_word == '')
            {
              return {
                type: 'columns',
                arg: arg,
                current_word: current_word,
                offset: (idx - current_word.length),
                first: true,
                items: options.columns
              }
            }
          }

          // handler for command arguments (such as from: to: cols: etc)
          var partial = this.partialStringLookup(args, current_word);
          if (current_word == '' || partial != -1)
          {
            var last_word_is_argname = false;

            var args = [];
            for (var i = 0; i < this.args[cmd].length; ++i)
            {
              if (last_word == this.args[cmd][i] || last_word == this.args[cmd][i] + ":")
                last_word_is_argname = true;

              var offset = this.findToplevel(chunk, this.args[cmd][i], 0);
              if (offset == -1 || offset + this.args[cmd][i].length >= str.length)
              {
                // the second condition above says that, if the argument is the last thing
                // in the string, don't remove it from the droplist just yet (wait until a colon or space pressed)
                args.push(this.args[cmd][i] + ':');
              }

            }

            if (!last_word_is_argname && args.length > 0)
            {
              return {
                type: 'arguments',
                arg: null,
                current_word: current_word,
                offset:  (idx - current_word.length),
                items: args
              }
            }
          }
        }

      }

      return {
        type: 'none',
        arg: null,
        offset: -1,
        items: []
      }
    }
  };



  var g_cmdbarparser = new CommandBarParser

  var CommandBarParserExport = {
    validate: function(cmdbar) {
      var p = new CommandBarParser
      return p.validate(cmdbar)
    },
    toJSON: function(cmdbar) {
      var p = new CommandBarParser
      return p.toJSON(cmdbar)
    },
    toCmdbar: function(json) {
      var p = new CommandBarParser
      return p.toCmdbar(json)
    },
    getHints: function(str, idx, options) {
      //return g_cmdbarparser.getHints(str, idx, options);

      var dbg = g_cmdbarparser.getHints(str, idx, options);
      //console.log(Date() + JSON.stringify(dbg));
      return dbg;
    },
    getHintableCommands: function() {
      return g_cmdbarparser.getHintableCommands()
    },
    getVerbArguments: function(verb) {
      // get a list of arguments for a given verb.
      // e.g. for "calc" this would return [ 'name', 'formula', 'type', 'decimal' ] etc.
      return g_cmdbarparser.getVerbArguments(verb)
    }
  }

  // exposed public method
  return CommandBarParserExport

})));
