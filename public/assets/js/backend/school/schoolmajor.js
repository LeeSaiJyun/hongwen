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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'school.name', title: __('School.name'),operate: 'LIKE'},
                        {field: 'school.cat_id', title: __('School.cat_id'), addClass:"selectpage",extend:"data-source='school/cat/index' data-field='name'"},
                        // {field: 'major_ids', title: __('Major_ids'),operate:false},
                        {field: 'major_text', title: __('Major.name'), operate:false, formatter: Table.api.formatter.label},
                        // {field: 'school.id', title: __('School.id')},
                        {field: 'school.title_image', title: __('School.title_image'), operate:false, formatter: Table.api.formatter.image},
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