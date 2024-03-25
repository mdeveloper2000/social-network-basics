const pictureForm = document.querySelector('#updatePicture')
const perfilForm = document.querySelector('#updateProfile')

pictureForm.addEventListener('submit', async (e) => {
        
    e.preventDefault()

    const formData = new FormData()
    const file = document.querySelector("#picture")
    formData.append("query", "updatePicture")
    formData.append("picture", file.files[0])

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
            const fotoAtualizada = document.querySelector('.fotoAtualizada')
            fotoAtualizada.src = "../fotos/" + data
        }
        else {
            console.log('Erro ao tentar salvar publicação')
        }            
    })

})

perfilForm.addEventListener('submit', async (e) => {
        
    e.preventDefault()

    const formData = new FormData()
    const about = perfilForm.about.value
    formData.append("query", "updateProfile")
    formData.append("about", about)

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
            console.log('Perfil atualizado com sucesso')
        }
        else {
            console.log('Erro ao tentar atualizar perfil')            
        }            
    })

})

function selecionarFoto() {
    const btn = document.querySelector('.foto')
    btn.click()
}

window.onload = async () => {
    const formData = new FormData()
    const about = document.querySelector('#about')
    formData.append("query", "getProfile")
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
            about.value = data.about
        }
        else {
            console.log('Erro ao tentar recuperar perfil')            
        }            
    })
}