define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/bank/index',
                    add_url: 'user/bank/add',
                    edit_url: 'user/bank/edit',
                    del_url: 'user/bank/del',
                    multi_url: 'user/bank/multi',
                    table: 'user_bank',
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
                        {field: 'id', title: __('Id'),sortable: true},
                        {field: 'user.mobile', title: __('User.mobile')},
                        {field: 'user_id', title: __('User_id'),visible: false},
                        {field: 'name', title: __('Name')},
                        {field: 'bankname', title: __('Bankname')},
                        {field: 'banklocation', title: __('Banklocation')},
                        {field: 'bankcard', title: __('Bankcard')},
                        {field: 'is_del', title: __('Is_del'), searchList: {"0":__('Is_del 0'),"1":__('Is_del 1')}, formatter: Table.api.formatter.normal},
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