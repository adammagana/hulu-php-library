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
                    codeTemplate = $(codeContainer.data('template')).html(),
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
                        console.log(response)
                        responseContainer.text(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            });
        }
    };

    $(document).ready(hulu.init);
}());