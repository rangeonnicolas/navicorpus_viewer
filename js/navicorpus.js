//Well.. this code is awful... I will do it better very (very very very) soon...

//todo: variables globales a mettre ds le namespace :
var keyWordNavigation = true;
var dragOnJoys = false;
var keywordsMouseOverList = []; ;
var disp_occur_timeout = null;
var textNavHeight = 180;

///Todo: DECOMMENTER LE RETURN SI IDENTICAL AU DEBUT DE TRACEMAP

var hyerarScroll = 2;
var hyerarSeuil = 3;
var relatedKW = "";
var wordList = []; ;

function chargerCorpus(corpus, corporaPath) {
    jQuery.ajax({
        url: pathJoin([corporaPath,corpus,'config.js']),
        async: false,
        dataType: "script"
    });
    jQuery.ajax({
        url: pathJoin([corporaPath,corpus,'config2.js']),
        async: false,
        dataType: "script"
    });
}

function serialize(mixed_value) {
    //  discuss at: http://phpjs.org/functions/serialize/
    // original by: Arpad Ray (mailto:arpad@php.net)
    // improved by: Dino
    // improved by: Le Torbi (http://www.letorbi.de/)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // bugfixed by: Andrej Pavlovic
    // bugfixed by: Garagoth
    // bugfixed by: Russell Walker (http://www.nbill.co.uk/)
    // bugfixed by: Jamie Beck (http://www.terabit.ca/)
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // bugfixed by: Ben (http://benblume.co.uk/)
    //    input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
    //    input by: Martin (http://www.erlenwiese.de/)
    //        note: We feel the main purpose of this function should be to ease the transport of data between php & js
    //        note: Aiming for PHP-compatibility, we have to translate objects to arrays
    //   example 1: serialize(['Kevin', 'van', 'Zonneveld']);
    //   returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
    //   example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
    //   returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'

    var val, key, okey,
        ktype = '',
        vals = '',
        count = 0,
        _utf8Size = function(str) {
            var size = 0,
                i = 0,
                l = str.length,
                code = '';
            for (i = 0; i < l; i++) {
                code = str.charCodeAt(i);
                if (code < 0x0080) {
                    size += 1;
                } else if (code < 0x0800) {
                    size += 2;
                } else {
                    size += 3;
                }
            }
            return size;
        },
        _getType = function(inp) {
            var match, key, cons, types, type = typeof inp;

            if (type === 'object' && !inp) {
                return 'null';
            }

            if (type === 'object') {
                if (!inp.constructor) {
                    return 'object';
                }
                cons = inp.constructor.toString();
                match = cons.match(/(\w+)\(/);
                if (match) {
                    cons = match[1].toLowerCase();
                }
                types = ['boolean', 'number', 'string', 'array'];
                for (key in types) {
                    if (cons == types[key]) {
                        type = types[key];
                        break;
                    }
                }
            }
            return type;
        },
        type = _getType(mixed_value);

    switch (type) {
        case 'function':
            val = '';
            break;
        case 'boolean':
            val = 'b:' + (mixed_value ? '1' : '0');
            break;
        case 'number':
            val = (Math.round(mixed_value) == mixed_value ? 'i' : 'd') + ':' + mixed_value;
            break;
        case 'string':
            val = 's:' + _utf8Size(mixed_value) + ':"' + mixed_value + '"';
            break;
        case 'array':
        case 'object':
            val = 'a';
            for (key in mixed_value) {
                if (mixed_value.hasOwnProperty(key)) {
                    ktype = _getType(mixed_value[key]);
                    if (ktype === 'function') {
                        continue;
                    }

                    okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                    vals += this.serialize(okey) + this.serialize(mixed_value[key]);
                    count++;
                }
            }
            val += ':' + count + ':{' + vals + '}';
            break;
        case 'undefined':
            // Fall-through
        default:
            // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
            val = 'N';
            break;
    }
    if (type !== 'object' && type !== 'array') {
        val += ';';
    }
    return val;
}

/*
 * cloning funtion
 * @author Keith Devens
 * @see http://keithdevens.com/weblog/archive/2007/Jun/07/javascript.clone
 */
function clone(srcInstance) {
    /*Si l'instance source n'est pas un objet ou qu'elle ne vaut rien c'est une feuille donc on la retourne*/
    if (typeof(srcInstance) != 'object' || srcInstance == null) {
        return srcInstance;
    }
    /*On appel le constructeur de l'instance source pour crée une nouvelle instance de la même classe*/
    var newInstance = srcInstance.constructor();
    /*On parcourt les propriétés de l'objet et on les recopies dans la nouvelle instance*/
    for (var i in srcInstance) {
        newInstance[i] = clone(srcInstance[i]);
    }
    /*On retourne la nouvelle instance*/
    return newInstance;
}


/*
 * 
 *
 *
 */

function includeJs(file) { //todo: is this function useful?
    var script = document.createElement('script');
    script.setAttribute('type', 'text/javascript');
    script.setAttribute('src', file);
    document.getElementsByTagName('head')[0].appendChild(script);
}

function sendRequest0() {

    $.ajax({
        url: "./wordreq.php",
        type: "POST",
        dataType: "text",
        data: "corpus=" + corpus + "&type=newAccess&list=" + serialize(GexfJS.graph.nodeIndexByLabel),
        success: function(data) {
            if (String.substring(data, 0, 12) == "_code_erreur") {
                alert("Database connection seems to be unavailable or corrupted...\nPlease report this bug to the webmaster.\nThanks!");
                console.log(data);
            }
        },
        error: function(data) {
            alert("Database connection seems to be unavailable...")
        }
    });
}

function sendRequest1(words) {
    $.ajax({
        url: "./wordreq.php",
        headers: {
            "charset": "UTF-8"
        },
        type: "POST",
        dataType: "text",
        data: "list=" + serialize(words) + "&corpus=" + corpus + "&type=texts",
        success: function(data) {
            var _tC = $("#textContent");
            _tC.html("<h3>Texts containing all the selected Keywords:</h3>" + data);
        },
        error: function(data) {
            var _tC = $("#textContent");
            _tC.html("<h3>Texts containing all the selected Keywords:</h3><label class=info>Connection failed.</label>");
        }
    });
}

function sendRequest2(words) {
    $.ajax({
        url: "./wordreq.php",
        headers: {
            "charset": "UTF-8"
        },
        type: "POST",
        dataType: "text",
        data: "list=" + serialize(words) + "&corpus=" + corpus + "&type=relatedKewWords",
        success: function(data) {
            relatedKW = data;
            displayLeftDatapannel(GexfJS.params.activeNode);
        },
        error: function(data) {
            relatedKW = "<label class=info>Connection failed.</label>";
            displayLeftDatapannel(GexfJS.params.activeNode);
        }
    });
}

function sendRequest3(doc, word, minEdgeWeight) {
    $.ajax({
        url: "./wordreq.php",
        headers: {
            "charset": "UTF-8"
        },
        type: "POST",
        dataType: "text",
        data: "doc=" + doc + "&word=" + word + "&corpus=" + corpus + "&type=word_pos&minweight=" + minEdgeWeight,
        success: function(data) {
            $("#occurencies_pannel").html("<center>" + data + "</center>");
        },
        error: function(data) {
            $("#occurencies_pannel").html("<center>No available data...</center>")
        }
    });
}

function derouler(show) {
    var _cG = $("#leftcolumn");
    var _tN = $("#textNavigation");
    var _sN = $("#SNButtons");

    if (show) {
        _tN.animate({
            height: textNavHeight + "px"
        }, function() {});
        _cG.animate({
            "left": "0px",
            "bottom": 6 + GexfJS.overviewHeight + "px"
        }, function() {
            $("#aUnfold").attr("class", "leftarrow");
        });
        _sN.animate({
            "bottom": (textNavHeight + 5) + "px"
        });
    } else {
        _tN.animate({
            height: "0px" 
        }, function() {});
        _cG.animate({
            "left": "-" + _cG.width() + "px",
            "bottom": 6 + GexfJS.overviewHeight + "px" 
        }, function() {
            $("#aUnfold").attr("class", "rightarrow");
        });
        _sN.animate({
            "bottom": "5px"
        });
    }
}

function startMoveJoys(evt) {
    evt.defaultPrevented;
    dragOnJoys = true;
    lastMouseJoys = {
        x: evt.pageX,
        y: evt.pageY
    }
    mouseHasMovedJoys = false;
}

function keywordMouseOver(id) {

    var _label = $("#" + id).html();
    keywordsMouseOverList = []; ;

    _test = $("#textnav_1");
    for (var _i = 1; _test.length > 0; _i++) {
        _test = $("#textnav_" + _i);
        if (_test.html() == _label) {
            _test.attr("class", "keywordSmallMouseOverTextnav");
            keywordsMouseOverList.push("#textnav_" + _i);
        }
    }
    _test = $("#relatedkw_1");
    for (var _i = 1; _test.length > 0; _i++) {
        _test = $("#relatedkw_" + _i);
        if (_test.html() == _label) {
            _test.attr("class", "keywordSmallMouseOverRelatkw");
            keywordsMouseOverList.push("#relatedkw_" + _i);
        }
    }
}

function keywordMouseOut() {
    for (var _i = 0; _i < keywordsMouseOverList.length; _i++) {
        $(keywordsMouseOverList[_i]).attr("class", "keywordSmall");
    }
    keywordsMouseOverList = []; ;
}

function display_occurencies(doc, word) {
    $("#occurencies_bckgnd").show();
    $("#occurencies_pannel").html("<center>Please wait...</center>");

    disp_occur_timeout = setTimeout(function() {
        sendRequest3(doc, word, GexfJS.minEdgeWeight)
    }, 500);
}

function hide_occurencies(word) {
    clearInterval(disp_occur_timeout);
    $("#occurencies_bckgnd").hide();
}

// Affichage de l'ecran explicatif à la premiere visite
function display_howtouse() {
    $("#howtouse_pannel").html("<center><h1>How to use?</h1> <br>fff<> </center>");
    alert("ll");
}

function endMoveJoys(evt) {
    document.body.style.cursor = "default";
    dragOnJoys = false;
    mouseHasMovedJoys = false;
}

function onJoysticClick(evt) {
    if (!mouseHasMovedJoys) {

    }
    endMoveJoys();
}

function recentreAndUpdateSearch(_label, _recentre) {
    _nodeIndex = getNodeFromLabel(_label);
    GexfJS.params.currentNode = _nodeIndex;
    if (_nodeIndex != -1) {
        var _d = GexfJS.graph.nodeList[_nodeIndex],
            _b = _d.coords.base;
        if (_recentre) {
            GexfJS.params.centreX = _b.x;
            GexfJS.params.centreY = _b.y;
        }
        $("#searchinput")
            .val(_d.label)
            .removeClass('grey');
    }
}

function getNodeFromLabel(_label) {
    for (var i = GexfJS.graph.nodeList.length - 1; i >= 0; i--) {
        var _d = GexfJS.graph.nodeList[i];
        if (_d.visible) {
            if (_d.label == _label) {
                return i;
            }
        }
    }
    return -1;
}

function addKeyWordAndUpdateView(_kw, _recentre) {
    addToWordList(_kw);
    displayLeftDatapannel(GexfJS.params.activeNode);
    displayBottomDatapannel();
    recentreAndUpdateSearch(_kw, _recentre);
}

function removeKeyWordAndUpdateView(_kw) {
    removeFromWordList(_kw);
    displayLeftDatapannel(GexfJS.params.activeNode);
    displayBottomDatapannel();
}

function addToWordList(_kw) {
    var _dejaPresent = false;
    for (var i in wordList) {
        if (_kw == wordList[i]) {
            _dejaPresent = true;
        }
    }
    if (!_dejaPresent)
        wordList[wordList.length] = _kw;
}

function removeFromWordList(_kw) {
    for (var i in wordList) {
        if (_kw == wordList[i]) {
            wordList[i] = wordList[wordList.length - 1];
            wordList.pop();
        }
    }
}

function displayBottomDatapannel() {
    _tC = $("#textContent");
    _tC.html("Processing...");

    relatedKW = "No related keyword.";
    sendRequest1(wordList);
    sendRequest2(wordList);

    derouler(true);
}

function displayLeftDatapannel(_nodeIndex, _recentre) {
    if (keyWordNavigation) {
        derouler(true);
        displayKeyWords(_nodeIndex, _recentre);
    } else {
        displayNodeCharac(_nodeIndex, _recentre);
    }
}

function displayKeyWords(_nodeIndex, _recentre) {

    GexfJS.params.currentNode = _nodeIndex;

    wordList = wordList.sort();

    _str = '';


    _str += '<h3>Selected Keywords:</h3><br>'; // todo: modifier langue

    for (i in wordList) {
        _kw = wordList[i];
        _str += '<label class="keyword">' + _kw + '</label>';
        _str += '<a href=# onclick="removeKeyWordAndUpdateView(\'' + _kw + '\')"><label class="keywordDelete">x</label></a><br></br>';
    }

    _str += '<br><h4>Other suggestions:</h4><label class=info>(ie: unselected keywords which occur in the texts at the bottom of this page)</label><br><br>' + relatedKW;

    $("#leftcontent").html(_str);
}


function onJoysticMove(evt) {
    evt.defaultPrevented;

    if (dragOnJoys) {

        document.body.style.cursor = "n-resize";

        var _left;

        _height = $(document).height() - evt.pageY;
        if ((evt.pageY > 50) & ($(document).height() - evt.pageY > 10)) {

            $("#textNavigation").css({
                height: $(document).height() - evt.pageY + "px"
            });

            textNavHeight = $("#textNavigation").height(); //surement innutile maintenant
        }

        mouseHasMovedJoys = true;
    }
}

function howtouse_screen1(){
	content = "\
		<center><h2>How to use it?<h2></center>\
		<center><h2><a href='javascript:howtouse_screen2()'>Step 1: Select one keyword on the map :</a></h2></center>\
		<div id=\"next\"><a  href='javascript:howtouse_screen2()'>Next >>\ \ \ </a></div>\
		<center><a href='javascript:howtouse_screen2()'><img height=450px src='./img/1_2.png' alt='img' /></a></center>  "
	$("#howtouse_pannel").html(content)
	$("#next").css({"text-align":"right","font-size":"1.5em"});
}

function howtouse_screen2(){
	content = "\
		<center><h2>How to use it?<h2></center>\
		<center><h2><a href='javascript:howtouse_screen3()'>Step 2: See others suggested keywords:</a></h2></center>\
		<div id=\"next\"><a  href='javascript:howtouse_screen3()'>Next >>\ \ \ </a></div>\
		<center><a href='javascript:howtouse_screen3()'><img height=450px src='./img/2_2.png' alt='img' /></a></center>  "
	$("#howtouse_pannel").html(content)
	$("#next").css({"text-align":"right","font-size":"1.5em"});
}

function howtouse_screen3(){
	content = "\
		<center><h2>How to use it?<h2></center>\
		<center><h2><a href='javascript:$(\"#howtouse_bckgnd\").hide();docready()'>Step 3: Place your cursor over the keywords to browse texts :</a></h2></center>\
		<div id=\"next\"><a  href='javascript:$(\"#howtouse_bckgnd\").hide();docready()'>Finish >>\ \ \ </a></div>\
		<center><a href='javascript:$(\"#howtouse_bckgnd\").hide();docready()'><img height=450px src='./img/3_2.png' alt='img' /></a></center>  "
	$("#howtouse_pannel").html(content)
	$("#next").css({"text-align":"right","font-size":"1.5em"});
}
