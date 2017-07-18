
<p align="center"> <img src="https://i.imgur.com/ZksRYTl.png" alt="ChatBotPHP"/> </p>

----------

## Chatbot para messenger feito em PHP ( Chatbot for messenger made in PHP )
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/PaulaoDev/ChatBot-PHP-Facebook/master/LICENSE)
[![GitHub stars](https://img.shields.io/github/stars/PaulaoDev/ChatBot-PHP-Facebook.svg)](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/PaulaoDev/ChatBot-PHP-Facebook.svg)](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/fork)
[![GitHub issues](https://img.shields.io/github/issues/PaulaoDev/ChatBot-PHP-Facebook.svg)](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/issues)
[![GitHub watchers](https://img.shields.io/github/watchers/badges/shields.svg?style=social&label=Watch)](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/subscription)
[![Whatsapp](https://img.shields.io/badge/Whatsapp-On-green.svg)](https://bit.ly/whatsappdopaulo)
[![Donate Paypal](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://goo.gl/ujU2QU)
[![Donate Bitcoin](https://img.shields.io/badge/Donate-Bitcoin-yellow.svg)](https://blockchain.info/address/37RWdwgsXK94pANXm9fHv722k4zQmtmCpH)

----------


 **Essenciais Para A Configuração ( Essential For Setup ) :** 
  > - PHP >= 5.4
  > - Composer. https://getcomposer.org/download/
  > - Entender o Basico de PHP 
  > - Hospedagem Usada Para O Projeto https://goo.gl/rRHSWu

---------

   **Pra que Serve? ( What is it for? )** 
   - Atendente Para Paginas
   - Facil Configuração
   - Menor Tempo De Resposta
   - Integração Com Banco De Dados
   - Facil manutenção

----------

   ### Projeto no Composer ( Project in Composer )
   `composer chatbotphp paulao-dev/chat-bot-php-facebook`

----------

#### **Demo:** [`Demo Online`](https://www.facebook.com/PDeveloperchatbots/)
#### **Note:** Abra o chat da pagina e envie **`help`**
#### **Note:** Open the page chat and send **`help`**
    

----------


# Video Tutorial


[![Tutorial Configurando O Chatbot](https://i.imgur.com/APHcJIE.jpg)](https://www.youtube.com/watch?v=_HQfTZLV1lA)


----------

 1. Acesse o Painel De Desenvolvedores do [`Facebook Developers`](https://developers.facebook.com)

 
	 * Meus Aplicativos > Adicionar Aplicativo
	 * Nome De Exibiçao > De sua Preferencia
	 * Clique no Botão > Crie um número de identificação do aplicativo.
	
 2. Aplicativo Criado

	* Com o Aplicativo já criado  No menu vá em  **Adicionar Produto**
	

 3. Configurando o Messenger

	* Clique Encima do Botão Começar na Opção **"Messenger"**

 4. Configuraçoes

	* Com o Messenger Adicionado Vá em **Webhooks** E Clique em **Configurar Webhooks**

 5. Configurando WebHooks


	* Em **URL de retorno de chamada:** coloque o `https://seudominio.com/webhooks`.
	* **Senha:** Abra o arquivo **[`index.php`](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/blob/master/index.php)** e atribua uma senha para verificaçao no webhooks.
	      
    
    ---------
    
    
	```php
	Route::get('/webhook', function() {
      $token_access = "minhasenha123";
    });
   	```
   	      
    
    ---------
    
    
	* Em **Verificar token:** o valor definido em $token_access.
	* Em **Campos de Assinatura:** selecione **messages, messaging_postbacks, message_deliveries, message_reads**
	* Apos seguir os passos clique em **Verificar e Salvar**
	
	
 6. Configurando Servidor PHP

	Apos a verificaçao ser bem sucedida.
	
  - Abra o terminal na pasta baixada o repositorio é de um **`composer install`**
	* **Config Bot:** Abra o arquivo **[`/config/botConfig/config.php`](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/blob/master/config/botConfig/config.php)** e insira sua configuração
      
    
    ---------
    
    
  ```php
    define("BOT_KEY", "SUA KEY");
	define("BOT_TOKEN", "minhasenha123");
	define("BOT_DOMINIO", "https://meusite.com/");
	define("BOT_ENDPOINT", BOT_DOMINIO."endpoint");
   ```
   
    
   ----------
    
    
 * **Para gerar a KEY:** vá ate **Geração de token** acima de Webhooks em **Pagina** selecione a pagina desejada para o BOT. Ira Abrir uma janela pedindo a permissão da pagina para o acesso do Facebook Developers. Apos Aceitar em **Token de acesso da Página** ira aparecer um Token quer Sera o `BOT_TOKEN` **[`/config/botConfig/config.php`](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/blob/master/config/botConfig/config.php)**.


 7. Configurar Mensagens

	- Para Configurar bastar ir ate **[`/neural/neuro-system.json`](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/blob/master/neural/neuro-system.json)** 
  - As Callbacks são configurada em **[`/src/bot/callbacks.php`](https://github.com/PaulaoDev/ChatBot-PHP-Facebook/blob/master/src/bot/callbacks.php)**
  
	> **Note:**
	> - As mensagens são configuradas em JSON.

___________
