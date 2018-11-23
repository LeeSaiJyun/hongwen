define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'application/index',
                    add_url: 'application/add',
                    edit_url: 'application/edit',
                    del_url: 'application/del',
                    multi_url: 'application/multi',
                    table: 'application',
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
                        {field: 'user_id', title: __('User_id')},
                        {field: 'name', title: __('Name')},
                        {field: 'school_id', title: __('School_id')},
                        {field: 'major_id', title: __('Major_id')},
                        {field: 'applicationdata', title: __('Applicationdata'), searchList: {"0":__('Applicationdata 0'),"1":__('Applicationdata 1')}, formatter: Table.api.formatter.normal},
                        {field: 'idcard', title: __('Idcard')},
                        {field: 'birthday', title: __('Birthday'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'age', title: __('Age')},
                        {field: 'sex', title: __('Sex'), searchList: {"0":__('Sex 0'),"1":__('Sex 1'),"2":__('Sex 2')}, formatter: Table.api.formatter.normal},
                        {field: 'ethnic', title: __('Ethnic')},
                        {field: 'graduation', title: __('Graduation')},
                        {field: 'certificate', title: __('Certificate')},
                        {field: 'graduationdate', title: __('Graduationdate'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'graduationmajor', title: __('Graduationmajor')},
                        {field: 'idcard_positive_image', title: __('Idcard_positive_image'), formatter: Table.api.formatter.image},
                        {field: 'idcard_negative_image', title: __('Idcard_negative_image'), formatter: Table.api.formatter.image},
                        {field: 'graduation_image', title: __('Graduation_image'), formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"-1":__('Status -1')}, formatter: Table.api.formatter.status},
                        {field: 'applicationtime', title: __('Applicationtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'result', title: __('Result')},
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