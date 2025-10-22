# Sistema de Votação - Teste Avaliativo Signo Technology

Este projeto é um sistema de votação e gerenciamento de enquetes desenvolvido como parte de um teste avaliativo. Ele permite a criação, edição e exclusão de enquetes por administradores, com um sistema de aprovação para novos administradores, e permite que usuários registrados votem nas enquetes ativas. A atualização dos resultados da votação na tela da enquete ocorre automaticamente via polling.

## Tecnologias Utilizadas

* **Backend:** Laravel 11 (PHP 8.3)
* **Frontend:** Blade Templates, Tailwind CSS, Vanilla JavaScript (para Polling)
* **Banco de Dados:** MySQL
* **Autenticação/Autorização:** Laravel Breeze, com separação de papéis (Guest, Admin Pendente, Admin, Super Admin)

## Como Rodar o Projeto Localmente

Siga os passos abaixo para configurar e executar o projeto em seu ambiente de desenvolvimento:

1.  **Clone o Repositório:**
    ```bash
    git clone [URL_DO_SEU_REPOSITORIO_GITHUB]
    cd sistema-votacao
    ```
    *(Substitua pela URL real do seu repositório)*

2.  **Instale as Dependências:**
    ```bash
    composer install
    npm install
    ```

3.  **Configure o Ambiente:**
    * Copie o arquivo de exemplo `.env.example` para `.env`:
        ```bash
        cp .env.example .env
        ```
    * Crie um banco de dados MySQL vazio para o projeto (ex: `votacao_db`).
    * Abra o arquivo `.env` e configure as credenciais do seu banco de dados:
        ```dotenv
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=votacao_db # Ou o nome que você criou
        DB_USERNAME=root      # Seu usuário MySQL
        DB_PASSWORD=          # Sua senha MySQL (pode ser vazia se usar Laragon padrão)
        ```

4.  **Gere a Chave da Aplicação e Rode as Migrações:**
    ```bash
    php artisan key:generate
    php artisan migrate:fresh
    ```
    *(O comando `migrate:fresh` limpará o banco e criará todas as tabelas necessárias).*

5.  **Compile os Assets de Frontend:**
    ```bash
    npm run build
    ```

6.  **Inicie os Servidores:**
    Você precisará de **dois** terminais abertos na pasta do projeto:
    * **Terminal 1 (Servidor Web):**
        ```bash
        php artisan serve
        ```
    * **Terminal 2 (Compilador Vite):**
        ```bash
        npm run dev
        ```

7.  **Acesse o Sistema:** Abra seu navegador e acesse `http://127.0.0.1:8000` (ou a URL fornecida pelo `php artisan serve`).

## Como Usar (Fluxo de Teste Sugerido)

1.  **Crie o Super Admin ("Chefe"):**
    * Acesse `http://127.0.0.1:8000`.
    * Clique em "Solicitar Acesso de Admin" e preencha o formulário para criar sua conta principal.
    * **Importante:** Acesse seu gerenciador de banco de dados (HeidiSQL, phpMyAdmin, etc.), abra a tabela `users` do banco `votacao_db`, encontre o usuário que você acabou de criar e altere:
        * `admin_status` de `pending` para `admin`.
        * `is_super_admin` de `0` para `1`.

2.  **Acesse como Super Admin:**
    * Faça login com a conta que você acabou de promover (`http://127.0.0.1:8000/login`).
    * Você será direcionado para o Dashboard de Administração (`/admin/dashboard`).

3.  **Crie e Gerencie Enquetes:**
    * Use o botão "Nova Enquete" para criar algumas enquetes. Certifique-se de criar pelo menos uma com datas que a tornem "Em Andamento".
    * Teste as funcionalidades de Editar (ícone de lápis) e Excluir (ícone de lixeira) na lista.
    * Teste os filtros de status (Todas, Em Andamento, etc.).

4.  **Simule um Visitante e um Admin Pendente:**
    * Abra uma **janela anônima** do navegador.
    * Acesse `http://127.0.0.1:8000`.
    * Crie uma conta usando "Registrar como Visitante". Faça login e veja o `/guest/dashboard`. Teste o acesso à página de votação clicando no ícone de olho.
    * Faça Log Out.
    * Crie outra conta usando "Solicitar Acesso de Admin". Faça login. Você verá o dashboard de visitante com o aviso de "Solicitação Pendente".

5.  **Aprove o Admin Pendente:**
    * Volte para a janela onde você está logado como Super Admin.
    * No menu superior direito (clique no seu nome), vá para "Aprovar Admins".
    * Use os ícones para aprovar (círculo com check) o usuário que estava pendente.
    * Na janela anônima, faça Log Out e Log In novamente com o usuário que era pendente. Ele agora deve ser direcionado para o `/admin/dashboard`.

6.  **Teste a Atualização Automática (Polling):**
    * Abra duas janelas do navegador (podem ser duas normais, logadas com usuários diferentes, ou uma normal e uma anônima logada).
    * Navegue para a **mesma** página de votação de uma enquete "Em Andamento" em ambas as janelas (clicando no ícone de olho no dashboard).
    * Vote em uma das janelas.
    * Observe a outra janela: os resultados (contagem de votos e barra de progresso) devem se atualizar automaticamente após alguns segundos (o intervalo está definido como 2 segundos).
