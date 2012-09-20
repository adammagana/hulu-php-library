(function () {
    var hulu = {
        init: function () {
            hulu.registerEvents();
        },

        registerEvents: function () {
            // Handles all form submissions
            $('body').on('submit', 'form', function (e) {
                e.preventDefault();

                var responseContainer = $(this).siblings('.response_container'),
                    codeContainer = $(this).siblings('.code_snippet'),
                    codeTemplate = $('#code_template').html(),
                    selectFields = $(this).children('select, input'),
                    requestParams = {},
                    templateData = {
                        method: codeContainer.data('method'),
                        params: []
                    };

                // Retrieve all form options
                for (var i = 0;i < selectFields.length;i += 1) {
                    var key = $(selectFields[i]).attr('name'),
                        value = $(selectFields[i]).val();

                    // Only push parameters if the value isn't empty
                    if (value !== '') {
                        requestParams[key] = value;
                        templateData.params.push({key: key, value: value});
                    }
                }

                // Render the PHP code snippet
                codeContainer.html(Mustache.to_html(codeTemplate, templateData));

                // Make the request to api.php!
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'GET',
                    data: requestParams,
                    success: function (response) {
                        // Render the response and display it in the response container
                        responseContainer.text(hulu.formatXML(response));
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            });
        },

        // Formatting XML Credit: https://gist.github.com/1083506
        formatXML: function (xml) {
            var formatted = '';
            var reg = /(>)(<)(\/*)/g;
            xml = xml.replace(reg, '$1\r\n$2$3');
            var pad = 0;
            jQuery.each(xml.split('\r\n'), function(index, node) {
                var indent = 0;
                if (node.match( /.+<\/\w[^>]*>$/ )) {
                    indent = 0;
                } else if (node.match( /^<\/\w/ )) {
                    if (pad != 0) {
                        pad -= 1;
                    }
                } else if (node.match( /^<\w[^>]*[^\/]>.*$/ )) {
                    indent = 1;
                } else {
                    indent = 0;
                }

                var padding = '';
                for (var i = 0; i < pad; i++) {
                    padding += '  ';
                }

                formatted += padding + node + '\r\n';
                pad += indent;
            });

            return formatted;
        }
    };

    $(document).ready(hulu.init);
}());