window.addEventListener('load', function () {
    window.objectcache.latency.init();
    window.objectcache.groups.init();
});

jQuery.extend(window.objectcache, {
    latency: {
        init: function () {
            this.fetchData();
            setInterval(this.fetchData, 10000);
        },

        fetchData: function () {
            jQuery
                .ajax({
                    url: objectcache.rest.url + 'objectcache/v1/latency',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', objectcache.rest.nonce);
                    },
                })
                .done(function (data) {
                    var widget = document.querySelector('.objectcache\\:latency-widget');

                    var table = widget.querySelector('table');
                    table && widget.removeChild(table);

                    var error = widget.querySelector('.error');
                    error && widget.removeChild(error);

                    table = document.createElement('table');
                    widget.prepend(table);

                    var content = '';

                    var formatLatency = function (us) {
                        if (us < 500) return '<strong>' + us + '</strong> μs';
                        if (us < 1000) return '<strong class="warning">' + us + '</strong> μs';
                        return '<strong class="error">' + Math.round((us / 1000 + Number.EPSILON) * 100) / 100 + '</strong> ms';
                    };

                    data.forEach(function (item) {
                        content += '<tr>';
                        content += '  <td>' + item.url + '</td>';
                        content += '  <td>';
                        content += item.error ? '<span class="error">' + item.error + '</span>' : formatLatency(item.latency);
                        content += '  </td>';
                        content += '</tr>';
                    });

                    document.querySelector('.objectcache\\:latency-widget table').innerHTML = content;
                })
                .error(function (error) {
                    var widget = document.querySelector('.objectcache\\:latency-widget');

                    var table = widget.querySelector('table');
                    table && widget.removeChild(table);

                    var container = widget.querySelector('.error');

                    if (! container) {
                        container = document.createElement('p');
                        container.classList.add('error');

                        widget.append(container);
                    }

                    if (error.responseJSON && error.responseJSON.message) {
                        container.textContent = error.responseJSON.message;
                    } else {
                        container.textContent = 'Request failed (' + error.status + ').';
                    }
                });
        },
    },

    groups: {
        init: function () {
            document.querySelector('.objectcache\\:groups-widget button')
                .addEventListener('click', window.objectcache.groups.fetchData);
        },

        fetchData: function () {
            jQuery
                .ajax({
                    url: objectcache.rest.url + 'objectcache/v1/groups',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', objectcache.rest.nonce);

                        var widget = document.querySelector('.objectcache\\:groups-widget');

                        var button = widget.querySelector('.button');
                        button.blur();
                        button.classList.add('disabled');
                        button.textContent = button.dataset.loading;

                        var container = widget.querySelector('.table-container');
                        container && widget.removeChild(container);

                        var error = widget.querySelector('.error');
                        error && widget.removeChild(error);
                    },
                })
                .done(function (data) {
                    var widget = document.querySelector('.objectcache\\:groups-widget');

                    var info = widget.querySelector('p:first-child');
                    info && widget.removeChild(info);

                    var container = document.createElement('div');
                    container.classList.add('table-container');
                    widget.prepend(container);

                    var table = document.createElement('table');
                    container.prepend(table);

                    var content = '';

                    if (data.length) {
                        data.forEach(function (item) {
                            content += '<tr>';
                            content += '  <td>' + item.group + '</td>';
                            content += '  <td>';
                            content += '    <strong>' + item.count + '</strong>';
                            content += '  </td>';
                            content += '</tr>';
                        });
                    } else {
                        content += '<tr>';
                        content += '  <td colspan="2">No cache groups found.</td>';
                        content += '</tr>';
                    }

                    table.innerHTML = content;
                })
                .error(function (error) {
                    var widget = document.querySelector('.objectcache\\:groups-widget');
                    var container = widget.querySelector('.error');

                    if (! container) {
                        container = document.createElement('p');
                        container.classList.add('error');

                        widget.append(container);
                    }

                    if (error.responseJSON && error.responseJSON.message) {
                        container.textContent = error.responseJSON.message;
                    } else {
                        container.textContent = 'Request failed (' + error.status + ').';
                    }
                })
                .always(function () {
                    var button = document.querySelector('.objectcache\\:groups-widget .button');
                    button.textContent = button.dataset.text;
                    button.classList.remove('disabled');
                });
        },
    },
});
