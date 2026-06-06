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

// Global UI handlers moved from inline templates
document.addEventListener('DOMContentLoaded', function () {
    // Toggle filtro dropdown when clicking button
    const filtrarBtn = document.querySelector('.filtrar-btn');
    const filtroDropdown = document.getElementById('filtroDropdown');
    if (filtrarBtn && filtroDropdown) {
        filtrarBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            filtroDropdown.classList.toggle('ativo');
        });
    }
});

document.addEventListener('click', function (event) {
    // Close filtro dropdown when clicking outside
    const filtroContainer = document.querySelector('.filtrar-container');
    if (filtroContainer && !filtroContainer.contains(event.target)) {
        const filtro = document.getElementById('filtroDropdown');
        if (filtro) filtro.classList.remove('ativo');
    }

    // Close sidenav when clicking overlay
    const overlay = document.getElementById('overlay');
    const menu = document.getElementById('mobile-menu');
    if (overlay && overlay.contains(event.target)) {
        overlay.classList.remove('active');
        if (menu) menu.classList.remove('active');
    }
});

// Sidenav toggle
document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.querySelector('.sidenav-trigger');
    const overlay = document.getElementById('overlay');
    const menu = document.getElementById('mobile-menu');
    if (trigger && overlay && menu) {
        trigger.addEventListener('click', function () {
            menu.classList.toggle('active');
            overlay.classList.toggle('active');
        });
    }

    // Confirm action links (data-confirm)
    document.querySelectorAll('.confirm-action').forEach(el => {
        el.addEventListener('click', function (e) {
            const msg = this.getAttribute('data-confirm');
            if (msg && !confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // Open modal for retirar (data attributes)
    document.querySelectorAll('.btn-open-modal-ship').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.doacaoId;
            const nome = this.dataset.doacaoNome;
            const form = document.getElementById('formRetirarPreco');
            if (form) form.action = '/admin/doacoes/retirar/' + id;
            const texto = document.getElementById('modalTextoItem');
            if (texto) texto.innerText = "O item (" + nome + ") foi retirado. Insira o preço de venda para o estoque:";
            const modal = document.getElementById('modalPreco');
            if (modal) modal.style.display = 'flex';
        });
    });

    // Close modal buttons
    document.querySelectorAll('.btn-close-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = document.getElementById('modalPreco');
            if (modal) modal.style.display = 'none';
        });
    });

    // Apply background images from data attribute
    document.querySelectorAll('.banner-bg-img[data-bg-image]').forEach(el => {
        el.style.backgroundImage = 'url(' + el.dataset.bgImage + ')';
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const senhaInput = document.getElementById("senha");
    const toggleSenhaBtn = document.getElementById("toggle-senha");

    if (toggleSenhaBtn && senhaInput) {
        toggleSenhaBtn.addEventListener("click", function () {
            // Verifica o tipo atual e altera
            if (senhaInput.type === "password") {
                senhaInput.type = "text";
                toggleSenhaBtn.textContent = "visibility"; // Altera o ícone para o olho aberto
                toggleSenhaBtn.style.color = "var(--amarelo-brecho)"; // Dá um destaque visual
            } else {
                senhaInput.type = "password";
                toggleSenhaBtn.textContent = "visibility_off"; // Retorna para o olho riscado
                toggleSenhaBtn.style.color = "#B3B3B3";
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const heroCarousel = document.querySelector('.hero-carousel');
    if (!heroCarousel) return;

    const slides = Array.from(heroCarousel.querySelectorAll('.carousel-images .hero-image'));
    const dots = Array.from(heroCarousel.querySelectorAll('.carousel-dots .dot'));
    if (!slides.length || slides.length !== dots.length) return;

    let currentSlide = slides.findIndex(slide => slide.classList.contains('active'));
    if (currentSlide < 0) currentSlide = 0;

    function goToSlide(index) {
        slides.forEach((slide, idx) => slide.classList.toggle('active', idx === index));
        dots.forEach((dot, idx) => dot.classList.toggle('active', idx === index));
        currentSlide = index;
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', function () {
            goToSlide(index);
        });
    });

    let autoSlideTimer = setInterval(function () {
        goToSlide((currentSlide + 1) % slides.length);
    }, 5000);

    heroCarousel.addEventListener('mouseenter', function () {
        clearInterval(autoSlideTimer);
    });

    heroCarousel.addEventListener('mouseleave', function () {
        clearInterval(autoSlideTimer);
        autoSlideTimer = setInterval(function () {
            goToSlide((currentSlide + 1) % slides.length);
        }, 5000);
    });
});