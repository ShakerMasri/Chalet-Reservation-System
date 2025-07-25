/*document.addEventListener("DOMContentLoaded",()=>{
    fetch("nav.html").then((res)=>res.text()).then((data)=>{
        document.getElementById("navbar").innerHTML=data;
    });
});
this code is to avoid repeating the same html code for the navbar
will work on it soon :)
*/

document.getElementById("signup").addEventListener("click", function (e) {
  window.location.href = "../html/signup.html";
});

document.getElementById("signin").addEventListener("click", function (e) {
  window.location.href = "../html/login.html";
});