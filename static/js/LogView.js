Ext.define('PimcoreExcelculatorLogViewClass', {

    panel: false,
    progressString: 'Loading...',

    constructor: function() {

    },

    init: function() {
        if(!this.panel) {
            this.openLogView();
        }
        else {
            var panelTab = Ext.getCmp('pimcore_panel_tabs');
            panelTab.setActiveTab('excel_log_view');
        }
    },

    openLogView: function () {
        this.timestamp = Ext.create('Ext.toolbar.TextItem', {
            html: ''
        });

        this.panel = Ext.create('Ext.panel.Panel', {
            scrollable: true,
            title: 'Excelculator ' + t('excelculator_server_log'),
            id: 'excel_log_view',
            padding: 6,
            html: this.progressString,
            tbar: {
                items: [
                    new Ext.Button({
                        text:  'Refresh',
                        width: 200,
                        scale: "medium",
                        handler : function() {
                            this.refresh();
                        }.bind(this)
                    }),
                    '->',
                    this.timestamp
                ]
            },
            closable: true,
            layout: 'anchor'
        });

        var panelTab = Ext.getCmp('pimcore_panel_tabs');
        panelTab.add(this.panel);
        panelTab.setActiveTab('excel_log_view');

        this.refresh();
    },

    refresh: function () {

        Ext.Ajax.request({
            url: '/plugin/PimcoreExcelculator/index/log',
            params: {
            },
            success: function(response, opts) {
                var obj = Ext.decode(response.responseText);

                this.panel.setHtml(obj.data);
                this.timestamp.setHtml('Last refresh: ' + obj.timestamp);

            }.bind(this)
        });

    }

});

var PimcoreExcelculatorLogView = Ext.create('PimcoreExcelculatorLogViewClass');
