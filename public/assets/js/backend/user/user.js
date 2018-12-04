define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    multi_url: 'user/user/multi',
                    table: 'user',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'user.id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true},
                        {field: 'group.name', title: __('Group'), operate: false},
                        // {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'nickname', title: __('Nickname'), operate: 'LIKE'},
                        // {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'avatar', title: __('Avatar'), formatter: Table.api.formatter.image, operate: false},
                        // {field: 'level', title: __('Level'), operate: 'BETWEEN', sortable: true},
                        {field: 'gender', title: __('Gender'),  searchList: {"1": __('Male'), "0": __('Female')}, formatter: Controller.api.formatter.gender },
                        // {field: 'score', title: __('Score'), operate: 'BETWEEN', sortable: true},
                        {field: 'successions', title: __('Successions'), visible: false, operate: 'BETWEEN', sortable: true},
                        {field: 'maxsuccessions', title: __('Maxsuccessions'), visible: false, operate: 'BETWEEN', sortable: true},
                        // {field: 'loginip', title: __('Loginip'), formatter: Table.api.formatter.search},
                        {field: 'jointime', title: __('Jointime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        // {field: 'joinip', title: __('Joinip'), formatter: Table.api.formatter.search},
                        {field: 'pid', title: __('pid')},
                        {field: 'balance', title: __('balance'), operate: 'BETWEEN', sortable: true},
                        {field: 'frozen', title: __('frozen'), operate: 'BETWEEN', sortable: true},
                        {field: 'graduaction', title: __('graduaction'), operate: false},
                        {field: 'graduaction_time', title: __('graduaction_time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'graduaction_major', title: __('graduaction_major'), operate: false},
                        {field: 'graduaction_image', title: __('graduaction_image'), formatter: Table.api.formatter.image, operate: false},
                        {field: 'idcard_positive_image', title: __('idcard_positive_image'), formatter: Table.api.formatter.image, operate: false},
                        {field: 'idcard_negative_image', title: __('idcard_negative_image'), formatter: Table.api.formatter.image, operate: false},
                        {field: 'ethnic', title: __('ethnic'), operate: false},
                        {field: 'openid', title: __('openid')},


                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {
                            field: 'buttons',
                            width: "120px",
                            title: __('资料'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'bank',
                                    text: __('银行卡'),
                                    title: __('银行卡'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-credit-card',
                                    url: 'user/bank/index?user_id={ids}',
                                    visible: function (row) {return true;}//返回true时按钮显示,返回false隐藏
                                },
                                {
                                    name: 'address',
                                    text: __('地址'),
                                    title: __('地址'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-map-marker',
                                    url: 'user/address/index?user_id={ids}',
                                },
                            ],
                            formatter: Table.api.formatter.buttons,
                            operate:false
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            // 获取选中项
            $(document).on("click", ".btn-selected", function () {
                var send_ids = Table.api.selectedids(table);
                Fast.api.open("user/message/add?ids="+send_ids,"群发消息",{
                    callback:function (value) {
                        //Fast.api.close();
                    }
                });
            });
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
            formatter: {
                gender: function (value, row, index) {
                    if(value==1){
                        return '男';
                    }else if(value==0){
                        return '女';
                    }else if(value==-1){
                        return '保密';
                    }else{
                        return value;
                    }
                }
            }
        }

    };
    return Controller;
});