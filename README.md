
<p align="center"> <img src="http://i.imgur.com/6KXNtkF.png" alt="ChatBotPHP"/> </p>

----------

## ChatBot Para Messenger Feito Em PHP

----------


 **Required:** 
  > - PHP >= 5.4
  > - Composer. https://getcomposer.org/download/
  > - Entender o Basico de PHP 
  > - Hospedagem Usada Para O Projeto https://goo.gl/rRHSWu

---------

   - Atendente Para Paginas
   - Facil Configuração
   - Menor Tempo De Resposta
   - Integração Com Banco De Dados
   - Facil manutenção

----------

#### **Demo:** https://fb.com/capaspersonalizadaass
#### **Note:** Abra o chat da pagina e envie **help**
    

----------

 1. Acesse o Painel De Desenvolvedores do Facebook **https://developers.facebook.com**

 
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


	* Em **URL de retorno de chamada:** coloque o https://seudominio.com/webhooks.
	* **Senha:** Abra o arquivo **index.php** e atribua uma senha para verificaçao no webhooks.
	      
    
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
	
  - Abra o terminal na pasta baixada o repositorio é de um **composer install**
	* **Config Index:** Abra o arquivo **index.php** e insira sua configuração
      
    
    ---------
    
    
  ```php
  Route::post("/webhook", function(){
		    // Cria o Robo
		    $BotCore = BotCore::getInstance();
		    // Seta as Configs
		    $BotCore->setKey("KEY GERADA DA SUA PAGINA");
		    $BotCore->setToken("minhasenha123");
		    $BotCore->setDominio("https://meusite.com");
		    $BotCore->endpoint("https://meusite.com/endpoint");
    });
   ```
   
    
   ----------
    
    
 * **Para gerar a KEY:** vá ate **Geração de token** acima de Webhooks em **Pagina** selecione a pagina desejada para o BOT. Ira Abrir uma janela pedindo a permissão da pagina para o acesso do Facebook Developers. Apos Aceitar em **Token de acesso da Página** ira aparecer um Token copie ele e coloque na variavel $key em **/views/webhooks.php**.


 7. Configurar Mensagens

	- Para Configurar bastar ir ate **neural/neuro-system.json** 
  - As Callbacks são configurada em **src/bot/callbacks.php**
  
	> **Note:**
	> - As mensagens são configuradas em JSON.

___________

 # Ajude o Projeto
 
___________

  - Carteira Bitcoin: 37RWdwgsXK94pANXm9fHv722k4zQmtmCpH 

--------------------------

<p align="center"><img src="https://chart.googleapis.com/chart?chs=240x240&choe=UTF-8&chld=M%7C0&cht=qr&chl=37RWdwgsXK94pANXm9fHv722k4zQmtmCpH" width="200"/></p> 

--------------------------

 - Paypal:  https://goo.gl/ujU2QU

--------------------------

 - WhatsApp: http://bit.ly/whatsappdopaulo
 
--------------------------
