var resSaveSort = function (config) {
    config = config || {};
    resSaveSort.superclass.constructor.call(this, config);
};

Ext.extend(resSaveSort, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, jquery: {}, form: {}
});
Ext.reg('ressavesort', resSaveSort);

ResSaveSort = new resSaveSort();
