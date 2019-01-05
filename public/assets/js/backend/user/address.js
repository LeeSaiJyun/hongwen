define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/address/index',
                    add_url: 'user/address/add',
                    edit_url: 'user/address/edit',
                    del_url: 'user/address/del',
                    multi_url: 'user/address/multi',
                    table: 'user_address',
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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'user_id', title: __('User_id'),visible: false},
                        {field: 'province.name', title: __('Province_id'),operate:false},
                        {field: 'city.name', title: __('City_id'),operate:false},
                        {field: 'area.name', title: __('Area_id'),operate:false},
                        {field: 'name', title: __('Name'),operate:"LIKE"},
                        {field: 'address', title: __('Address'),operate:"LIKE"},
                        {field: 'telephone', title: __('Telephone'),operate:"LIKE"},
                        {field: 'is_default', title: __('Is_default'),operate:false, searchList: {"0":__('Is_default 0'),"1":__('Is_default 1')}, formatter: Table.api.formatter.normal},
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