const todoInput = document.querySelector('.input-form')
const todoList = document.querySelector('ul')
const todos = document.querySelectorAll('li')
const removeBtns = document.querySelectorAll('.remove')

function handleDone(){
    this.classList.toggle("disabled")
}

function handleRemove(){
    this.parentElement.remove()
    console.log(this.parentElement)
}

function handleAdd(e){
    e.preventDefault()
    const todo = todoInput.querySelector('input').value
    const item = document.createElement("li")
    const removeBtn = document.createElement("div")
    removeBtn.textContent = "X"
    removeBtn.classList.add("remove","btn","btn-danger")
    item.innerHTML = todo+" "
    item.appendChild(removeBtn)
    item.classList.add("list-group-item")
    todoList.appendChild(item)
    item.addEventListener("click",handleDone)
    removeBtn.addEventListener("click",handleRemove)
    todoInput.reset()
}

todoInput.addEventListener("submit",handleAdd)
todos.forEach(todo=>todo.addEventListener("click",handleDone))
removeBtns.forEach(btn=>btn.addEventListener("click",handleRemove))



