Ext.define('PimcoreExcelculatorServerStatusClass', {

    panel: false,
    progressString: 'Checking...',

    constructor: function() {

    },

    init: function() {
        if(!this.panel) {
            this.openServerStatusView();
        }
        else {
            var panelTab = Ext.getCmp('pimcore_panel_tabs');
            panelTab.setActiveTab('excel_server_status');
        }
    },

    openServerStatusView: function () {
        this.timestamp = Ext.create('Ext.toolbar.TextItem', {
            html: ''
        });

        this.panel = Ext.create('Ext.panel.Panel', {
            title: 'Excelculator ' + t('excelculator_server_status'),
            id: 'excel_server_status',
            padding: 6,
            html: this.progressString,
            tbar: {
                items: [
                    new Ext.Button({
                        text:  'Refresh status',
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
        panelTab.setActiveTab('excel_server_status');

        this.refresh();
    },

    refresh: function () {

        this.panel.setHtml(this.progressString);

        Ext.Ajax.request({
            url: '/plugin/PimcoreExcelculator/index/status',
            params: {
            },
            success: function(response, opts) {
                var obj = Ext.decode(response.responseText);

                this.panel.setHtml(obj.data.statusString);
                this.timestamp.setHtml('Last check: ' + obj.timestamp);

            }.bind(this)
        });

    }

});

var PimcoreExcelculatorServerStatus = Ext.create('PimcoreExcelculatorServerStatusClass');
