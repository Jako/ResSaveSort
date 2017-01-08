/**
 * ResSaveSort Custom Manager Page Script
 *
 * Copyright 2013-2017 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package ressavesort
 * @subpackage script
 */

var resSaveSort = function (config) {
    config = config || {};
    resSaveSort.superclass.constructor.call(this, config);
};

Ext.extend(resSaveSort, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, jquery: {}, form: {}
});
Ext.reg('ressavesort', resSaveSort);

ResSaveSort = new resSaveSort();
