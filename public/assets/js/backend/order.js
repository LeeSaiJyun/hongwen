define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/index',
                    add_url: 'order/add',
                    edit_url: 'order/edit',
                    del_url: 'order/del',
                    multi_url: 'order/multi',
                    table: 'order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                // exportTypes: ['excel'],
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        // {field: 'user_id', title: __('User_id'),visible:false,operate:false},
                        {field: 'user.username', title: __('User.username'),operate:false},
                        {field: 'data.school', title: __('Data.school'),operate:false },
                        {field: 'data.major', title: __('Data.major'),operate:false },
                        {field: 'data.grade', title: __('Data.grade'),operate:false },
                        {field: 'orderno', title: __('Orderno'),operate:'like'},
                        {field: 'paymentdata', title: __('Paymentdata'), searchList: {"application":__('Application'),"tuition":__('Tuition')}, formatter: Table.api.formatter.normal},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'balance', title: __('Balance'), operate:'BETWEEN'},
                        {field: 'paytime', title: __('Paytime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"-1":__('Status -1')}, formatter: Table.api.formatter.status}
                    ]
                ],
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