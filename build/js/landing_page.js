import { updateCopyRightYear, initApp, directToLoginPage } from "./main.js";

document.addEventListener('DOMContentLoaded', updateCopyRightYear);
document.addEventListener('DOMContentLoaded', initApp);
document.addEventListener('DOMContentLoaded', directToLoginPage('signInButton1'));
document.addEventListener('DOMContentLoaded', directToLoginPage('signInButton2'));
document.addEventListener('DOMContentLoaded', directToLoginPage('signInButton3'));

const testimonialsContainer = document.getElementById('js-testimonials');
const fetchTestimonials = async () => {
  const data = await fetch(`./backend/admin_action.php?fetch-testimonials=1`, {
    method: 'GET',
  });
  const response = await data.text();
  // console.log(response);
  testimonialsContainer.innerHTML = response;
}
fetchTestimonials();