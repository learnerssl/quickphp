let Storage = {
    /**
     * 设置缓存数据
     * @param name
     * @param value
     * @param expire  过期时间，单位为秒
     * @constructor
     */
    Set: function (name, value, expire = 0) {
        let item = {
            data: value,
            expireTime: new Date().getTime() + expire * 1000
        };
        localStorage.setItem(name, JSON.stringify(item));
    },
    /**
     * 获取缓存数据
     * @param name
     * @returns {null}
     * @constructor
     */
    Get: function (name) {
        let currentTime = new Date().getTime();
        let value = localStorage.getItem(name);
        if (!value) return null;
        let data = JSON.parse(value);
        if (data.expireTime < currentTime) {
            localStorage.removeItem(name);
            return null;
        }
        return data.data;
    },
    /**
     * 删除缓存数据
     * @param name
     * @constructor
     */
    Remove: function (name) {
        localStorage.removeItem(name);
    },
    /**
     * 删除所有缓存数据
     * @constructor
     */
    RemoveAll: function () {
        localStorage.clear();
    },
    /**
     * 返回指定的key名
     * @param i  从0开始
     * @returns {string}
     * @constructor
     */
    GetKey: function (i) {
        return localStorage.key(i);
    },
    /**
     * 获取缓存数量
     * @returns {number}
     * @constructor
     */
    GetLength: function () {
        return localStorage.length;
    }
};