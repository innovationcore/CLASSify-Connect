function handleUpload() {
    const parsed = parseCSVWithNewNames(moduleData, classifier[0]);
    console.log(classifier);
    console.log(parsed);
}

function checkEmail() {
    const email_field = document.getElementsByName('classify-email____0')[0];
    let email = email_field.value;
    console.log(email);
    $.get(`https://data.ai.uky.edu/classify/users/getUserFromEmail?email=${email}`, function(data, status) {
        let response = JSON.stringify(data);
        response = JSON.parse(response)
        const res_element = document.getElementById('response');
        console.log(res_element);

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
