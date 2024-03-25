const form = document.querySelector('form')

form.addEventListener('submit', async (e) => {
        
    e.preventDefault()

    const search = form.search.value

    const formData = new FormData()
    formData.append("query", "search")
    formData.append("search", search)
    
    try {
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
                console.log('Erro ao pesquisar')             
            }
            else {
                const lista = document.querySelector('.lista')
                lista.innerHTML = ''
                data.forEach(usuario => {
                    const div = document.createElement('div')
                    const span = document.createElement('span')
                    const img = document.createElement('img')
                    const button = document.createElement('button')
                    div.classList.add("banner")
                    span.innerHTML = usuario.username
                    div.appendChild(span)
                    img.classList.add("avatar")
                    img.src = "../fotos/" + usuario.picture
                    div.appendChild(img)
                    button.classList.add("button-md", "button-information")
                    button.innerHTML = '<i class="fa-solid fa-address-card"></i> Ver perfil...'
                    button.onclick = () => {
                        verPerfil(usuario.id)
                    }
                    div.appendChild(button)
                    lista.appendChild(div)
                })
            }            
        })
    } 
    catch(error) {
        console.log(error)
    }

    async function verPerfil(id) {
        const formData = new FormData();
        formData.append("query", "readProfile");
        formData.append("id", id);
        await fetch('../controllers/UserController.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json, text/plain, */*'                
            }
        })
        .then((res) => res.json())
        .then((data) => {
            if(data !== null) {
                const lista = document.querySelector(".lista")
                lista.innerHTML = ''
                const button = document.createElement("button")
                if(data.friendship === null) {
                    button.classList.add("button-md", "button-information", "button-align-center")
                    const btnClass = "btnClass" + data.id
                    button.classList.add(btnClass)
                    button.innerHTML = '<i class="fa-solid fa-user-plus"></i> Adicionar'                    
                    button.onclick = () => {
                        enviarConvite(btnClass, data.id)
                    }
                }
                else if(data.friendship.accepted === "YES") {
                    button.classList.add("button-md", "button-information", "button-align-center")
                    button.innerHTML = '<i class="fa-solid fa-handshake"></i> Amizade confirmada'
                }
                else if(data.friendship.accepted === "NO") {
                    button.classList.add("button-md", "button-warning", "button-align-center")
                    button.innerHTML = '<i class="fa-solid fa-hourglass-start"></i> Aguardando resposta...'
                }
                
                lista.appendChild(button)
                const nome = document.createElement("div")
                nome.classList.add("nome")
                nome.innerHTML = data.username
                lista.appendChild(nome)
                const img = document.createElement("img")
                img.classList.add("perfil-img")
                img.src = "../fotos/" + data.picture
                lista.appendChild(img)
                const div = document.createElement('div')
                div.classList.add('sobre')
                div.innerHTML = data.about
                lista.appendChild(div)
            }
            else {
                console.log('Erro ao tentar verificar perfil')            
            }            
        })
    }

    async function enviarConvite(btnClass, id) {
        const formData = new FormData()
        formData.append("query", "sendInvite")
        formData.append("id", id)
        await fetch('../controllers/UserController.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json, text/plain, */*'                
            }
        })
        .then((res) => res.json())
        .then((data) => {            
            if(data === true) {
                const button = document.querySelector('.' + btnClass)
                button.innerHTML = ''
                button.classList.add("button-md", "button-warning")
                button.innerHTML = '<i class="fa-solid fa-hourglass-start"></i> Aguardando resposta...'
                button.disabled = true
            }
            else {
                console.log('Erro ao enviar convite')
            }
        })
    }

})