const btns = document.querySelectorAll(".btn")
const result = document.querySelector(".result")

let NewCalculate = true

function resetZero(){
    result.innerHTML = "0"
    NewCalculate = true
}

function inputNumber(e){
    if(NewCalculate){
        result.innerHTML = e.target.dataset.btn;
        NewCalculate = false
    }else {
        result.innerHTML += e.target.dataset.btn;
    }
}

function calculate(){
    ans = eval(result.innerHTML)
    result.innerHTML = ans
    NewCalculate = true
}

btns.forEach(btn => {
    if(btn.dataset.btn==="AC"){
        btn.addEventListener("click", resetZero)
    }else if(btn.dataset.btn === "="){
        btn.addEventListener("click", calculate)
    }else{
        btn.addEventListener("click", inputNumber)
    }
})