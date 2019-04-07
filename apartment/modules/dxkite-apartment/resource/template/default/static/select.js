(function (window) {

    function getParentLink(target) {
        // 父节点
        var parent = [];
        // 获取取值链
        while (target.dataset.parent) {
            v_p.unshift(target.value);
            if (typeof target.dataset.parent != 'undefined') {
                target = document.getElementById(target.dataset.parent);
            } else {
                break;
            }
        }
        return parent;
    }

    function getChildData(link, data,i) {
        var newData = data;
        var i=i || link.length;
        for (var j = 0; j < i; j++) {
            if (link[j] instanceof Element) {
                value = link[j].value;
            } else {
                newData = newData[value].list;
            }
            
        }
        return newData;
    }

    //更新select
    function fillSelect(select, list) {
        select.innerHTML = "";
        for (var index in list) {
            var option = new Option(list[index].text, list[index].value);
            select.add(option);
        }
    }

    window.cascade = function cascade(selectList, data) {
        
        for (var i = 0; i < selectList.length; i++) {
            //增加变更事件
            selectList[i].addEventListener(
                "change", function (event) {
                    var value = event.target.value;
                    // 父节点
                    var v_p = getParentLink(this);
                    // 获取取值链
                    while (target.dataset.parent) {
                        v_p.unshift(target.value);
                        if (typeof target.dataset.parent != 'undefined') {
                            target = document.getElementById(target.dataset.parent);
                        } else {
                            break;
                        }
                    }
                    console.log(v_p);
                    var v_length = v_p.length;
                    //如果是最后一个select就跳出
                    if (v_length >= selectList.length) return;
                    //构造新的选择器
                    var newSelectList = [];
                    for (var j = v_length; j < selectList.length; j++)
                        newSelectList.push(selectList[j]);
                    var newData = getChildData(data);
                    console.log(newSelectList);
                    console.log(newData);
                    cascade(newSelectList, newData);
                }
            );
        }

    }

})(window);