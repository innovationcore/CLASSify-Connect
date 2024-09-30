function handleUpload() {
    alert('Upload was clicked!');
}

function checkEmail(email) {
    console.log(email);
    $.get(`https://data.ai.uky.edu/classify/users/getUserFromEmail?email=${email}`, function(data, status) {
        let response = JSON.stringify(data);
        response = JSON.parse(response)
        if (!response.success) {
            if (window.confirm(`${email} is not registered with CLASSify. Press OK to be directed to the Center for Applied AI Collaboration Form which allows you to apply for access. 
            \n\nNote: Your browser may block this popup. Allow it access to be redirected to the form.`)) {
               window.open('https://redcap.uky.edu/redcap/surveys/?s=K7WTCDH37AXLEKNM', '_blank');
            }
        }
        else {
            alert(`${email} is registered with CLASSify. You may proceed.`);
        }
    })
}

settings = {}

$('#upload-dataset').click(handleUpload);
$('#account-status').click(checkEmail);
