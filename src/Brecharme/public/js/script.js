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
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        const input = btn.closest('.input-with-icon')?.querySelector('input');
        if (!input) return;

        btn.addEventListener('click', function () {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'visibility' : 'visibility_off';
            this.style.color = isPassword ? 'var(--amarelo-brecho)' : '#B3B3B3';
        });
    });

    document.querySelectorAll('.btn-table-details[data-toggle-target]').forEach(btn => {
        const target = document.getElementById(btn.dataset.toggleTarget);
        if (!target) return;

        btn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            const isOpen = target.style.display === 'table-row' || target.style.display === 'block';
            if (isOpen) {
                target.style.display = 'none';
                this.classList.remove('ativo');
                if (icon) icon.innerText = 'expand_more';
            } else {
                target.style.display = 'table-row';
                this.classList.add('ativo');
                if (icon) icon.innerText = 'expand_less';
            }
        });
    });
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

/**
 * Controle do Modal de Precificação e Retirada de Doações
 */
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('modalPreco');
    const form = document.getElementById('formRetirarPreco');
    const textoItem = document.getElementById('modalTextoItem');

    // Validação de segurança caso o modal não exista na página atual
    if (!modal || !form || !textoItem) return;

    // Captura o clique em qualquer botão de "Confirmar Retirada"
    document.querySelectorAll('.btn-abrir-modal').forEach(botao => {
        botao.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome');
            
            // Atualiza o texto interno do Modal com o nome do produto
            textoItem.innerText = "Defina o valor de mercado para o item: " + nome;
            
            // Monta dinamicamente a URL baseada no domínio atual da aplicação (independente de estar em localhost ou produção)
            const urlBase = window.location.origin;
            form.action = `${urlBase}/admin/doacoes/retirar/${id}`;
            
            // Exibe o modal na tela
            modal.style.display = "flex";
            
            // Coloca o foco automático no campo de preço após abrir
            const inputPreco = document.getElementById('preco_venda');
            if (inputPreco) inputPreco.focus();
        });
    });

    // Fecha o modal ao clicar em Cancelar ou no botão de fechar
    document.querySelectorAll('.btn-close-modal').forEach(botao => {
        botao.addEventListener('click', function() {
            modal.style.display = "none";
            form.reset(); // Limpa o campo digitado ao fechar
        });
    });

    // Fecha o modal se o usuário clicar em qualquer área cinza fora do conteúdo principal
    window.addEventListener('click', function(evento) {
        if (evento.target === modal) {
            modal.style.display = "none";
            form.reset();
        }
    });
});

/**
 * GERENCIAMENTO DE MODAL DE DETALHES DAS RESERVAS
 */
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modalDetalhesReserva");
    const btnFechar = document.getElementById("btnFecharModal");
    const botoesAbrir = document.querySelectorAll(".btn-abrir-detalhes");

    // Elementos internos do modal para preenchimento
    const modalId = document.getElementById("modalIdReserva");
    const modalData = document.getElementById("modalDataReserva");
    const modalStatus = document.getElementById("modalStatusReserva");
    const modalTotal = document.getElementById("modalTotalReserva");
    const modalListaProdutos = document.getElementById("modalListaProdutos");

    if (botoesAbrir.length > 0 && modal) {
        botoesAbrir.forEach(botao => {
            botao.addEventListener("click", function (e) {
                e.preventDefault();

                // Recuperando os dados anexados ao botão
                const idCompra = this.getAttribute("data-id");
                const dataCompra = this.getAttribute("data-data");
                const statusCompra = this.getAttribute("data-status");
                const totalCompra = this.getAttribute("data-total");
                
                // Trata a string de produtos em formato JSON seguro
                let produtos = [];
                try {
                    produtos = JSON.parse(this.getAttribute("data-produtos"));
                } catch (err) {
                    console.error("Erro ao processar dados dos produtos:", err);
                }

                // Injetando os metadados textuais básicos
                modalId.textContent = `#${idCompra}`;
                modalData.textContent = dataCompra;
                modalStatus.textContent = statusCompra;
                modalTotal.textContent = totalCompra;

                // Limpa a lista interna antes de renderizar os novos produtos
                modalListaProdutos.innerHTML = "";

                if (!produtos || produtos.length === 0) {
                    modalListaProdutos.innerHTML = `<p style="color:#888; font-size:0.9rem;">Nenhum detalhe de peça encontrado para esta reserva.</p>`;
                } else {
                    // Loop renderizando cada produto associado à compra
                    produtos.forEach(item => {
                        // Trata se o relacionamento veio direto como produto ou através do pivot da pivot table
                        const produtoReal = item.produto ? item.produto : item;
                        
                        const nome = produtoReal.nome || 'Peça Brecharme';
                        const categoria = produtoReal.categoria || 'Geral';
                        const valor = produtoReal.preco || produtoReal.valor || item.preco || 0;
                        
                        // Define a imagem com base no caminho configurado no sistema
                        let imgUrl = '/img/fallback-placeholder.png'; // Fallback padrão caso não exista imagem
                        if (produtoReal.imagem) {
                            imgUrl = produtoReal.imagem.startsWith('http') ? produtoReal.imagem : `/storage/${produtoReal.imagem}`;
                        } else if (produtoReal.caminho_img) {
                            imgUrl = `/` + produtoReal.caminho_img.replace(/^\//, '');
                        }

                        const produtoHTML = `
                            <div class="modal-produto-row">
                                <img src="${imgUrl}" alt="${nome}" class="modal-prod-img" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\' viewBox=\'0 0 24 24\' fill=\'%23aaa\'><path d=\'M18 4V3c0-.55-.45-1-1-1H7c-.55 0-1 .45-1 1v1H2v2h2v15c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6h2V4h-4zM9.5 12c.83 0 1.5-.67 1.5-1.5S10.33 9 9.5 9 8 9.67 8 10.5 8.67 12 9.5 12zm5 0c.83 0 1.5-.67 1.5-1.5S15.33 9 14.5 9s-1.5.67-1.5 1.5.67 1.5 1.5 1.5z\'/></svg>'">
                                <div class="modal-prod-info">
                                    <h5>${nome}</h5>
                                    <p>Categoria: ${categoria}</p>
                                    <p><strong>R$ ${parseFloat(valor).toFixed(2).replace('.', ',')}</strong></p>
                                </div>
                            </div>
                        `;
                        modalListaProdutos.insertAdjacentHTML("beforeend", produtoHTML);
                    });
                }

                // Exibe a modal adicionando a classe ativa
                modal.classList.add("modal-active");
            });
        });

        // Evento para fechar clicando no 'X'
        btnFechar.addEventListener("click", function () {
            modal.classList.remove("modal-active");
        });

        // Evento para fechar clicando fora da caixa branca (no fundo escuro)
        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                modal.classList.remove("modal-active");
            }
        });
    }
});