const main = document.querySelector(".container") 
let url = "https://api.twitch.tv/helix/streams?first=20&game_id=21779"

fetch(url,{
    method: 'GET',
    headers: new Headers({
        "Client-ID": "hawrs4wf69t1vphcgdes0xcodfop3v"
    })
})
.then(function(res){
    return res.json()
})
.then(function(jsonfile){
    const Imgurl = jsonfile.data
        .map(item=>`<div class="box"><img src=${item.thumbnail_url} /></div>`)
        .map(string => string.replace("{width}x{height}", "480x360")).join("")
    const streamTitle = jsonfile.data
        .map(item=>`<h3>${item.title}</h3>`)
    //render stream thumbnail picture     
    main.innerHTML = Imgurl

    // get id form previous data and 
    const user = jsonfile.data.map(item=>`id=${item.user_id}&`).join("")
    //send a new request to get user-info
    fetch(`https://api.twitch.tv/helix/users?${user}`,{
        method: 'GET',
        headers: new Headers({
            "Client-ID": "hawrs4wf69t1vphcgdes0xcodfop3v"
        })
    })
    .then(res=>{
        return res.json()
    }).then(userfile=>{
        let i = 0
        //render user-info
        const boxes = document.querySelectorAll(".box")
        boxes.forEach(box=>{
            const Userinfo = document.createElement("div")
            Userinfo.classList.add("info")
            Userinfo.innerHTML = `
                <img src=${userfile.data[i].profile_image_url}>
                <div>
                ${streamTitle[i]}
                <hr>
                <h4>${userfile.data[i].display_name}</h4>
                <p>${userfile.data[i].description}</p>
                </div>`
                i++
                box.appendChild(Userinfo)
        })
    })
    
})




//xhml
// const xhmlrequest = new XMLHttpRequest
// let response

// xhmlrequest.open("GET",url )
// xhmlrequest.setRequestHeader("Client-ID","hawrs4wf69t1vphcgdes0xcodfop3v")

// xhmlrequest.onload = function(){
//     if(xhmlrequest.status < 400 && xhmlrequest.status == 200) {
//         response = JSON.parse(xhmlrequest.response)
//         const thumbnail_url = response.data
//         .map(item=>{
//             return `<div class="box"><img src=${item.thumbnail_url} ></div>`.replace("{width}x{height}", "480x360")
//         })
//         .join("")
        
//         main.innerHTML = thumbnail_url
//       }
// }
// xhmlrequest.send();