/*document.addEventListener("DOMContentLoaded",()=>{
    fetch("nav.html").then((res)=>res.text()).then((data)=>{
        document.getElementById("navbar").innerHTML=data;
    });
});
this code is to avoid repeating the same html code for the navbar
will work on it soon :)
*/

document.getElementById("sign-up").addEventListener("click", function (e) {
  window.location.href = "./html/signup.html";
});

document.getElementById("sign-in").addEventListener("click", function (e) {
  window.location.href = "./html/login.html";
});