define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'withdraw/index',
                    audit_url: 'withdraw/audit',//审核
                    multi_url: 'withdraw/multi',
                    table: 'withdraw',
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
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'money', title: __('Money'), operate: 'BETWEEN'},
                        {field: 'balance', title: __('Balance'), operate: 'BETWEEN'},
                        {field: 'bank_id', title: __('Bank_id')},
                        {field: 'order_id', title: __('Order_id')},
                        {field: 'withdrawtime', title: __('Withdrawtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'paytime', title: __('Paytime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'admin.username', title: __('Admin.username')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"-1":__('Status -1')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate',
                            width: "120px",
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'audit',
                                    title: __('审核'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-money',
                                    url: 'withdraw/audit',
                                    callback: function (data) {            //回调方法，用来响应 Fast.api.close()方法 **注意不能有success 是btn-ajax的回调，btn-dialog 用的callback回调，两者不能同存！！！！
                                        $(".btn-refresh").trigger("click");//刷新当前页面的数据
                                        console.error(data);//控制输出回调数据
                                    },
                                    hidden:function(data){   //控制按钮隐藏方法 判断表格数据是否满足要求，然后隐藏或显示
                                        if(data.status !== 0)
                                            return true;
                                    }
                                },
                            ],
                            formatter: Table.api.formatter.operate
                        }

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
        audit: function () {
            //通过
            $(document).on("click", ".btn-accept", function () {
                var form = $("form[role=form]").serializeArray();
                form.push({name: "audit_flag", value: "accept"});
                Fast.api.ajax({
                    data: form
                }, function () {
                    parent.window.$("#table").bootstrapTable('refresh');
                    Fast.api.close();
                });
            });

            //拒绝
            $(document).on("click", ".btn-reject", function () {
                    var form = $("form[role=form]").serializeArray();
                    form.push({name: "audit_flag", value: "reject"});
                    Fast.api.ajax({
                        data: form
                    }, function () {
                        parent.window.$("#table").bootstrapTable('refresh');
                        Fast.api.close();
                    });
                });

        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});