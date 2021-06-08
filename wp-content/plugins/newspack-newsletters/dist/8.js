(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[8],{

/***/ "./node_modules/codemirror/mode/python/python.js":
/*!*******************************************************!*\
  !*** ./node_modules/codemirror/mode/python/python.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// CodeMirror, copyright (c) by Marijn Haverbeke and others\n// Distributed under an MIT license: https://codemirror.net/LICENSE\n\n(function(mod) {\n  if (true) // CommonJS\n    mod(__webpack_require__(/*! ../../lib/codemirror */ \"./node_modules/codemirror/lib/codemirror.js\"));\n  else {}\n})(function(CodeMirror) {\n  \"use strict\";\n\n  function wordRegexp(words) {\n    return new RegExp(\"^((\" + words.join(\")|(\") + \"))\\\\b\");\n  }\n\n  var wordOperators = wordRegexp([\"and\", \"or\", \"not\", \"is\"]);\n  var commonKeywords = [\"as\", \"assert\", \"break\", \"class\", \"continue\",\n                        \"def\", \"del\", \"elif\", \"else\", \"except\", \"finally\",\n                        \"for\", \"from\", \"global\", \"if\", \"import\",\n                        \"lambda\", \"pass\", \"raise\", \"return\",\n                        \"try\", \"while\", \"with\", \"yield\", \"in\"];\n  var commonBuiltins = [\"abs\", \"all\", \"any\", \"bin\", \"bool\", \"bytearray\", \"callable\", \"chr\",\n                        \"classmethod\", \"compile\", \"complex\", \"delattr\", \"dict\", \"dir\", \"divmod\",\n                        \"enumerate\", \"eval\", \"filter\", \"float\", \"format\", \"frozenset\",\n                        \"getattr\", \"globals\", \"hasattr\", \"hash\", \"help\", \"hex\", \"id\",\n                        \"input\", \"int\", \"isinstance\", \"issubclass\", \"iter\", \"len\",\n                        \"list\", \"locals\", \"map\", \"max\", \"memoryview\", \"min\", \"next\",\n                        \"object\", \"oct\", \"open\", \"ord\", \"pow\", \"property\", \"range\",\n                        \"repr\", \"reversed\", \"round\", \"set\", \"setattr\", \"slice\",\n                        \"sorted\", \"staticmethod\", \"str\", \"sum\", \"super\", \"tuple\",\n                        \"type\", \"vars\", \"zip\", \"__import__\", \"NotImplemented\",\n                        \"Ellipsis\", \"__debug__\"];\n  CodeMirror.registerHelper(\"hintWords\", \"python\", commonKeywords.concat(commonBuiltins));\n\n  function top(state) {\n    return state.scopes[state.scopes.length - 1];\n  }\n\n  CodeMirror.defineMode(\"python\", function(conf, parserConf) {\n    var ERRORCLASS = \"error\";\n\n    var delimiters = parserConf.delimiters || parserConf.singleDelimiters || /^[\\(\\)\\[\\]\\{\\}@,:`=;\\.\\\\]/;\n    //               (Backwards-compatibility with old, cumbersome config system)\n    var operators = [parserConf.singleOperators, parserConf.doubleOperators, parserConf.doubleDelimiters, parserConf.tripleDelimiters,\n                     parserConf.operators || /^([-+*/%\\/&|^]=?|[<>=]+|\\/\\/=?|\\*\\*=?|!=|[~!@]|\\.\\.\\.)/]\n    for (var i = 0; i < operators.length; i++) if (!operators[i]) operators.splice(i--, 1)\n\n    var hangingIndent = parserConf.hangingIndent || conf.indentUnit;\n\n    var myKeywords = commonKeywords, myBuiltins = commonBuiltins;\n    if (parserConf.extra_keywords != undefined)\n      myKeywords = myKeywords.concat(parserConf.extra_keywords);\n\n    if (parserConf.extra_builtins != undefined)\n      myBuiltins = myBuiltins.concat(parserConf.extra_builtins);\n\n    var py3 = !(parserConf.version && Number(parserConf.version) < 3)\n    if (py3) {\n      // since http://legacy.python.org/dev/peps/pep-0465/ @ is also an operator\n      var identifiers = parserConf.identifiers|| /^[_A-Za-z\\u00A1-\\uFFFF][_A-Za-z0-9\\u00A1-\\uFFFF]*/;\n      myKeywords = myKeywords.concat([\"nonlocal\", \"False\", \"True\", \"None\", \"async\", \"await\"]);\n      myBuiltins = myBuiltins.concat([\"ascii\", \"bytes\", \"exec\", \"print\"]);\n      var stringPrefixes = new RegExp(\"^(([rbuf]|(br)|(fr))?('{3}|\\\"{3}|['\\\"]))\", \"i\");\n    } else {\n      var identifiers = parserConf.identifiers|| /^[_A-Za-z][_A-Za-z0-9]*/;\n      myKeywords = myKeywords.concat([\"exec\", \"print\"]);\n      myBuiltins = myBuiltins.concat([\"apply\", \"basestring\", \"buffer\", \"cmp\", \"coerce\", \"execfile\",\n                                      \"file\", \"intern\", \"long\", \"raw_input\", \"reduce\", \"reload\",\n                                      \"unichr\", \"unicode\", \"xrange\", \"False\", \"True\", \"None\"]);\n      var stringPrefixes = new RegExp(\"^(([rubf]|(ur)|(br))?('{3}|\\\"{3}|['\\\"]))\", \"i\");\n    }\n    var keywords = wordRegexp(myKeywords);\n    var builtins = wordRegexp(myBuiltins);\n\n    // tokenizers\n    function tokenBase(stream, state) {\n      var sol = stream.sol() && state.lastToken != \"\\\\\"\n      if (sol) state.indent = stream.indentation()\n      // Handle scope changes\n      if (sol && top(state).type == \"py\") {\n        var scopeOffset = top(state).offset;\n        if (stream.eatSpace()) {\n          var lineOffset = stream.indentation();\n          if (lineOffset > scopeOffset)\n            pushPyScope(state);\n          else if (lineOffset < scopeOffset && dedent(stream, state) && stream.peek() != \"#\")\n            state.errorToken = true;\n          return null;\n        } else {\n          var style = tokenBaseInner(stream, state);\n          if (scopeOffset > 0 && dedent(stream, state))\n            style += \" \" + ERRORCLASS;\n          return style;\n        }\n      }\n      return tokenBaseInner(stream, state);\n    }\n\n    function tokenBaseInner(stream, state, inFormat) {\n      if (stream.eatSpace()) return null;\n\n      // Handle Comments\n      if (!inFormat && stream.match(/^#.*/)) return \"comment\";\n\n      // Handle Number Literals\n      if (stream.match(/^[0-9\\.]/, false)) {\n        var floatLiteral = false;\n        // Floats\n        if (stream.match(/^[\\d_]*\\.\\d+(e[\\+\\-]?\\d+)?/i)) { floatLiteral = true; }\n        if (stream.match(/^[\\d_]+\\.\\d*/)) { floatLiteral = true; }\n        if (stream.match(/^\\.\\d+/)) { floatLiteral = true; }\n        if (floatLiteral) {\n          // Float literals may be \"imaginary\"\n          stream.eat(/J/i);\n          return \"number\";\n        }\n        // Integers\n        var intLiteral = false;\n        // Hex\n        if (stream.match(/^0x[0-9a-f_]+/i)) intLiteral = true;\n        // Binary\n        if (stream.match(/^0b[01_]+/i)) intLiteral = true;\n        // Octal\n        if (stream.match(/^0o[0-7_]+/i)) intLiteral = true;\n        // Decimal\n        if (stream.match(/^[1-9][\\d_]*(e[\\+\\-]?[\\d_]+)?/)) {\n          // Decimal literals may be \"imaginary\"\n          stream.eat(/J/i);\n          // TODO - Can you have imaginary longs?\n          intLiteral = true;\n        }\n        // Zero by itself with no other piece of number.\n        if (stream.match(/^0(?![\\dx])/i)) intLiteral = true;\n        if (intLiteral) {\n          // Integer literals may be \"long\"\n          stream.eat(/L/i);\n          return \"number\";\n        }\n      }\n\n      // Handle Strings\n      if (stream.match(stringPrefixes)) {\n        var isFmtString = stream.current().toLowerCase().indexOf('f') !== -1;\n        if (!isFmtString) {\n          state.tokenize = tokenStringFactory(stream.current(), state.tokenize);\n          return state.tokenize(stream, state);\n        } else {\n          state.tokenize = formatStringFactory(stream.current(), state.tokenize);\n          return state.tokenize(stream, state);\n        }\n      }\n\n      for (var i = 0; i < operators.length; i++)\n        if (stream.match(operators[i])) return \"operator\"\n\n      if (stream.match(delimiters)) return \"punctuation\";\n\n      if (state.lastToken == \".\" && stream.match(identifiers))\n        return \"property\";\n\n      if (stream.match(keywords) || stream.match(wordOperators))\n        return \"keyword\";\n\n      if (stream.match(builtins))\n        return \"builtin\";\n\n      if (stream.match(/^(self|cls)\\b/))\n        return \"variable-2\";\n\n      if (stream.match(identifiers)) {\n        if (state.lastToken == \"def\" || state.lastToken == \"class\")\n          return \"def\";\n        return \"variable\";\n      }\n\n      // Handle non-detected items\n      stream.next();\n      return inFormat ? null :ERRORCLASS;\n    }\n\n    function formatStringFactory(delimiter, tokenOuter) {\n      while (\"rubf\".indexOf(delimiter.charAt(0).toLowerCase()) >= 0)\n        delimiter = delimiter.substr(1);\n\n      var singleline = delimiter.length == 1;\n      var OUTCLASS = \"string\";\n\n      function tokenNestedExpr(depth) {\n        return function(stream, state) {\n          var inner = tokenBaseInner(stream, state, true)\n          if (inner == \"punctuation\") {\n            if (stream.current() == \"{\") {\n              state.tokenize = tokenNestedExpr(depth + 1)\n            } else if (stream.current() == \"}\") {\n              if (depth > 1) state.tokenize = tokenNestedExpr(depth - 1)\n              else state.tokenize = tokenString\n            }\n          }\n          return inner\n        }\n      }\n\n      function tokenString(stream, state) {\n        while (!stream.eol()) {\n          stream.eatWhile(/[^'\"\\{\\}\\\\]/);\n          if (stream.eat(\"\\\\\")) {\n            stream.next();\n            if (singleline && stream.eol())\n              return OUTCLASS;\n          } else if (stream.match(delimiter)) {\n            state.tokenize = tokenOuter;\n            return OUTCLASS;\n          } else if (stream.match('{{')) {\n            // ignore {{ in f-str\n            return OUTCLASS;\n          } else if (stream.match('{', false)) {\n            // switch to nested mode\n            state.tokenize = tokenNestedExpr(0)\n            if (stream.current()) return OUTCLASS;\n            else return state.tokenize(stream, state)\n          } else if (stream.match('}}')) {\n            return OUTCLASS;\n          } else if (stream.match('}')) {\n            // single } in f-string is an error\n            return ERRORCLASS;\n          } else {\n            stream.eat(/['\"]/);\n          }\n        }\n        if (singleline) {\n          if (parserConf.singleLineStringErrors)\n            return ERRORCLASS;\n          else\n            state.tokenize = tokenOuter;\n        }\n        return OUTCLASS;\n      }\n      tokenString.isString = true;\n      return tokenString;\n    }\n\n    function tokenStringFactory(delimiter, tokenOuter) {\n      while (\"rubf\".indexOf(delimiter.charAt(0).toLowerCase()) >= 0)\n        delimiter = delimiter.substr(1);\n\n      var singleline = delimiter.length == 1;\n      var OUTCLASS = \"string\";\n\n      function tokenString(stream, state) {\n        while (!stream.eol()) {\n          stream.eatWhile(/[^'\"\\\\]/);\n          if (stream.eat(\"\\\\\")) {\n            stream.next();\n            if (singleline && stream.eol())\n              return OUTCLASS;\n          } else if (stream.match(delimiter)) {\n            state.tokenize = tokenOuter;\n            return OUTCLASS;\n          } else {\n            stream.eat(/['\"]/);\n          }\n        }\n        if (singleline) {\n          if (parserConf.singleLineStringErrors)\n            return ERRORCLASS;\n          else\n            state.tokenize = tokenOuter;\n        }\n        return OUTCLASS;\n      }\n      tokenString.isString = true;\n      return tokenString;\n    }\n\n    function pushPyScope(state) {\n      while (top(state).type != \"py\") state.scopes.pop()\n      state.scopes.push({offset: top(state).offset + conf.indentUnit,\n                         type: \"py\",\n                         align: null})\n    }\n\n    function pushBracketScope(stream, state, type) {\n      var align = stream.match(/^[\\s\\[\\{\\(]*(?:#|$)/, false) ? null : stream.column() + 1\n      state.scopes.push({offset: state.indent + hangingIndent,\n                         type: type,\n                         align: align})\n    }\n\n    function dedent(stream, state) {\n      var indented = stream.indentation();\n      while (state.scopes.length > 1 && top(state).offset > indented) {\n        if (top(state).type != \"py\") return true;\n        state.scopes.pop();\n      }\n      return top(state).offset != indented;\n    }\n\n    function tokenLexer(stream, state) {\n      if (stream.sol()) state.beginningOfLine = true;\n\n      var style = state.tokenize(stream, state);\n      var current = stream.current();\n\n      // Handle decorators\n      if (state.beginningOfLine && current == \"@\")\n        return stream.match(identifiers, false) ? \"meta\" : py3 ? \"operator\" : ERRORCLASS;\n\n      if (/\\S/.test(current)) state.beginningOfLine = false;\n\n      if ((style == \"variable\" || style == \"builtin\")\n          && state.lastToken == \"meta\")\n        style = \"meta\";\n\n      // Handle scope changes.\n      if (current == \"pass\" || current == \"return\")\n        state.dedent += 1;\n\n      if (current == \"lambda\") state.lambda = true;\n      if (current == \":\" && !state.lambda && top(state).type == \"py\")\n        pushPyScope(state);\n\n      if (current.length == 1 && !/string|comment/.test(style)) {\n        var delimiter_index = \"[({\".indexOf(current);\n        if (delimiter_index != -1)\n          pushBracketScope(stream, state, \"])}\".slice(delimiter_index, delimiter_index+1));\n\n        delimiter_index = \"])}\".indexOf(current);\n        if (delimiter_index != -1) {\n          if (top(state).type == current) state.indent = state.scopes.pop().offset - hangingIndent\n          else return ERRORCLASS;\n        }\n      }\n      if (state.dedent > 0 && stream.eol() && top(state).type == \"py\") {\n        if (state.scopes.length > 1) state.scopes.pop();\n        state.dedent -= 1;\n      }\n\n      return style;\n    }\n\n    var external = {\n      startState: function(basecolumn) {\n        return {\n          tokenize: tokenBase,\n          scopes: [{offset: basecolumn || 0, type: \"py\", align: null}],\n          indent: basecolumn || 0,\n          lastToken: null,\n          lambda: false,\n          dedent: 0\n        };\n      },\n\n      token: function(stream, state) {\n        var addErr = state.errorToken;\n        if (addErr) state.errorToken = false;\n        var style = tokenLexer(stream, state);\n\n        if (style && style != \"comment\")\n          state.lastToken = (style == \"keyword\" || style == \"punctuation\") ? stream.current() : style;\n        if (style == \"punctuation\") style = null;\n\n        if (stream.eol() && state.lambda)\n          state.lambda = false;\n        return addErr ? style + \" \" + ERRORCLASS : style;\n      },\n\n      indent: function(state, textAfter) {\n        if (state.tokenize != tokenBase)\n          return state.tokenize.isString ? CodeMirror.Pass : 0;\n\n        var scope = top(state), closing = scope.type == textAfter.charAt(0)\n        if (scope.align != null)\n          return scope.align - (closing ? 1 : 0)\n        else\n          return scope.offset - (closing ? hangingIndent : 0)\n      },\n\n      electricInput: /^\\s*[\\}\\]\\)]$/,\n      closeBrackets: {triples: \"'\\\"\"},\n      lineComment: \"#\",\n      fold: \"indent\"\n    };\n    return external;\n  });\n\n  CodeMirror.defineMIME(\"text/x-python\", \"python\");\n\n  var words = function(str) { return str.split(\" \"); };\n\n  CodeMirror.defineMIME(\"text/x-cython\", {\n    name: \"python\",\n    extra_keywords: words(\"by cdef cimport cpdef ctypedef enum except \"+\n                          \"extern gil include nogil property public \"+\n                          \"readonly struct union DEF IF ELIF ELSE\")\n  });\n\n});\n\n\n//# sourceURL=webpack:///./node_modules/codemirror/mode/python/python.js?");

/***/ })

}]);