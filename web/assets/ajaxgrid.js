(function ($) {
    $.fn.ajaxgrid = function (options) {
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
            if (!options.dataUrl){
                throw 'check your dataUrl'
            }
            if (!options.sortableColumns){
                options.sortableColumns = [];
            }
            if (!options.filterableColumns){
                options.filterableColumns = [];
            }
            if (!options.rowsPerPage){
                options.rowsPerPage = 10;
            }
        }

        function createBaseElements() {
            table = $('table').appendTo(root);
            tbody = $('tbody').appendTo(table);
            headerRow = $('tr').appendTo(table);
            filters = $('div').appendTo(root).addClass('filters');
            pagination = $('div').appendTo(root).addClass('pagination');
        }

        function sendInitRequest() {
            $.fn.getJSON(options.dataUrl, request, function (json) {
                var columns = Object.getOwnPropertyNames(json.data[0]);
            });

        }



    };

})(jQuery)
