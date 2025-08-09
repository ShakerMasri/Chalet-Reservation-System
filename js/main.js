/*document.addEventListener("DOMContentLoaded",()=>{
    fetch("nav.html").then((res)=>res.text()).then((data)=>{
        document.getElementById("navbar").innerHTML=data;
    });
});
this code is to avoid repeating the same html code for the navbar
will work on it soon :)
*/

function signin() {
  window.location.href = "./html/login.html";
}
function signup() {
  window.location.href = "./html/signup.html";
}
