$(function () {
    function showinfo(title, info) {
        var modal = $('#process');
        modal.find('.modal-title').html(title);
        modal.find('.modal-body').html(info);
        modal.modal('show');
    }

    function showerr(err) {
        var modal = $('#process');
        modal.find('.modal-title').html(err.name);
        modal.find('.modal-body').html(err.message + '如果有问题，请联系开发者 Q:670337693; ');
        modal.modal('show');
    }

    $("[remote-setting]").on('click', function () {
        var method = this.dataset.method;
        var data = new FormData(document.getElementById(this.dataset.param));
        var that = $(this);
        var text = that.text();
        that.text('操作中');
        dx.call('apartment', method, data)
            .then(function (res) {
                that.text(text);
                if (res) {
                    showinfo('操作成功', '如果有问题，请联系开发者 Q:670337693; ');
                } else {
                    showinfo('操作失败', '如果有问题，请联系开发者 Q:670337693;');
                }
            }).catch(function (res) {
                that.text(text);
                showerr(res);
            });
    });

    $("[toggle]").on('click', function () {
        var method = this.dataset.method;
        var that = $(this);
        var text = that.text();
        that.text('操作中');
        dx.call('apartment', method)
            .then(function (res) {
                that.text(res ? "关闭" : "开启");
            }).catch(function (res) {
                that.text(text);
                showerr(res);
            });
    });


    $("[upload]").on('click', function () {
        var method = this.dataset.method;
        var data = new FormData(document.getElementById(this.dataset.param));
        var that = $(this);
        var text = that.text();
        that.text('导入数据中...');
        dx.call('apartment', method, data)
            .then(function (res) {
                that.text(text);
                if (res == -1) {
                    showinfo('Excel表格式错误', '如果有问题，请联系开发者 Q:670337693; ');
                } else {
                    showinfo('上传成功', '成功导入数据' + res + '条数据<br/>未上传的数据为重复数据。');
                }
            })
            .catch(function (res) {
                that.text(text);
                showerr(res);
            });
    });
});