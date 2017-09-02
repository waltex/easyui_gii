var g_lng = [];
var g_param;
$(document).ready(function () {
    $.getJSON('app_setting.json', {_: new Date().getTime()}, function (data) {
        g_param = data;
        //find all language json dictionary
        $.getJSON('language/en2' + g_param["language to translate"] + '.json', {_: new Date().getTime()})
                .done(function (data) {
                    var lng = "en2" + g_param["language to translate"];
                    g_lng[lng] = data;//store language dictionary to global array
                    init_app();
                });
    });
});
// Translate multiple language
function T(value) {
    var t = (g_param["language debug"]) ? "*" : ""; //traslate debug
    var e = (g_param["language debug"]) ? "*ERR*" : ""; // error traslate debug
    var lng = "en2" + g_param["language to translate"];
    // check if exist data language on array
    if (g_lng[lng] === undefined) {
        //auto translate language with google api or add row to json for manual translate
        (g_param["auto translate"]) ? $.post('auto/translate', {value: value}) : $.post('manual/translate', {value: value});
    } else {
        return t + g_lng[lng][value] + t;
    }
}
