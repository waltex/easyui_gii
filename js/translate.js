var g_lng = [];
var g_param;
$(document).ready(function () {
    $.getJSON('app_setting.json', {_: new Date().getTime()}, function (data) {
        g_param = data;
        //find all language json dictionary
        $.getJSON('language/' + g_param["language from translate"] + '2' + g_param["language to translate"] + '.json', {_: new Date().getTime()})
                .done(function (data) {
                    var lng = g_param["language from translate"] + "2" + g_param["language to translate"];
                    g_lng[lng] = data;//store language dictionary to global array
                    init_app();
                })
                .fail(function (data) {
                    var lng = g_param["language from translate"] + "2" + g_param["language to translate"];
                    g_lng[lng] = [];//store language dictionary to global array
                    init_app();
                });
    });
});
// Translate multiple language
function T(value) {
    var t = (g_param["language debug"]) ? g_param["language debug success"] : ""; //traslate debug
    var e = (g_param["language debug"]) ? g_param["language debug error"] : ""; // error traslate debug
    var lng = g_param["language from translate"] + "2" + g_param["language to translate"];
    // check if exist data language on array
    if (g_lng[lng][value] === undefined) {
        //auto translate language with google api or add row to json for manual translate
        (g_param["auto translate"]) ? $.post('api/auto/translate', {value: value}) : false;
        return e + value + e;
    } else {
        return (g_param["language default"] != g_param["language from translate"]) ? t + g_lng[lng][value] + t : t + value + t;
    }
}
