import pyautogui
import time
import urllib.parse
import webbrowser # Abre o navegador direto na URL certa
import psycopg2 
from psycopg2.extras import RealDictCursor

def enviar_mensagem_pyautogui(numero, mensagem):
    # 1. Transforma o texto para o formato de URL
    mensagem_codificada = urllib.parse.quote(mensagem)
    
    # 2. Monta o link DIRETO do chat com o número e o texto
    link_whatsapp = f"https://web.whatsapp.com/send?phone={numero}&text={mensagem_codificada}"
        
    # 3. Abre uma nova aba direto na URL correta
    print(f"    🚀 Abrindo aba para o número: {numero}")
    webbrowser.open_new_tab(link_whatsapp)
    
    # 4. Espera o WhatsApp Web carregar o chat por completo
    time.sleep(15) 
    

    
    # 6. Dá o ENTER final para enviar a mensagem
    pyautogui.press('enter')
    print(f"    ✅ Enter pressionado para {numero}!")
    
    # 7. Espera a mensagem subir e fecha a aba do cliente atual
    time.sleep(3)
    pyautogui.hotkey('ctrl', 'w') 
    time.sleep(2)

# 🎯 CONFIGURAÇÃO DA CONEXÃO COM O PGADMIN
try:
    conexao = psycopg2.connect(
        host="127.0.0.1",
        user="postgres",       
        password="postgres",  # Sua senha configurada
        database="brecharme",  
        port="5432"            
    )
    
    cursor = conexao.cursor(cursor_factory=RealDictCursor)

    # 1. 🎯 CORRIGIDO: Alinhamento perfeito da linha abaixo para o Python não travar
    cursor.execute("SELECT * FROM comunicado WHERE status = 'pendente' ORDER BY data_envio ASC;")
    comunicados_pendentes = cursor.fetchall()

    if comunicados_pendentes:
        
        # 2. Busca todos os clientes ativos
        cursor.execute("SELECT telefone FROM users WHERE admin = false AND excluido = false;")
        clientes = cursor.fetchall()
        
        # 3. LOOP PRINCIPAL: Passa de comunicado em comunicado
        for comp_index, comunicado in enumerate(comunicados_pendentes, start=1):

                    
            texto_completo = f"*{comunicado['assunto']}*\n\n{comunicado['mensagem']}"

            # 4. LOOP SECUNDÁRIO: Envia o comunicado atual para todos os clientes
            for cli_index, cliente in enumerate(clientes, start=1):
                if cliente['telefone']:
                    telefone_limpo = ''.join(filter(str.isdigit, str(cliente['telefone'])))
                    
                    if len(telefone_limpo) <= 11:
                        telefone_limpo = "55" + telefone_limpo
                    
                    print(f" -> Enviando para cliente {cli_index}/{len(clientes)}")
                    enviar_mensagem_pyautogui(telefone_limpo, texto_completo)

            # 5. Atualiza o status no banco após enviar para todos os clientes
            cursor.execute(
                "UPDATE comunicado SET status = 'enviado' WHERE id_comunicado = %s;", 
                (comunicado['id_comunicado'],)
            )
            conexao.commit() 


except psycopg2.Error as erro:
    print(f"❌ Erro ao ligar ao PostgreSQL/pgAdmin: {erro}")
finally:
    if 'conexao' in locals() and conexao:
        cursor.close()
        conexao.close()