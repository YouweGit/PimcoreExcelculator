pimcore.registerNS("pimcore.plugin.pimcoreExcelculator");

pimcore.plugin.pimcoreExcelculator = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.pimcoreExcelculator";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        this.addMenuItems();
    },

    addMenuItems: function () {
        var menuItems = [],
            user = pimcore.globalmanager.get('user');

        if (user.isAllowed('plugin_pimcoreexcelculator_manager')) {
            menuItems.push({
                text: t('excelculator_server_log'),
                iconCls: 'pimcore_icon_log',
                handler: this.openLogView.bind(this)
            });
        }

        if (user.isAllowed('plugin_pimcoreexcelculator_manager')) {
            menuItems.push({
                text: t('excelculator_server_status'),
                iconCls: 'pimcore_icon_log',
                handler: this.openServerStatus.bind(this)
            });
        }

        if (user.isAllowed('plugin_pimcoreexcelculator_manager')) {
            menuItems.push({
                text: t('excelculator_server_test'),
                iconCls: 'pimcore_icon_log',
                handler: this.openServerTest.bind(this)
            });
        }


        var toolbar = pimcore.globalmanager.get('layout_toolbar');

        if (menuItems.length > 0) {
            this.navEl = Ext.get('pimcore_menu_pimcoreExcelculator_plugin');
            if (!this.navEl) {
                this.navEl = Ext.get('pimcore_menu_search')
                    .insertSibling('<li id="pimcore_menu_pimcoreExcelculator_plugin" data-menu-tooltip="PimcoreExcelculatorPlugin" class="pimcore_menu_item"></li>', 'after');
            }
        }

        if (menuItems.length > 0) {
            var menu = new Ext.menu.Menu({
                cls: 'pimcore_navigation_flyout',
                items: menuItems
            });

            this.navEl.on('mouseover', toolbar.showSubMenu.bind(menu));
            this.navEl.on('mousedown', toolbar.showSubMenu.bind(menu));
        }
    },

    openLogView: function() {
        PimcoreExcelculatorLogView.init();
    },

    openServerStatus: function () {
        PimcoreExcelculatorServerStatus.init();
    },

    openServerTest: function () {
        PimcoreExcelculatorServerTest.init();
    }

});

var pimcoreExcelculatorPlugin = new pimcore.plugin.pimcoreExcelculator();

