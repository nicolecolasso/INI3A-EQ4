document.addEventListener("DOMContentLoaded", function() {
    // 1. Cria dinamicamente o fundo escuro (overlay) para o menu mobile
    const overlay = document.createElement("div");
    overlay.className = "sidenav-overlay";
    document.body.appendChild(overlay);

    // 2. Seleciona os elementos criados no Blade
    const trigger = document.querySelector(".sidenav-trigger");
    const sidebar = document.getElementById("mobile");

    // Função para abrir/fechar o menu mobile
    function toggleMenu() {
        sidebar.classList.toggle("active");
        overlay.classList.toggle("active");
    }

    // 3. Aplica os eventos de clique
    if (trigger && sidebar) {
        // Abre ao clicar no botão hambúrguer amarelo
        trigger.addEventListener("click", function(e) {
            e.preventDefault();
            toggleMenu();
        });

        // Fecha se clicar no fundo escuro
        overlay.addEventListener("click", toggleMenu);
    }
});