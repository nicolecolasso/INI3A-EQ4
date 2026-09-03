document.addEventListener("DOMContentLoaded", function () {
    
    /* ==========================================================================
       1. CONTROLE DO SLIDER / CARROSSEL DE PRODUTOS EM DESTAQUE (HOME)
       ========================================================================== */
    const sliderContainer = document.querySelector('.items-slider');
    
    if (sliderContainer) {
        const grid = sliderContainer.querySelector('.items-grid');
        const prevBtn = sliderContainer.querySelector('.slider-arrow.prev');
        const nextBtn = sliderContainer.querySelector('.slider-arrow.next');

        if (grid && prevBtn && nextBtn) {
            
            // Função que calcula e move o grid de itens de forma fluida
            const scrollSlider = (direction) => {
                const firstCard = grid.querySelector('.item-card');
                let scrollAmount = 260; // Fallback de segurança

                if (firstCard) {
                    // Calcula tamanho exato do card + o espaçamento (gap) dinâmico
                    const cardWidth = firstCard.getBoundingClientRect().width;
                    const gridGap = parseFloat(window.getComputedStyle(grid).gap) || 0;
                    scrollAmount = cardWidth + gridGap;
                }

                grid.scrollBy({
                    left: direction === 'next' ? scrollAmount : -scrollAmount,
                    behavior: 'smooth'
                });
            };

            // Eventos de clique nas setas
            nextBtn.addEventListener('click', () => scrollSlider('next'));
            prevBtn.addEventListener('click', () => scrollSlider('prev'));

            // Oculta as setas caso todos os produtos caibam na tela sem precisar de scroll
            const toggleArrowsVisibility = () => {
                if (grid.scrollWidth <= grid.clientWidth) {
                    prevBtn.style.opacity = '0';
                    prevBtn.style.pointerEvents = 'none';
                    nextBtn.style.opacity = '0';
                    nextBtn.style.pointerEvents = 'none';
                } else {
                    prevBtn.style.opacity = '1';
                    prevBtn.style.pointerEvents = 'auto';
                    nextBtn.style.opacity = '1';
                    nextBtn.style.pointerEvents = 'auto';
                }
            };

            toggleArrowsVisibility();
            window.addEventListener('resize', toggleArrowsVisibility);
        }
    }

    /* ==========================================================================
       2. PREVIEW DE UPLOAD DE IMAGENS
       ========================================================================== */
    const uploadAreas = document.querySelectorAll('.upload-area');
    uploadAreas.forEach(area => {
        const fileInput = area.querySelector('input[type="file"]');
        const previewDiv = area.querySelector('.upload-preview');
        const selectBtn = area.querySelector('.btn-selecionar');

        if (selectBtn && fileInput) {
            selectBtn.addEventListener('click', () => fileInput.click());
        }

        if (fileInput && previewDiv) {
            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewDiv.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: contain;">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    /* ==========================================================================
       3. DROPDOWN DE FILTROS (VITRINE)
       ========================================================================== */
    const filtrarBtn = document.querySelector('.filtrar-btn');
    const filtroDropdown = document.getElementById('filtroDropdown');
    if (filtrarBtn && filtroDropdown) {
        filtrarBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            filtroDropdown.classList.toggle('ativo');
        });
    }

    /* ==========================================================================
       4. ALTERNAR EXIBIÇÃO DE SENHAS
       ========================================================================== */
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

    /* ==========================================================================
       5. MENU RESPONSIVO (SIDENAV MOBILE)
       ========================================================================== */
    const trigger = document.querySelector('.sidenav-trigger');
    const overlay = document.getElementById('overlay');
    const menu = document.getElementById('mobile-menu');
    if (trigger && overlay && menu) {
        trigger.addEventListener('click', function () {
            menu.classList.toggle('active');
            overlay.classList.toggle('active');
        });
    }

    /* ==========================================================================
       6. CARROSSEL PRINCIPAL (HERO BANNER AUTOMÁTICO)
       ========================================================================== */
    const heroCarousel = document.querySelector('.hero-carousel');
    if (heroCarousel) {
        const slides = Array.from(heroCarousel.querySelectorAll('.carousel-images .hero-image'));
        const dots = Array.from(heroCarousel.querySelectorAll('.carousel-dots .dot'));
        
        if (slides.length && slides.length === dots.length) {
            let currentSlide = slides.findIndex(slide => slide.classList.contains('active'));
            if (currentSlide < 0) currentSlide = 0;

            const goToSlide = (index) => {
                slides.forEach((slide, idx) => slide.classList.toggle('active', idx === index));
                dots.forEach((dot, idx) => dot.classList.toggle('active', idx === index));
                currentSlide = index;
            };

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => goToSlide(index));
            });

            let autoSlideTimer = setInterval(() => {
                goToSlide((currentSlide + 1) % slides.length);
            }, 5000);

            heroCarousel.addEventListener('mouseenter', () => clearInterval(autoSlideTimer));
            heroCarousel.addEventListener('mouseleave', () => {
                clearInterval(autoSlideTimer);
                autoSlideTimer = setInterval(() => {
                    goToSlide((currentSlide + 1) % slides.length);
                }, 5000);
            });
        }
    }

    /* ==========================================================================
       7. LINKS COM CONFIRMAÇÃO DE AÇÃO DE SEGURANÇA
       ========================================================================== */
    document.querySelectorAll('.confirm-action').forEach(el => {
        el.addEventListener('click', function (e) {
            const msg = this.getAttribute('data-confirm');
            if (msg && !confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    /* ==========================================================================
    8. MODAL DE DETALHES DAS RESERVAS DO PERFIL
    ========================================================================== */
    const modalReserva = document.getElementById("modalDetalhesReserva");
    const btnFecharReserva = document.getElementById("btnFecharModal");
    const botoesAbrirReserva = document.querySelectorAll(".btn-abrir-detalhes");

    if (botoesAbrirReserva.length > 0 && modalReserva) {
        
        const modalId = document.getElementById("modalIdReserva");
        const modalData = document.getElementById("modalDataReserva");
        const modalStatus = document.getElementById("modalStatusReserva");
        const modalTotal = document.getElementById("modalTotalReserva");
        const modalListaProdutos = document.getElementById("modalListaProdutos");

        botoesAbrirReserva.forEach(botao => {
            botao.addEventListener("click", function (e) {
                e.preventDefault();

                modalId.textContent = `#${this.getAttribute("data-id")}`;
                modalData.textContent = this.getAttribute("data-data");
                modalStatus.textContent = this.getAttribute("data-status");

                modalListaProdutos.innerHTML = "";
                let produtos = [];
                
                try {
                    produtos = JSON.parse(this.getAttribute("data-produtos"));
                } catch (err) {
                    console.error("Erro ao processar produtos:", err);
                }

                let somaTotalCalculada = 0;

                if (!produtos || produtos.length === 0) {
                    modalListaProdutos.innerHTML = `<p style='color:#888; font-size:0.9rem;'>Nenhum detalhe de peça encontrado.</p>`;
                } else {
                    produtos.forEach(prod => {
                        const valorNum = parseFloat(prod.valor || 0);
                        somaTotalCalculada += valorNum;

                        // Trata o caminho exato salvo na coluna 'caminho_img' da tabela 'produto'
                        let imgCaminho = prod.caminho_img || '';

                        // Remove a barra inicial se existir para padronizar
                        if (imgCaminho.startsWith('/')) {
                            imgCaminho = imgCaminho.substring(1);
                        }

                        // Pega o caminho base do projeto atual (ex: /26-brecharme/)
                        const baseUrl = window.location.pathname.split('/')[1] 
                            ? `/${window.location.pathname.split('/')[1]}/` 
                            : '/';

                        // Monta a URL completa exatamente igual ao asset() do Blade
                        let imgUrl = imgCaminho ? `${window.location.origin}${baseUrl}${imgCaminho}` : '';

                        modalListaProdutos.insertAdjacentHTML("beforeend", `
                            <div class="modal-produto-row">
                                <img src="${imgUrl}" 
                                    alt="${prod.nome || 'Produto'}" 
                                    class="modal-prod-img"
                                    onerror="this.onerror=null; this.style.display='none';">
                                <div class="modal-prod-info">
                                    <h5>${prod.nome || 'Peça do Brechó'}</h5>
                                    <p><strong>R$ ${valorNum.toFixed(2).replace('.', ',')}</strong></p>
                                </div>
                            </div>
                        `);
                    });
                }

                let totalAtributo = this.getAttribute("data-total");
                modalTotal.textContent = (totalAtributo && totalAtributo !== "R$ 0,00") 
                    ? totalAtributo 
                    : `R$ ${somaTotalCalculada.toFixed(2).replace('.', ',')}`;

                modalReserva.classList.add("modal-active");
            });
        });

        if (btnFecharReserva) {
            btnFecharReserva.addEventListener("click", () => modalReserva.classList.remove("modal-active"));
        }
    }

    /* ==========================================================================
    9. MODAL DE PRECIFICAÇÃO E RETIRADA DE DOAÇÕES (ADMIN)
    ========================================================================== */
    const modalPreco = document.getElementById('modalPreco');
    const formIntegrarPreco = document.getElementById('formIntegrarPreco');
    const textoItem = document.getElementById('modalTextoItem');

    if (modalPreco && formIntegrarPreco && textoItem) {
        document.querySelectorAll('.btn-abrir-modal, .btn-open-modal-ship').forEach(botao => {
        botao.addEventListener('click', function () {
            const id = this.dataset.id || this.dataset.doacaoId;
            const nome = this.dataset.nome || this.dataset.doacaoNome;

            textoItem.innerText = "Defina o valor de mercado para o item: " + nome;

            // Usa a URL já correta que o Laravel gerou, só troca o placeholder pelo id real
            const urlTemplate = this.dataset.url;
            formIntegrarPreco.action = urlTemplate.replace(':id', id);

            modalPreco.style.display = "flex";

            const inputPreco = document.getElementById('preco_venda');
            if (inputPreco) inputPreco.focus();
        });
    });

        document.querySelectorAll('.btn-close-modal').forEach(botao => {
            botao.addEventListener('click', () => {
                modalPreco.style.display = "none";
                formIntegrarPreco.reset();
            });
        });
    }

    // Renderização assíncrona de Background-images via Data Attributes
    document.querySelectorAll('.banner-bg-img[data-bg-image]').forEach(el => {
        el.style.backgroundImage = `url(${el.dataset.bgImage})`;
    });
});

/* ==========================================================================
   10. ESCUTAS DE CLIQUE ADICIONAIS EXTERNOS (FECHAMENTO DE OVERLAYS)
   ========================================================================== */
document.addEventListener('click', function (event) {
    const filtroContainer = document.querySelector('.filtrar-container');
    if (filtroContainer && !filtroContainer.contains(event.target)) {
        const filtro = document.getElementById('filtroDropdown');
        if (filtro) filtro.classList.remove('ativo');
    }

    const overlay = document.getElementById('overlay');
    const menu = document.getElementById('mobile-menu');
    if (overlay && overlay.contains(event.target)) {
        overlay.classList.remove('active');
        if (menu) menu.classList.remove('active');
    }

    const modalPreco = document.getElementById('modalPreco');
    if (modalPreco && event.target === modalPreco) {
        modalPreco.style.display = "none";
        const form = document.getElementById('formIntegrarPreco');
        if (form) form.reset();
    }

    const modalReserva = document.getElementById("modalDetalhesReserva");
    if (modalReserva && event.target === modalReserva) {
        modalReserva.classList.remove("modal-active");
    }
});

/*
* 11. PREVISUALIZAÇÃO DE COMUNICADOS ANTIGOS NO FORMULÁRIO DE CRIAÇÃO/EDIÇÃO DE COMUNICADOS  
*/

document.addEventListener('DOMContentLoaded', function () {
    const selectHistorico = document.getElementById('comunicado_historico');
    const inputAssunto = document.getElementById('assunto');
    const textareaMensagem = document.getElementById('mensagem');

    if (selectHistorico && inputAssunto && textareaMensagem) {
        selectHistorico.addEventListener('change', function () {
            // Pega a opção que foi selecionada pelo usuário
            const opcaoSelecionada = this.options[this.selectedIndex];

            if (opcaoSelecionada && opcaoSelecionada.value !== "") {
                // Captura os dados guardados nos atributos 'data-' da option
                const assuntoAntigo = opcaoSelecionada.getAttribute('data-assunto');
                const mensagemAntiga = opcaoSelecionada.getAttribute('data-mensagem');

                // Injeta os dados capturados diretamente nos inputs do seu _form.blade.php
                inputAssunto.value = assuntoAntigo;
                textareaMensagem.value = mensagemAntiga;
            } else {
                // Se o usuário selecionar a opção padrão "Selecione um comunicado", limpa os campos
                inputAssunto.value = '';
                textareaMensagem.value = '';
            }
        });
    }
});

/*
* 12. VISUALIZAÇÃO DO CAMPO DE LOCALIZAÇÃO NO FORMULÁRIO DE DOAÇÕES 
*/
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('necessita_retirada');
    const boxLocalizacao = document.getElementById('box-localizacao');
    const inputLocalizacao = document.getElementById('localizacao');

    function alternarVisibilidade() {
        if (checkbox.checked) {
            boxLocalizacao.style.display = 'block';
            inputLocalizacao.setAttribute('required', 'required');
        } else {
            boxLocalizacao.style.display = 'none';
            inputLocalizacao.removeAttribute('required');
            inputLocalizacao.value = '';
        }
    }

    checkbox.addEventListener('change', alternarVisibilidade);
    alternarVisibilidade(); 
});
