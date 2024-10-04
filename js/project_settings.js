function handleUpload() {
    alert('Upload was clicked!');
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
            res_element.innerText = `${email} is not registered with CLASSify. Fill out the <a href='https://redcap.uky.edu/redcap/surveys/?s=K7WTCDH37AXLEKNM' target='_blank'>Center for Applied AI Collaboration Form</a> which allows you to apply for access. 
            \n\nNote: Your browser may block this popup. Allow it access to be redirected to the form.`;
        }
        else {
            res_element.innerText = `${email} is registered with CLASSify. You may proceed.`;
        }
    })
}

function collectSettings(userSettings) {
    settings = userSettings;
}

settings = {}

/*$('#upload-dataset').click(handleUpload);
$('#account-status').click(checkEmail);*/
