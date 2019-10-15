define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'app/upload/index' + location.search,
                    add_url: 'app/upload/add',
                    edit_url: 'app/upload/edit',
                    del_url: 'app/upload/del',
                    multi_url: 'app/upload/multi',
                    table: 'upload',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'upload_id',
                sortName: 'upload_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'upload_id', title: __('Upload_id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'upload_status', title: __('Upload_status')},
                        {field: 'text', title: __('Text')},
                        {field: 'image', title: __('Image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'images', title: __('Images'), events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'file', title: __('File')},
                        {field: 'content', title: __('Content')},
                        {field: 'look', title: __('Look')},
                        {field: 'status', title: __('Status')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'type_id', title: __('Type_id')},
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