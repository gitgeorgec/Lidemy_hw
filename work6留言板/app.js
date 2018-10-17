const url = "http://localhost:8080/myapp/API.php";
const LoginUser = document.querySelector(".user")
const messageList = document.querySelector(".message_list")
const mainInput = document.querySelector(".main_input")
const viewPage = document.querySelector("#page")
const pageList = document.querySelector(".page_list")
let userId =""
let userName =""
let page =1

mainInput.addEventListener("submit",handelePostSubmit)
viewPage.addEventListener("change",handeleChangePage)

function toggleEditForm(e){
    const editForms = e.target.parentElement.parentElement.querySelectorAll(".user_message")
    console.log(editForms[0])
    editForms[0].classList.toggle("close")
    editForms[1].classList.toggle("close")
    // editForms.forEach(form=>form.classList.toggle("close"))
}

function toggleResponseForm(e){
    const message = this.parentElement.querySelector(".sub_input")
    message.classList.toggle("close")
}

function handelePostSubmit(e){
    e.preventDefault()
    const message = e.target.message.value
    fetch(`${url}?create=1&username=${userName}&message=${message}`,{
    })
    .then(res=>{
        mainInput.reset()
        messageList.innerHTML=""
        getMessageList()
    })
}

function handelePost(e){
    e.preventDefault()
    const submessage = e.target.submessage.value
    const id = e.target.id.value
    var formData = new FormData();
    formData.append('id', id);
    formData.append('submessage', submessage);
    formData.append('username', userName);
    fetch(url, { method: 'POST', body: formData })
    .then(function (response) {
        messageList.innerHTML=""
        getMessageList()
        return response.text();
    })
    .then(function (body) {
        console.log(body);
    });

}

function handleEditSubmit(e){
    e.preventDefault()
    const updateId = e.target.id.value
    const message = e.target.update.value
    fetch(`${url}?update=${message}&updateId=${updateId}`,{
    })
    .then(res=>{
        toggleEditForm(e)
        const oldMessage = e.target.parentElement.parentElement.querySelector(".user_message >p")
        oldMessage.textContent = message
    })

}

function handleDeleteSubmit(e){
    e.preventDefault()
    const id = e.target.delete.value
    console.log(id)
    // const formData = new FormData();
    // formData.append('delete', id);
    fetch(url+"?delete="+id)
    .then(res=>{
        this.parentElement.remove(e)
    })
}

function handeleChangePage(e){
    page = parseInt(e.target.value)
    messageList.innerHTML=""
    getMessageList()
}

function getMessageList(){
    fetch(`${url}?Message`)
    .then(res=>res.json())
    .then(res=>{
        const MainList = res.filter(message=>message.SubType==0)
        const SubList = res.filter(message=>message.SubType==1)
        let start = (page-1)*10
        let end = Math.min(start+10,MainList.length)
        const list = []
        for(let i=start; i< end; i++){
            list.push(createMsgBoard(MainList[i]))
        }
        function createMsgBoard(message){
            const divNode = document.createElement("div")
            divNode.classList.add('message')
            function userEditpart(item){
                return`                    
                <div class='user_message close'>
                    <form method='GET' class='edit'>
                        <input type='text' name='id' value=${item.id} hidden>
                        <textarea name='update' style='width:100%; height:100%;'>${item.message}</textarea>
                        <br> <button type='submit' class='sendbtn'>send</button>
                    </form>
                    <button class='editbtn'>cancel</button>
                </div>
                <form method='GET' class='delete' style='display:inline'>
                    <input type='text' name='delete' value=${item.id} hidden>
                    <button type='submit' class='deletebtn'>delete</button>
                </form>`
            } 
            divNode.innerHTML =  `
                    <div> 
                        ${message.username}
                        <br> ${message.date}
                    </div>
                    <div class='user_message'>
                        <p>${message.message}</p>
                        <br> ${message.username===userName?"<button class='editbtn'>edit</button>":''}
                    </div>
                    ${message.username===userName?userEditpart(message):''}
                    ${
                        SubList.filter(item=>{
                            return item.belongTo === message.id
                        }).map(item=>{
                            return `
                            <div class='message'>
                                <div> 
                                    ${item.username}
                                    <br> ${item.date}
                                </div>
                                <div class='user_message'>
                                    <p>${item.message}</p>
                                    <br> ${item.username===userName?"<button class='editbtn'>edit</button>":''}
                                </div>
                                ${item.username===userName?userEditpart(item):''}
                            </div>
                            `
                        }).join("")
                    }
                    
                    <button class='responbtn'>response</button>
                    <form class='message sub_input close' method='POST'> ${userName} response
                        <input type='text' name='id' value=${message.id} style='display:none'>
                        <div class='user-info'>
                            <input type='text' name=username value="${userName}" style='display:none' required>
                        </div>
                        <textarea name='submessage' id='' cols='30' rows='2' required></textarea>
                        <button type='submit' class='submitbtn'>Leave comment</button>
                    </form>
            `
            return divNode
        } 
        return list
    })
    .then(list=>{
        list.forEach(item=>{
            messageList.appendChild(item)
        })
    })
    .then(res=>{
        //addEventListener to new DOM element
        const responbtns = document.querySelectorAll(".responbtn")
        const responsForms = document.querySelectorAll(".sub_input")
        const editBtns = document.querySelectorAll(".editbtn")
        const editForms = document.querySelectorAll(".edit")
        const deleteFroms = document.querySelectorAll(".delete")
        editBtns.forEach(editBtn=>editBtn.addEventListener("click", toggleEditForm))
        responbtns.forEach(responbtn=>responbtn.addEventListener("click", toggleResponseForm))
        // subMessages.forEach(message=>message.addEventListener("click", preventBubble))
        // editForms.forEach(form=>form.addEventListener("click", preventBubble))
        editForms.forEach(form=>form.addEventListener("submit",handleEditSubmit))
        deleteFroms.forEach(form=>form.addEventListener("submit", handleDeleteSubmit))
        responsForms.forEach(form=>form.addEventListener("submit",handelePost))
    })
}

//get Login user
fetch(`${url}?id`)
.then(res=>res.json())
.then(res=>{
    if(res[0]==="not login"){
        window.location = "http://localhost:8080/myapp/login.php";
    }
    userId = res.id
    userName = res.username
    LoginUser.textContent = userName
    //get message from data base
    getMessageList()
} 
)

fetch(`${url}?page`)
.then(res=>res.json())
.then(maxPage=>{
    for(let i=1; i<=maxPage; i++){
        const optionItem = document.createElement("option")
        optionItem.value = i
        optionItem.innerText = i
        viewPage.appendChild(optionItem)
    }
})

