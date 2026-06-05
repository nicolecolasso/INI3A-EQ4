document.addEventListener("DOMContentLoaded", function () {
    // Busca todas as áreas de upload que tenham um input de arquivo e uma div de preview
    const uploadAreas = document.querySelectorAll('.upload-area');

    uploadAreas.forEach(area => {
        const fileInput = area.querySelector('input[type="file"]');
        const previewDiv = area.querySelector('.upload-preview');
        const selectBtn = area.querySelector('.btn-selecionar');

        // 1. Vincula o clique do botão ao input de arquivo escondido
        if (selectBtn && fileInput) {
            selectBtn.addEventListener('click', function () {
                fileInput.click();
            });
        }

        // 2. Escuta quando o usuário escolhe ou muda de imagem
        if (fileInput && previewDiv) {
            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];

                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function (e) {
                        // Limpa o ícone ou imagem antiga e injeta a nova imagem
                        previewDiv.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: contain;">`;
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        }
    });
});