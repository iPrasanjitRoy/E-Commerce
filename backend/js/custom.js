$(document).ready(function () {
    $('.p-form').on('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        var $form = $(this); // The form element 
        // Create FormData object from the form
        var formData = new FormData(this);

        var actionUrl = $form.attr('action'); // Get the URL from the form's action attribute
        var $submitButton = $form.find('button[type="submit"]'); // Find the submit button
        
        // Disable the submit button and show the loader
        $submitButton.prop('disabled', true);


 

        // Send AJAX request
        $.ajax({
            url: '/upload', // Replace with your server endpoint
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically transforming the data into a query string
            contentType: false, // Prevent jQuery from setting the Content-Type header
            beforeSend: function () {
                $submitButton.prop('disabled', true);
            },
            success: function (response) {
                // Handle successful response
                $('#response').text('File uploaded successfully: ' + JSON.stringify(response));
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Handle error
                $('#response').text('Upload failed: ' + textStatus);
            },
            complete: function () {
                // Re-enable the submit button and hide the loader
                $submitButton.prop('disabled', false);
            }
        });
    });
});
