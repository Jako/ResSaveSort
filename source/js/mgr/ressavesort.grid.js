ResSaveSort.grid.Systemsetting = function (config) {
    config = config || {};
    this.ident = config.ident || 'ressavesort-mecitem' + Ext.id();
    this.buttonColumnTpl = new Ext.XTemplate('<tpl for=".">'
        + '<tpl if="action_buttons !== null">'
        + '<ul class="action-buttons">'
        + '<tpl for="action_buttons">'
        + '<li><i class="icon {className} icon-{icon}" title="{text}"></i></li>'
        + '</tpl>'
        + '</ul>'
        + '</tpl>'
        + '</tpl>', {
        compiled: true
    });
    this.hiddenField = new Ext.form.TextArea({
        name: config.hiddenName || config.name,
        hidden: true
    });
    Ext.applyIf(config, {
        id: this.ident + '-systemsetting-grid',
        fields: ['id', 'sortby', 'sortdir', 'sortcontainer', 'rank'],
        autoHeight: true,
        store: new Ext.data.JsonStore({
            fields: ['id', 'sortby', 'sortdir', 'sortcontainer', 'rank'],
            data: Ext.util.JSON.decode(config.value)
        }),
        enableDragDrop: true,
        ddGroup: this.ident + '-systemsetting-grid-dd',
        autoExpandColumn: 'value',
        labelStyle: 'position: absolute',
        columns: [{
            header: _('ressavesort.sortby'),
            dataIndex: 'sortby',
            editable: true,
            editor: {
                xtype: 'textfield',
                allowBlank: false,
                listeners: {
                    change: {
                        fn: this.saveValue,
                        scope: this
                    }
                }
            },
            width: 100
        }, {
            header: _('ressavesort.sortdir'),
            dataIndex: 'sortdir',
            editable: true,
            editor: {
                xtype: 'textfield',
                listeners: {
                    change: {
                        fn: this.saveValue,
                        scope: this
                    }
                }
            },
            width: 100
        }, {
            header: _('ressavesort.sortcontainer'),
            dataIndex: 'sortcontainer',
            editable: true,
            editor: {
                xtype: 'textfield',
                listeners: {
                    change: {
                        fn: this.saveValue,
                        scope: this
                    }
                }
            },
            width: 100
        }, {
            renderer: {
                fn: this.buttonColumnRenderer,
                scope: this
            },
            width: 30,
            align: 'right'
        }, {
            dataIndex: 'rank',
            hidden: true
        }, {
            dataIndex: 'id',
            hidden: true
        }],
        tbar: ['->', {
            text: '<i class="icon icon-plus"></i> ' + _('add'),
            cls: 'primary-button',
            handler: this.addEntry,
            scope: this
        }],
        listeners: {
            render: {
                fn: this.renderListener,
                scope: this
            }
        }
    });
    ResSaveSort.grid.Systemsetting.superclass.constructor.call(this, config)
};
Ext.extend(ResSaveSort.grid.Systemsetting, MODx.grid.LocalGrid, {
    windows: {},
    getMenu: function () {
        var m = [];
        m.push({
            text: _('remove'),
            handler: this.removeEntry
        });
        return m;
    },
    addEntry: function () {
        var ds = this.getStore();
        var r = new ds.recordType({
            targetwidth: '',
            targetheight: '',
            targetRatio: ''
        });
        this.getStore().insert(0, r);
        this.getView().refresh();
        this.getSelectionModel().selectRow(0);
    },
    removeEntry: function () {
        Ext.Msg.confirm(_('remove') || '', _('confirm_remove') || '', function (e) {
            if (e === 'yes') {
                var ds = this.getStore();
                var rows = this.getSelectionModel().getSelections();
                if (!rows.length) {
                    return false;
                }
                for (var i = 0; i < rows.length; i++) {
                    var id = rows[i].id;
                    var index = ds.findBy(function (record, id) {
                        if (record.id === id) {
                            return true;
                        }
                    });
                    ds.removeAt(index);
                }
                this.getView().refresh();
                this.saveValue();
            }
        }, this);
    },
    renderListener: function (grid, a, b, c) {
        if (!(grid.container instanceof Ext.Layer)) {
            new Ext.dd.DropTarget(grid.container, {
                copy: false,
                ddGroup: this.ident + '-systemsetting-grid-dd',
                notifyDrop: function (dd, e, data) {
                    var ds = grid.store;
                    var sm = grid.getSelectionModel();
                    var rows = sm.getSelections();

                    var dragData = dd.getDragData(e);
                    if (dragData) {
                        var cindex = dragData.rowIndex;
                        if (typeof (cindex) !== "undefined") {
                            for (var i = 0; i < rows.length; i++) {
                                ds.remove(ds.getById(rows[i].id));
                            }
                            ds.insert(cindex, data.selections);
                            sm.clearSelections();
                        }
                    }
                    grid.getView().refresh();
                    grid.saveValue();
                }
            });
            this.add(this.hiddenField);
            this.saveValue();
        } else {
            grid.container.addListener('beforestartedit', function () {
                return false;
            });
            console.log(grid);
            return false;
        }
    },
    buttonColumnRenderer: function () {
        var values = {
            action_buttons: [{
                className: 'remove',
                icon: 'trash-o',
                text: _('remove')
            }]
        };
        return this.buttonColumnTpl.apply(values);
    },
    onClick: function (e) {
        var t = e.getTarget();
        var elm = t.className.split(' ')[0];
        if (elm === 'icon') {
            var act = t.className.split(' ')[1];
            var record = this.getSelectionModel().getSelected();
            this.menu.record = record.data;
            switch (act) {
                case 'remove':
                    this.removeEntry(record, e);
                    break;
                default:
                    break;
            }
        }
    },
    saveValue: function () {
        var value = [];
        Ext.each(this.getStore().getRange(), function (record) {
            value.push({
                sortby: record.data.sortby,
                sortdir: record.data.sortdir,
                sortcontainer: record.data.sortcontainer
            });
        });
        this.hiddenField.setValue(Ext.util.JSON.encode(value));
    }
});
Ext.reg('ressavesort-systemsetting-grid', ResSaveSort.grid.Systemsetting);
