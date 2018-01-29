let Cookie = {
    /**
     * 设置cookie数据
     * @param name
     * @param value
     * @param expire  过期时间，单位为秒
     * @constructor
     */
    Set: function (name, value, expire = 0) {
        let expireStr = '';
        if (expire) {
            let date = new Date();
            date.setTime(date.getTime() + expire * 1000);
            expireStr = "expires=" + date.toUTCString() + ";";
        }
        document.cookie = name + "=" + value + ";" + expireStr;
    },

    /**
     * 获取cookie数据
     * @param name
     * @constructor
     */
    Get: function (name) {
        let getCookie = document.cookie.replace(/[ ]/g, '');
        let resArr = getCookie.split(';');
        let length = resArr.length;
        let res = null;
        for (let i = 0; i <= length; i++) {
            let arr = resArr[i].split('=');
            if (arr[0] === name) {
                res = arr[1];
                break;
            }
        }
        return res;
    },

    /**
     * 删除cookie
     * @param name
     * @constructor
     */
    Remove: function (name) {
        let date = new Date();
        date.setTime(date.getTime() - 1);
        document.cookie = name + "=v; expires =" + date.toUTCString();
    },

    /**
     * 删除所有cookie
     * @constructor
     */
    RemoveAll: function () {
        let getCookie = document.cookie.replace(/[ ]/g, '');
        let resArr = getCookie.split(';');
        let length = resArr.length;
        for (let i = 0; i <= length; i++) {
            let arr = resArr[i].split('=');
            this.Remove(arr[0]);
        }
    }
};