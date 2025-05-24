# Desafio técnico Backend &middot; Montink
> Mini ERP para controle de Pedidos, Produtos, Cupons e Estoque

Este projeto faz parte de um teste técnico para uma vaga de backend na Montink. É um sistema de e-commerce que permite gerenciar produtos, pedidos, cupons de desconto e controle de estoque.

## Funcionalidades

- **Gestão de Produtos**
  - Cadastro de produtos com nome, preço e estoque
  - Suporte a variações de produtos
  - Controle de estoque automático

- **Carrinho de Compras**
  - Adição/remoção de produtos
  - Atualização de quantidades
  - Cálculo automático de subtotal
  - Aplicação de cupons de desconto

- **Cupons de Desconto**
  - Cupons percentuais e de valor fixo
  - Validação de data de expiração
  - Valor mínimo para aplicação
  - Status ativo/inativo

- **Pedidos**
  - Registro de pedidos com dados do cliente
  - Cálculo de frete baseado no valor total
  - Integração com cupons de desconto
  - Status de pedido e pagamento

## Getting Started

### Dependências
Para executar este projeto no modo de desenvolvimento, você precisará do [Docker](https://www.docker.com/) instalado (há um arquivo docker-compose na raiz do projeto).

### Instalando
**Clonando o repositório**
```shell
$ git clone https://github.com/ph-gaia/challenge-montink.git
$ cd challenge-montink
```

**Executando a aplicação**:
```shell
$ docker-compose up -d --build
```
Execute o comando, o docker instalará todas as dependências.

**Criação do Banco de Dados**: Para que a aplicação seja usada, é necessário importar o Banco de Dados; isso pode ser feito importando arquivo SQL (*dump.sql*) encontrado no diretório *www/public/* da aplicação.

**Instalação de bibliotecas**: Para o perfeito funcionamento da aplicação é necessário que seja realizado instalação de todas as bibliotecas. Para isso você vai precisar executar o *composer install*, com o seguinte comando:

```shell
$ docker exec -it montink-app composer install
```

### Assistente
Foi criado um assistente para aplicação *setup.php*.

Para criar todas as tabelas do banco de dados, você pode executar o seguinte comando:
```shell
$ php setup.php
```

### Executando os Testes
O projeto utiliza PHPUnit para testes automatizados. Para executar os testes:

```shell
$ docker exec -it montink-app ./vendor/bin/phpunit
```

Os testes estão localizados no diretório `www/App/Test/` e incluem:
- Testes de carrinho de compras

### Estrutura do Projeto

```
www/
├── App/
│   ├── Controllers/    # Controladores da aplicação
│   ├── Models/         # Modelos de dados
│   ├── Views/          # Templates de visualização
│   ├── Test/           # Testes automatizados
│   └── Route/          # Configuração de rotas
├── core/               # Núcleo do framework
├── public/             # Arquivos públicos
│   └── dump.sql        # Schema do banco de dados
└── vendor/             # Dependências do Composer
```

### Funcionamento

O sistema segue uma arquitetura MVC (Model-View-Controller):

1. **Roteamento**: As requisições são recebidas pelo arquivo `public/index.php` que inicia a aplicação através da classe `\Core\Bootstrap\InitApp()`. O roteador valida a requisição e a rota acessada.

2. **Controle**: Se a rota for válida, os parâmetros são extraídos da URI. Caso a rota não esteja registrada em `App/Route/Route.php`, o sistema retorna 404 Not Found.

3. **Execução**: Após a validação da rota, o Controller e o Método correspondentes são executados. O método `run()` instancia o Controller apropriado e executa a ação solicitada.

### Rotas Disponíveis

As rotas são registradas em `App/Route/Route.php` e incluem:

- **Produtos**
  - GET `/products` - Lista todos os produtos
  - GET `/products/create` - Formulário de criação
  - GET `/products/edit/{id}` - Formulário de edição
  - POST `/products/save` - Salva produto

- **Carrinho**
  - GET `/cart` - Visualiza carrinho
  - POST `/cart/add/{id}` - Adiciona produto
  - POST `/cart/update/{id}` - Atualiza quantidade
  - POST `/cart/remove/{id}` - Remove produto
  - POST `/cart/apply-coupon` - Aplica cupom

- **Pedidos**
  - POST `/orders/create` - Cria novo pedido

## Author
- [Paulo Henrique Coelho Gaia](https://www.linkedin.com/in/ph-gaia)
