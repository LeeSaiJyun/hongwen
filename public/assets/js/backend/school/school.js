define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'school/school/index',
                    add_url: 'school/school/add',
                    edit_url: 'school/school/edit',
                    del_url: 'school/school/del',
                    multi_url: 'school/school/multi',
                    table: 'school',
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
                        {field: 'name', title: __('Name')},
                        {field: 'title_image', title: __('Title_image'), formatter: Table.api.formatter.image,operate: false},
                        {field: 'schoolcat.id', title: __('Cat.Name'),visible:false, addClass:"selectpage",extend:"data-source='school/cat/index' data-field='name'"},
                        {field: 'schoolcat.name', title: __('Cat.name'),operate:false,sortable:true},
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