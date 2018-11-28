define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'announcement/index',
                    add_url: 'announcement/add',
                    edit_url: 'announcement/edit',
                    del_url: 'announcement/del',
                    multi_url: 'announcement/multi',
                    table: 'announcement',
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
                        {field: 'admin_id', title: __('Admin_id'),visible:false,operate:false},
                        {field: 'remark', title: __('Remark')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Controller.api.formatter.custom},
                        {field: 'createtime', title: __('Createtime'),operate:false, formatter: Table.api.formatter.datetime },
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
            },
            formatter: {//渲染的方法
                custom: function (value, row, index) {
                    //添加上btn-change可以自定义请求的URL进行数据处理
                    return '<a class="btn-change text-success" data-url="announcement/change" data-id="' + row.id + '"><i class="fa ' + (row.status == 1 ? 'fa-toggle-on': 'fa-toggle-off' ) + ' fa-2x"></i></a>';
                },
            },

        }
    };
    return Controller;
});