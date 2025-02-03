/*
 * 
 * Add a line in the applications section for the new interface
 *
 */

const applications = document.getElementsByClassName('menubox');
const newElement = document.createElement("div");
newElement.classList = 'hang';
newElement.innerHTML = '<a>CLASSify</a>';

console.log(applications.length);

applications.item(1).appendChild(newElement);