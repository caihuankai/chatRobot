// 这是一个跨项目（admin|h5）公用的js文件
// include的file属性加载是相对于index.php文件

/**
 * 获取url参数
 * 使用   decodeURI(getQueryString(参数名))
 * @param name
 * @returns {null}
 */
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return decodeURIComponent((r[2])); return null;
}

/**
 * 获取固定格式的接口数据
 *
 * @param data
 * @param codeTrue
 * @param layerShow 是否直接显示弹窗
 * @returns {*}
 */
function getJsonData(data, codeTrue, layerShow) {
    codeTrue = codeTrue != null ? codeTrue : 200;
    if (layerShow){
        layer.msg(data.msg);
    }

    if (data.code == codeTrue){
        return data.data
    }else{ // 错误

        return null;
    }
}