function handleUpload() {
    // Parse the CSV with the classifier field
    const parsed = parseCSVWithNewNames(moduleData, classifier[0]);
    const upload_url = 'https://data.ai.uky.edu/classify/reports/submit';
    var user_uuid = '';

    // Get email field from input
    const email = document.getElementsByName('classify-email____0')[0].value;

    // Get the user UUID from the email
    $.get(`https://data.ai.uky.edu/classify/users/getUserFromEmail?email=${email}`, function(data, status) {
        user_uuid = data.user_id;

        // Create form data object
        var form_data = new FormData();

        // Create a Blob from the parsed CSV string
        const csvBlob = new Blob([parsed], { type: 'text/csv' });

        // Define or fallback to a default filename
        var currentFile = filename; // Fallback if filename is not defined

        // Ensure the filename ends with .csv and then replace the suffix for the user_uuid
        currentFile = currentFile.endsWith('.csv') ? currentFile : currentFile + '.csv';
        currentFile = currentFile.replace('.csv', `_${user_uuid}.csv`);

        // Append the Blob and other fields to the form data
        form_data.append('file', csvBlob, currentFile);
        form_data.append('user_uuid', user_uuid);
        form_data.append('filename', currentFile);

        // Send the form data via a POST request
        $.ajax({
            url: upload_url,
            type: 'POST',
            data: form_data,
            processData: false,  // Don't process the files
            contentType: false,  // Let jQuery set the content type
            success: function(response) {
                const response_div = document.getElementById('upload-result');

                response_div.innerHTML = response.message + "<button onclick='classifyRedirect()'>Go to CLASSify</button>"

                console.log('File successfully uploaded', response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error uploading file:', textStatus, errorThrown);
            }
        });
    });
}



function checkEmail() {
    const email_field = document.getElementsByName('classify-email____0')[0];
    let email = email_field.value;
    console.log(email);
    $.get(`https://data.ai.uky.edu/classify/users/getUserFromEmail?email=${email}`, function(data, status) {
        let response = JSON.stringify(data);
        response = JSON.parse(response)
        const res_element = document.getElementById('response');

        if (!response.success) {
            res_element.innerText = `${email} is not registered with CLASSify.`;
        }
        else {
            res_element.innerText = `${email} is registered with CLASSify. You may proceed.`;
        }
    })
}

function parseCSVWithNewNames(csvString, classifierField) {
    if (!csvString || !classifierField) {
        console.error('Invalid input. Please provide both CSV content and a classifier.');
        return;
    }

    const lines = csvString.split('\n');
    if (lines.length < 2) {
        console.error('Invalid CSV format. At least one header row and one data row are required.');
        return;
    }

    // Replace header
    const headers = lines[0].split(',').map(h => {
        const cleanHeader = h.trim();
        return cleanHeader === classifierField ? "class" : cleanHeader;
    });

    // Check if the classifierField was found in headers
    if (!headers.includes('class')) {
        console.warn(`The classifier field "${classifierField}" was not found in the CSV headers.`);
    }

    // Create new CSV string with renamed header
    lines[0] = headers.join(',');


    return lines.join('\n');
}

function classifyRedirect() {
    window.open('https://data.ai.uky.edu/classify/result', "_blank");
}
