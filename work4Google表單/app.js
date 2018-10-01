const needs = document.querySelectorAll(".need")
const btn = document.querySelector(".btn")
const mailInput =document.querySelector("#mailInput")
const nameInput = document.querySelector("#nameInput")
const typeInputs = document.querySelectorAll(".typeInput")
const messageInput = document.querySelector(".messageInput")
const otherInput = document.querySelector(".otherInput")

function checkInput(){
    if(this.value || this.checked){
        this.parentElement.classList.remove("temp")
    } else if(!this.value){
        this.parentElement.classList.add("temp")
    }
}

function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!regex.test(email)) {
        return false;
    }else{
        return true;
    }
}

function checkEmail(){
    console.log(IsEmail(this.value))
    if(!IsEmail(this.value)){
        this.parentElement.classList.remove("temp")
        this.parentElement.classList.add("temp-email")
    }
    // 請輸入有效的電子郵件地址
}

mailInput.addEventListener("keyup",checkInput)
mailInput.addEventListener("keyup",checkEmail)
nameInput.addEventListener("keyup",checkInput)
typeInputs.forEach(type=>type.addEventListener("change",checkInput))
messageInput.addEventListener("keyup",checkInput)

function handlesubmit(e){
    e.preventDefault()
    if(!mailInput.value)mailInput.parentElement.classList.add("temp")
    if(!nameInput.value)nameInput.parentElement.classList.add("temp")
    if(!messageInput.value)messageInput.parentElement.classList.add("temp")

    let typeInputsChecked = false
    typeInputs.forEach(typeInput => {
        if(typeInput.checked === true) typeInputsChecked= true
    })
    if(!typeInputsChecked)typeInputs[0].parentElement.classList.add("temp")

    if(IsEmail(mailInput.value) && nameInput.value &&typeInputsChecked &&messageInput.value){
        let type;
        typeInputs.forEach(typeInput=>{
            if(typeInput.checked === true) type=typeInput.value 
        })

        console.log("mail: "+mailInput.value)
        console.log("name: "+nameInput.value)
        console.log("type: "+type)
        console.log("message: "+messageInput.value)
        console.log("other: "+otherInput.value)

        alert("mail: "+mailInput.value +"\n"+
            "name: "+nameInput.value+"\n"+
            "type: "+type+"\n"+
            "message: "+messageInput.value+"\n"+
            "other: "+otherInput.value)
    }
}


btn.addEventListener("click", handlesubmit)

