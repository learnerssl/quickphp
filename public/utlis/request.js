/**
 * 网络请求js（依赖jquery）
 */

let request = {
    /**
     * 序列化提交表单数据
     * @param url
     * @param callback
     * @param id
     */
    apiPost: (url, callback = null, id = "FormId") => {
        //获取表单数据
        let data = $("#" + id).serializeArray();

        //发送ajax请求
        $.post(url, data, function (result) {
            if (result.error === 0) {
                //如果存在回调函数，则执行
                if (typeof callback === 'function') callback();
                return dialog.success(result.etext, result.data.url);
            } else {
                return dialog.error(result.etext);
            }
        }, 'json');
    },

    /**
     * 页面重定向
     * @param url 重定向路径
     * @param data get参数(json格式)
     * @returns {*|void}
     */
    Redirect: (url, data = '') => {
        if (url === null || url === '') {
            return dialog.error('请传入有效地址!');
        }
        let param = '?';
        $.each(data, function (index, value) {
            if (value !== '') {
                param += index + "=" + value + "&";
            }
        });
        param = param.substring(0, param.length - 1);
        window.location.href = url + param;
    },

    /**
     * ajax请求数据
     * @param url  api地址
     * @param data  post提交参数数据 json
     * @param id    替换的文本域id
     */
    getData: (url, data, id = 'html') => {
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'text',
            success: function (result) {
                if (!result.match("^\{(.+:.+,*){1,}\}$")) {
                    //普通字符串处理
                    if (id === 'html') {
                        $(document).find("html").html(result);
                    } else {
                        $('#' + id).html(result);
                    }
                } else {
                    //将json字符串
                    return dialog.error(result.etext);
                }
            }
        });
    },

    /**
     * 数据提交操作
     * @param url  api地址
     * @param data post提交参数数据(json格式)
     * @param tid 操作成功后跳转方式
     * @param callback 回调函数
     * @param dom DOM操作对象
     */
    subData: (url, data, tid = 1, callback = null, dom = null) => {
        //解构
        const {message} = data;
        if (message) {
            layer.open({
                content: message,
                icon: 3,
                btn: ['是', '否'],
                yes: () => {
                    exec(url, data, tid, callback, dom);
                }
            });
        } else {
            exec(url, data, tid, callback, dom);
        }

        function exec(url, data, tid, callback, dom) {
            $.post(url, data, function (result) {
                if (result.error === 0) {
                    if (typeof callback === 'function') callback(dom);
                    switch (tid) {
                        case 1:
                            return dialog.success(result.etext, result.data.url);
                        case 2:
                            return window.location.reload();
                        case 3:
                            return dialog.toconfirm(result.etext);
                        case 4:
                            return window.location.href = result.data.url;
                        default:
                            return dialog.error('无效的跳转方式！');
                    }
                }
                return dialog.error(result.etext);
            }, 'json');
        }
    }
};






