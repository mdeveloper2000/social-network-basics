const postForm = document.querySelector('#postForm')

postForm.addEventListener('submit', async (e) => {
        
    e.preventDefault()

    const post_text = postForm.post_text.value

    const formData = new FormData()
    formData.append("query", "save")
    formData.append("post_text", post_text)    
    
    try {
        await fetch('../controllers/PostController.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json, text/plain, */*'                
            }
        })
        .then((res) => res.json())
        .then((data) => {            
            if(data === null) {
                console.log('Erro ao tentar salvar publicação')             
            }
            else {
                const id = data
                const formDataPost = new FormData()
                formDataPost.append("query", "get")
                formDataPost.append("id", id) 
                fetch('../controllers/PostController.php', {
                    method: 'POST',
                    body: formDataPost,
                    headers: {
                        'Accept': 'application/json, text/plain, */*'                
                    }
                })
                .then((res) => res.json())
                .then((data) => {
                    if(data === null) {
                        console.log('Erro ao tentar recuperar publicação')
                    }
                    else {
                        postForm.reset()
                        addPost(data)
                    }
                })
            }            
        })
    } 
    catch(error) {
        console.log(error)
    }

})

function addPost(post) {
    const feed_publicacoes = document.querySelector('.feed-publicacoes')
    const container = document.createElement('div')
    container.classList.add('publicacoes')
    const texto = document.createElement('div')
    const data_publicacao = document.createElement('div')
    data_publicacao.classList.add('data-publicacao')
    texto.innerHTML = post.post_text
    data_publicacao.innerHTML = post.post_date
    container.appendChild(texto)
    container.appendChild(data_publicacao)
    feed_publicacoes.prepend(container)
}

window.onload = async () => {
    
    const formData = new FormData()
    formData.append("query", "list")       
    
    try {
        await fetch('../controllers/PostController.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json, text/plain, */*'                
            }
        })
        .then((res) => res.json())
        .then((data) => {            
            if(data === null) {
                console.log('Erro ao listar publicações')             
            }            
            else {                
                data.forEach(publicacao => {
                    addPost(publicacao)                   
                })
            }            
        })
    } 
    catch(error) {
        console.log(error)
    }

    try {
        const formData = new FormData()
        formData.append("query", "listFriendshipsHomePage") 
        await fetch('../controllers/UserController.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json, text/plain, */*'
            }
        })
        .then((res) => res.json())
        .then((data) => {            
            if(data === null) {
                console.log('Lista retornou sem resultados')
            }            
            else {
                const lista_amigos = document.querySelector(".lista-amigos")                
                data.forEach(usuario => {
                    const div = document.createElement("div")
                    div.classList.add("amizade")
                    const img = document.createElement("img")
                    img.src = "../fotos/" + usuario.picture
                    div.appendChild(img)
                    const span = document.createElement("span")
                    span.innerHTML = usuario.username
                    div.appendChild(span)
                    lista_amigos.appendChild(div)
                })
            }            
        })
    } 
    catch(error) {
        console.log(error)
    }

}