# Perfect Pay - Desafio Sr.

## Descrição do projeto
O projeto consiste em uma API integrada ao gateway de pagamento Asaas 
para realização de pagamentos e consultas de dados de transações.

## Requisitos
De acordo com a documentação do Laravel, estes são os requisitos mínimos para
executar o projeto:

- PHP >= 8.2
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- Filter PHP Extension
- Hash PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Session PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

Estou utilizando o banco de dados MySQL para fazer o armazenamento de dados, então
será necessário instalar o driver do MySQL para o PHP.
- MySQL PHP Extension (pdo_mysqli)

## Instalação
1. Clone o repositório:
```bash
git clone https://github.com/Nathan-Guerra/desafio-perfectpay.git
```
_PS: utilize a flag `--depth 1` para clonar apenas 
a última versão do repositório caso não precise do histórico de commits._

2. Entre na pasta do projeto:
```bash
cd desafio-perfectpay
```

3. Instale as dependências do projeto:
```bash
composer install && npm install
```

4. Crie o arquivo `.env` a partir do arquivo `.env.example`:
```bash
cp .env.example .env
```

5. Gere a chave de criptografia do Laravel:
```bash
php artisan key:generate
```

6. Preencha os dados do banco de dados no arquivo `.env` conforme os
dados do seu ambiente.
7. Crie o banco de dados.
8. Execute as migrations e seeders:
```bash
php artisan migrate:fresh --seed
```
9. Insira os dados da API do Asaas no arquivo `.env`. 
Nomeadamente `ASAAS_API_KEY` e `ASAAS_API_URL`

## Executando o projeto
### Modo de desenvolvimento
Para executar o projeto em modo de desenvolvimento, utilize
o comando abaixo:
```bash
composer run dev
```
Isso irá executar o servidor embutido do PHP e o watcher do Laravel Mix
para compilar os arquivos CSS e JS do projeto.

### Modo de produção
Para executar o projeto como se fosse fazer um deploy, siga as seguintes 
instruções.
```bash
php artisan optimize:clear
npm run build
```
Isso irá compilar os arquivos CSS e JS do projeto e otimizar o cache do Laravel.

Após isso, você pode executar o servidor embutido do PHP.
```bash
php artisan serve
```
## Rotas
-> GET / - Página inicial do laravel

-> GET /checkout - Página de checkout do "produto".

-> GET /payments/{paymentUuid} - Página que exibe as informações do pagamento 
efetuado na página de checkout.

-> POST /api/payments - Insere um novo pagamento.

## Utilização
1. Va até a rota de checkout (/checkout) e preencha os dados da parte de cima.
2. Selecione um tipo de pagamento.
3. Caso o tipo de pagamento seja "Cartão de Crédito", preencha as informações da
parte de baixo da tela.
4. Aperte o botão azul no final da tela para realizar o pagamento.

## Testes
Para testar o projeto, basta utilizar o comando padrão do laravel.
```bash
php artisan test
```
_PS: Utilize a flag `-p` para executar os testes em paralelo._

Para verificar a taxa de cobertura dos testes, será necessário a biblioteca do
XDebug. Após a instalação basta executar o mesmo comando acima com a flag `--coverage`:
```bash
php artisan test --coverage
```

