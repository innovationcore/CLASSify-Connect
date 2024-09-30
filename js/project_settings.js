$(document).ready(function() {
    // Inject the button into the placeholder
    $('#my-custom-button').html('<button id="custom-action-btn" class="btn btn-primary">Upload Form Data to CLASSify</button>');

    // Handle the button click
    $('#custom-action-btn').click(function() {
        // Add your custom logic here
        alert('Button clicked! Add your action logic.');
        // For example, you can make an AJAX call to a REDCap endpoint or perform other actions.
    });
});
