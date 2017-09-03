/**
 * Automatic Tranlsate Generator with google api, and local database language with auto populate
 * its easy to implement to your app, use function T("hello") output "ciao", and  use function inip_app() for init your code
 * declare first  "translate.js" and after your code "myscript.js"
 *      <script src="js/translate.js" type="text/javascript"></script>
 *      <script src="js/index.js" type="text/javascript"></script>
 *
 */
var g_lng = [];
var g_param;
$(document).ready(function () {
    $.getJSON('app_setting.json', {_: new Date().getTime()}, function (data) {
        g_param = data;
        //only translate framemork jquiery easyui
        $.getScript('lib/easyui_1.5.1/locale/easyui-lang-' + g_param["language default"] + '.js')
                .fail(function (jqxhr, settings, exception) {
                    $.getScript('lib/easyui_1.5.1/locale/easyui-lang-en.js')
                });
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
    var t = (g_param["language debug"]) ? g_param["language debug success"] : ""; //traslate debug string
    var e = (g_param["language debug"]) ? g_param["language debug error"] : ""; // traslate debug error string
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
