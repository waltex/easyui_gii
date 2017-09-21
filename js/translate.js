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
        $.getScript('lib/easyui_1.5.1/locale/easyui-lang-' + g_param["lingua corrente"] + '.js')
                .fail(function (jqxhr, settings, exception) {
                    $.getScript('lib/easyui_1.5.1/locale/easyui-lang-en.js')
                });
        //find all language json dictionary
        $.getJSON('language/' + g_param["traduci dalla lingua"] + '2' + g_param["traduci alla lingua"] + '.json', {_: new Date().getTime()})
                .done(function (data) {
                    var lng = g_param["traduci dalla lingua"] + "2" + g_param["traduci alla lingua"];
                    g_lng[lng] = data;//store language dictionary to global array
                    init_app();
                })
                .fail(function (data) {
                    var lng = g_param["traduci dalla lingua"] + "2" + g_param["traduci alla lingua"];
                    g_lng[lng] = [];//store language dictionary to global array
                    init_app();
                });
    });
});
// Translate multiple language
function T(value) {
    var t = (g_param["debug traduzione"]) ? g_param["debug traduzione OK"] : ""; //traslate debug string
    var e = (g_param["debug traduzione"]) ? g_param["debug traduzione KO"] : ""; // traslate debug error string
    var lng = g_param["traduci dalla lingua"] + "2" + g_param["traduci alla lingua"];
    // check if exist data language on array
    if (g_lng[lng][value] === undefined) {
        //auto translate language with google api or add row to json for manual translate
        (g_param["traduci in automatico con google"]) ? $.post('api/auto/translate', {value: value}) : false;
        return e + value + e;
    } else {
        return (g_param["lingua corrente"] != g_param["traduci dalla lingua"]) ? t + g_lng[lng][value] + t : t + value + t;
    }
}
