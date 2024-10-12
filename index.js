let loginbtn1= document.querySelector("#login1");
let loginbtn2 = document.querySelector("#login2");

let getstartedbutton = document.querySelector("#getstarted");

loginbtn1.addEventListener("click",()=>{
    window.location.href="loginpage.html";
})
loginbtn2.addEventListener("click",()=>{
    window.location.href="loginpage.html";
})

getstartedbutton.addEventListener("click",()=>{
    console.log("clicked");
    window.location.href="newaccount.html";
})

