define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'school/schoolmajor/index',
                    add_url: 'school/schoolmajor/add',
                    edit_url: 'school/schoolmajor/edit',
                    del_url: 'school/schoolmajor/del',
                    multi_url: 'school/schoolmajor/multi',
                    table: 'school_major',
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
                        {field: 'school_id', title: __('School_id')},
                        {field: 'major_ids', title: __('Major_ids')},
                        {field: 'is_del', title: __('Is_del'), searchList: {"0":__('Is_del 0'),"1":__('Is_del 1')}, formatter: Table.api.formatter.normal},
                        {field: 'major.id', title: __('Major.id')},
                        {field: 'major.name', title: __('Major.name')},
                        {field: 'major.is_del', title: __('Major.is_del')},
                        {field: 'school.id', title: __('School.id')},
                        {field: 'school.cat_id', title: __('School.cat_id')},
                        {field: 'school.title_image', title: __('School.title_image'), formatter: Table.api.formatter.image},
                        {field: 'school.name', title: __('School.name')},
                        {field: 'school.is_del', title: __('School.is_del')},
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