const classify_root = 'https://classify.ai.uky.edu/';

function checkEmail() {
    $.get(`${classify_root}/users/getUserFromEmail?email=${email}`, function(data, status) {
        let response = JSON.stringify(data);
        response = JSON.parse(response)
        const res_element = document.getElementById('response');

        if (!response.success) {
            res_element.innerText = `${email} is not registered with CLASSify. Use the collaboration request below to
            request access.`;
        }
        else {
            if(response.accepted_terms) {
                res_element.innerHTML = `${email} is registered with CLASSify. You have agreed to the site's 
                usage terms. You may proceed.`;
            }
            else {
                res_element.innerHTML = `${email} is registered with CLASSify. You have not agreed to the site's 
                usage terms. To do so, navigate to <a href="${classify_root}/" target="_blank">CLASSify</a> to do so.`;
            }
        }
    })
}

function classifyRedirect() {
    window.open(`${classify_root}/result`, "_blank");
}
