define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'deliverymaterial/index',
                    add_url: 'deliverymaterial/add',
                    edit_url: 'deliverymaterial/edit',
                    del_url: 'deliverymaterial/del',
                    multi_url: 'deliverymaterial/multi',
                    table: 'delivery_material',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'application_id', title: __('Application_id')},
                        {field: 'application.telephone', title: __('Application.telephone'),operate:false},
                        {field: 'area', title: __('Area')},
                        {field: 'name', title: __('Name')},
                        {field: 'address', title: __('Address')},
                        {field: 'telephone', title: __('Telephone')},
                        {field: 'remark', title: __('Remark')},
                        {field: 'ex_order', title: __('Ex_order')},
                        {field: 'ex_company', title: __('Ex_company')},
                        {field: 'status', title: __('Status'), searchList: {"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        // {field: 'application.id', title: __('Application.id')},
                        // {field: 'application.user_id', title: __('Application.user_id')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});