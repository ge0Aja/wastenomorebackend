// euclidean distance
function dist(a, b) {
    return Math.sqrt(Math.pow(a[0] - b[0], 2), Math.pow(a[1] - b[1], 2));
}

function clickcancel() {
    var event = d3.dispatch('click', 'dblclick');

    function cc(selection) {
        var down,
            tolerance = 5,
            last,
            wait = null;
        selection.on('mousedown', function () {
            if (d3.event.button != 0) {
                return;
            }
            down = d3.mouse(document.body);
            last = +new Date();
        });
        selection.on('mouseup', function (d) {
            if (d3.event.button != 0) {
                return;
            }
            if (!down || dist(down, d3.mouse(document.body)) > tolerance) {
                return;
            } else {
                if (wait) {
                    window.clearTimeout(wait);
                    wait = null;
                    event.dblclick(d);
                } else {
                    wait = window.setTimeout((function (e) {
                        return function () {
                            event.click(d);
                            wait = null;
                        };
                    })(d3.event), 300);
                }
            }
        });
    };
    return d3.rebind(cc, event, 'on');
}

function parseQuery(qstr) {
    var query = {};
    var a = qstr.substr(1).split('&');
    for (var i = 0; i < a.length; i++) {
        var b = a[i].split('=');
        if (b[0] == '' || b[1] == '') {
            continue;
        }
        query[decodeURIComponent(b[0])] = decodeURIComponent(b[1] || '');
    }
    return query;
}
