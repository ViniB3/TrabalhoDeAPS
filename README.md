# TrabalhoDeAPS

# 🍤 Sistema de Gestão - Petiscaria Nishida

Este projeto é um sistema web desenvolvido para a gestão de pedidos, mesas e caixa da Petiscaria Nishida.

## 🚀 Como Executar o Projeto

Como o sistema não utiliza banco de dados MySQL externo (usa sessão do PHP para persistência temporária), a execução é muito simples.

### Pré-requisitos
* PHP 7.4 ou superior instalado.
* Navegador Web.

### Opção 1: Usando o Servidor Embutido do PHP (Recomendado/Mais Rápido)

Se você tem o PHP instalado no Windows (ou usa VS Code), siga estes passos:

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/ViniB3/TrabalhoDeAPS.git
    cd 
    ```

2.  **Inicie o servidor:**
    Dentro da pasta do projeto, abra o terminal e digite:
    ```bash
    php -S localhost:8000
    ```

3.  **Acesse no navegador:**
    Abra `http://localhost:8000`

### Opção 2: Usando XAMPP ou WAMP

1.  Baixe o repositório como ZIP ou faça o clone.
2.  Mova a pasta do projeto para dentro do diretório do servidor:
    * **XAMPP:** `C:\xampp\htdocs\petiscaria`
    * **WAMP:** `C:\wamp64\www\petiscaria`
3.  Inicie o Apache pelo painel de controle do XAMPP/WAMP.
4.  Acesse no navegador: `http://localhost/petiscaria`

---

## ⚠️ Importante: Primeiro Acesso e Erros de Sessão

Como o sistema guarda objetos PHP na sessão, se você alterar o código das classes e tentar rodar com uma sessão antiga aberta, pode ocorrer o erro:
> *Fatal error: __PHP_Incomplete_Class...* ou *Undefined array key...*

**Para corrigir isso:**
1.  Crie um arquivo chamado `limpar.php` na raiz com o conteúdo:
    ```php
    <?php
    session_start();
    session_destroy();
    header("Location: index.php");
    ?>
    ```
2.  Sempre que o sistema der erro, acesse `http://localhost:8000/limpar.php` para resetar a memória do sistema.

---

## 📂 Estrutura de Arquivos

Certifique-se de que todos os arquivos abaixo estejam na mesma pasta:

* `index.php`: Tela principal (Caixa e Mesas).
* `admin.php`: Tela administrativa (CRUDs de Estoque, Clientes, Funcionários).
* `classes.php`: Definição das Classes (Mesa, Produto, Caixa, etc.) e Banco de Dados simulado.
* `controlador.php`: Recebe as requisições POST e gerencia a lógica.
* `style.css`: Estilização visual.
* `limpar.php`: Utilitário para resetar a sessão.

---

## 🛠️ Fluxo de Uso (Teste Padrão)

Para testar todas as funcionalidades, siga este roteiro:

1.  **Administração:** Clique em *[Acessar Administração]* no topo. Cadastre um produto novo e volte.
2.  **Abrir Caixa:** No painel do gerente, informe o Troco (ex: 100.00) e abra o caixa.
3.  **Abrir Mesa:** Escolha uma mesa (ex: Mesa 1) e clique em "Abrir Mesa".
4.  **Fazer Pedido:**
    * Selecione um produto, qtd e obs. Clique em "Adicionar".
    * Note que o item fica **Amarelo (Novo)**.
    * Clique em **"🔔 Enviar para Cozinha"**. O item fica normal.
5.  **Cancelar Item:** Tente cancelar um item já enviado. Será exigida a senha do gerente (**admin**).
6.  **Fechar Conta:** Clique em "Fechar Conta".
7.  **Pagamento:** Selecione a forma de pagamento (ex: PIX) e finalize.
8.  **Relatório:** Feche o caixa para ver o histórico do dia.

---

## 👨‍💻 Autores

Projeto desenvolvido para a disciplina de Análise e Projeto de Software.

* Guilherme Nishida
* Jane Kelli
* Josué Modesto
* Vinicius Borges


