//定义操作js公共路径
const SCOPE = {
    'upload_url': '/index.php/admin/Common/upload', //上传文件路径
    'kindeditorupload_url': '/index.php/admin/Common/kindeditorupload' //kindeditor上传文件路径
};

/**
 * 上传图片配置信息
 * @type {{}}
 */
const config = {
    'height': 30, //设置按钮的高度(单位px)，默认为30.(不要在值里写上单位，并且要求一个整数，width也一样)
    'width': 120, //设置按钮宽度(单位px)，默认120
    'method': 'post', //提交上传文件的方法，接受post或get两个值，默认为post
    'auto': true, ////接受true or false两个值，当为true时选择文件后会自动上传；为false时只会把选择的文件增加进队列但不会上传，这时只能使用upload的方法触发上传。不设置auto时默认为true。
    'swf': '/public/js/uploadify/uploadify.swf',//swf的相对路径。
    'uploader': SCOPE.upload_url,////服务器端脚本文件路径。
    'buttonText': '选择图片',//设置按钮文字。
    'debug': false,//开启或关闭debug模式。
    'fileObjName': 'img',//设置在后台脚本使用的文件名。
    'fileSizeLimit': '2MB',//设置上传文件的容量最大值。这个值可以是一个数字或者字符串。如果是字符串，接受一个单位（B,KB,MB,GB）。如果是数字则默认单位为KB。设置为0时表示不限制。
    'fileTypeExts': '*.gif; *.jpg; *.png; *.jpeg',//设置允许上传的文件扩展名（也就是文件类型）。
};

/**
 * uploadify异步上传多图片封装
 * @param ids  文本域集合ID
 * @param compressImage  是否等比例压缩图片
 * @param addTextmark  是否添加文字水印
 */
function multiple_upload(ids = ['imgs'], compressImage = false, addTextmark = false) {
    let length = ids.length;
    for (let i = 0; i < length; i++) {
        $('#' + ids[i]).uploadify({
            ...config,
            'formData': {id: ids[i], compressImage: compressImage, addTextmark: addTextmark},//通过get或post上传文件时，此对象提供额外的数据。
            'multi': true, //设置是否允许一次选择多个文件，true为允许，false不允许
            'uploadLimit': '5',//上传文件的数量。
            'onUploadSuccess': function (file, data) {
                //file 已成功上传的文件对象信息(类似于php $_FILES)
                //data 由服务器端脚本返回的数据(接口json返回格式数据）
                let ret = JSON.parse(data); //由JSON字符串转换为JSON对象
                if (ret.error === 1) {
                    //普通字符串处理
                    $('.uploadify-button-text').html(' 上传完毕');

                    $this = $('.picPreview_' + ret.data.id);
                    $this.append(
                        `
                        <div style="display: inline-block">
                            <div>
                                <img src='${ret.data.src}' width='150' height='150'/>
                                <input type='hidden' name='${ret.data.id}[]' value='${ret.data.src}'/>
                            </div>
                            <div style="text-align: center">
                                <a onclick="$(this).parent().parent().remove()">删除</a>
                            </div>
                        </div>
                    `
                    );
                } else {
                    return dialog.error(ret.etext);
                }

            }
        });
    }
}


/**
 * uploadify异步上传单图片封装
 * @param id  文本域ID
 * @param compressImage  是否等比例压缩图片
 * @param addTextmark  是否添加文字水印
 */
function upload(id = 'img', compressImage = false, addTextmark = false) {
    $('#' + id).uploadify({
        ...config,
        'formData': {id: id, compressImage: compressImage, addTextmark: addTextmark},//通过get或post上传文件时，此对象提供额外的数据。
        'multi': false, //设置是否允许一次选择多个文件，true为允许，false不允许
        'onUploadSuccess': function (file, data) {
            //file 已成功上传的文件对象信息(类似于php $_FILES)
            //data 由服务器端脚本返回的数据(接口json返回格式数据）
            let ret = JSON.parse(data); //由JSON字符串转换为JSON对象
            if (ret.error === 1) {
                //普通字符串处理
                $('.uploadify-button-text').html(' 上传完毕');

                $this = $('.picPreview_' + ret.data.id);
                $this.append(
                    `
                        <div style="display: inline-block">
                            <div>
                                <img src='${ret.data.src}' width='150' height='150'/>
                                <input type='hidden' name='${ret.data.id}' value='${ret.data.src}'/>
                            </div>
                            <div style="text-align: center">
                                <a onclick="$(this).parent().parent().remove()">删除</a>
                            </div>
                        </div>
                    `
                );
            } else {
                return dialog.error(ret.etext);
            }

        }
    });
}