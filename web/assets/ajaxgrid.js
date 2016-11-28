$.fn.ajaxgrid = function(options) {
    var root = this;
    var table, tbody;
    var headerRow;
    var filters, pagination;
    var request = {
        rows: options.rowsPerPage
    };

    completeOptions(options);
    createBaseElements();
    sendInitRequest();

    return root;

    function completeOptions(options) {
        if (!options.dataUrl) {
            throw 'Wrong "dataUrl" exception';
        }
        if (!options.editUrl) {
            throw 'Wrong "editUrl" exception';
        }
        if (!options.sortableColumns) {
            options.sortableColumns = [];
        }
        if (!options.filterableColumns) {
            options.filterableColumns = [];
        }
        if (!options.rowsPerPage) {
            options.rowsPerPage = 5;
        }
    }
    function createBaseElements() {
        filters = createElement('div', root).addClass('filters center-block table-grid form-inline');
        table = createElement('table', root).addClass('table table-grid');
        headerRow = createElement('tr', table);
        tbody = createElement('tbody', table);
        pagination = createElement('div', root).addClass('pagination text-center center-block');
    }
    function sendInitRequest() {
        $.getJSON(options.dataUrl, request, function(json) {
            var columns = Object.getOwnPropertyNames(json.data[0]);
            columns.push('Edit');
            setHeader(headerRow, columns, options.sortableColumns);
            setData(tbody, json.data);
            setPagination(pagination, Math.ceil(json.rows / options.rowsPerPage));
            setFilters(filters, options.filterableColumns);
        });
    }

    function setHeader(headerRow, columns, sortable) {
        headerRow.empty();
        setHeaderText(headerRow, columns);
        setHeaderSort(headerRow, sortable);
    }
    function setHeaderText(headerRow, columns) {
        for (var i = 0; i < columns.length; i++) {
            createElement('th', headerRow).text(columns[i]);
        }
    }
    function setHeaderSort(headerRow, sortable) {
        sortable.forEach(function (column) {
            headerRow.find(':contains(' + column +')')
                .addClass('sortable gray-link')
                .click(function(){
                    onSortClick(column);
                });
        });
    }

    function setData(tbody, data) {
        tbody.empty();
        for (var i = 0; i < data.length; i++) {
            var row = createElement('tr', tbody);
            setRow(row, data[i]);
        }
    }
    function setRow(row, data) {
        for (var dataCell in data) {
            if (data.hasOwnProperty(dataCell)) {
                createElement('td', row).text(data[dataCell]);
            }
        }
        var editLink = '<a href="'.concat(options.editUrl, '/', data['id'], '">' +
            '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>');
        createElement('td', row).html(editLink);
    }

    function setPagination(pagination, pages) {
        pagination.empty();
        var page = request.page ? request.page : 0;
        for (var i = 0; i < pages; i++) {
            var button = createElement('button class="btn"', pagination)
                .text(i + 1)
                .click(onPageClick.bind(i));
            if (i === +page) {
                button.addClass('selected btn btn-primary');
            }
        }
    }
    function setFilters(filters, filterable) {
        filters.empty();
        var list = createElement('select class="btn"', filters);
        createElement('input class="form-control"', filters).attr('type', 'text');
        createElement('button class="btn"', filters).text('Filter').click(onFilterClick);
        filterable.forEach(function (column) {
            createElement('option', list).text(column).attr('value', column);
        });
    }

    function createElement(element, root) {
        return $('<' + element + '>').appendTo(root);
    }

    function onFilterClick() {
        var pattern = $('.filters input').val();
        if (pattern !== '') {
            request.pattern = pattern;
            request.filterbyfield = $('.filters select').val();
            request.page = 0;
        } else {
            delete request.pattern;
            delete request.filterbyfield;
        }
        $.getJSON(options.dataUrl, request, function (json) {
            setData(tbody, json.data);
            setPagination(pagination,
                Math.ceil(json.rows / options.rowsPerPage));
        });
    }
    function onPageClick() {
        if (request.page !== this) {
            request.page = this;
            $.getJSON(options.dataUrl, request, function (json) {
                setData(tbody, json.data);
                setPagination(pagination,
                    Math.ceil(json.rows / options.rowsPerPage));
            });
        }
    }
    function onSortClick(column) {
        if (column === request.sortbyfield) {
            if (request.order === 'asc') {
                root.find('.sortable:contains(' + column + ')')
                    .addClass('desc')
                    .removeClass('asc');
                request.order = 'desc';
            } else {
                root.find('.sortable:contains(' + column + ')')
                    .removeClass('desc');
                delete request.order;
                delete request.sortbyfield;
            }
        } else {
            root.find('.sortable:contains(' + column + ')')
                .addClass('asc');
            root.find('.sortable:contains(' + request.sortbyfield + ')')
                .removeClass('asc')
                .removeClass('desc');
            request.sortbyfield = column;
            request.order = 'asc';
        }
        $.getJSON(options.dataUrl, request, function (json) {
            setData(tbody, json.data);
            setPagination(pagination,
                Math.ceil(json.rows / options.rowsPerPage));
        });
    }
};