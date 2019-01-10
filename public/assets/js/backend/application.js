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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'user_id', title: __('User_id'),visible:false,operate:false},
                        {field: 'user.nickname', title: __('User.username'),operate:false},
                        {field: 'name', title: __('Name'),operate:'LIKE'},
                        {field: 'telephone', title: __('Telephone')},
                        {field: 'type.type_name', title: __('Type.type_name'),operate:'LIKE'},
                        // {field: 'school_id', title: __('School_id'),visible:false,operate:false},
                        {field: 'school.name', title: __('School.name'),operate:'LIKE'},
                        // {field: 'major_id', title: __('Major_id'),visible:false,operate:false},
                        {field: 'cat.name', title: __('Cat.name'),operate:'LIKE'},

                        {field: 'major.name', title: __('Major.name'),operate:'LIKE'},
                        {field: 'idcard', title: __('Idcard'),operate:'LIKE'},
                        {field: 'birthday', title: __('Birthday'),operate:false, addclass:'datetimerange'},
                        {field: 'age', title: __('Age'),operate:false},
                        {field: 'sex', title: __('Sex'), searchList: {"0":__('Sex 0'),"1":__('Sex 1'),"2":__('Sex 2')}, formatter: Table.api.formatter.normal},
                        {field: 'ethnic', title: __('Ethnic'),operate:false},
                        {field: 'graduation', title: __('Graduation'),operate:'LIKE'},
                        {field: 'certificate', title: __('Certificate')},
                        {field: 'graduationdate', title: __('Graduationdate'),operate:false},
                        {field: 'graduationmajor', title: __('Graduationmajor'),operate:false},
                        {field: 'idcard_positive_image', title: __('Idcard_positive_image'), formatter: Table.api.formatter.image,operate:false},
                        {field: 'idcard_negative_image', title: __('Idcard_negative_image'), formatter: Table.api.formatter.image,operate:false},
                        {field: 'graduation_image', title: __('Graduation_image'), formatter: Table.api.formatter.image,operate:false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"-1":__('Status -1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'daterange', formatter: Table.api.formatter.datetime},
                        {field: 'applicationtime', title: __('Applicationtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'daterange', formatter: Table.api.formatter.datetime},
                        {field: 'result', title: __('Result'),operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $('#c-major_id').data("params", function (obj) {
                return {school_id: $("#c-school_id").val()};
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $('#c-major_id').data("params", function (obj) {
                return {school_id: $("#c-school_id").val()};
            });
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