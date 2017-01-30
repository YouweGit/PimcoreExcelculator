Ext.define('PimcoreExcelculatorServerTestClass', {

    panel: false,
    progressString: 'Running...',

    constructor: function() {

    },

    init: function() {
        if(!this.panel) {
            this.openServerTestView();
        }
        else {
            var panelTab = Ext.getCmp('pimcore_panel_tabs');
            panelTab.setActiveTab('excel_server_test');
        }
    },

    openServerTestView: function () {
        this.timestamp = Ext.create('Ext.toolbar.TextItem', {
            html: ''
        });

        this.panel = Ext.create('Ext.panel.Panel', {
            title: 'Excelculator ' + t('excelculator_server_test'),
            id: 'excel_server_test',
            padding: 6,
            html: this.progressString,
            tbar: {
                items: [
                    new Ext.Button({
                        text:  'Re-run test',
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
        panelTab.setActiveTab('excel_server_test');

        this.refresh();
    },

    refresh: function () {

        this.panel.setHtml(this.progressString);

        Ext.Ajax.request({
            url: '/plugin/PimcoreExcelculator/index/test',
            params: {
            },
            success: function(response, opts) {
                var obj = Ext.decode(response.responseText);

                this.panel.setHtml(obj.data);
                this.timestamp.setHtml('Last run: ' + obj.timestamp);

            }.bind(this)
        });

    }

});

var PimcoreExcelculatorServerTest = Ext.create('PimcoreExcelculatorServerTestClass');
