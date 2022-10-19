
## Sistema simples utilizado Mezzio do laminas
**Para Iniciar** 

``` 
git clone https://github.com/lRafaelOliveira/TesteClube-envios.git
```

``` 
composer install
```

**Configurar a conexão com o Banco de Dados local no arquivo: src/App/Handler/ApiHandler.php linha 35**
**ATENÇÃO: è preciso que ja tenha a database criada juntamente com as tabelas e dados**

**Agora para startar o sistema:**


``` 
composer run --timeout=0 server
```

## Para Testar o sistema utilize o insominia/postman

**realizar chamada via POST no endpoint api/consulta passando como parametros o json abaixo:**

``` 
{
 "cep_origem": "74350300",
 "cep_destino": "72910001",
 "peso": 250,
 "altura": 15,
 "largura": 10,
 "comprimento": 10,
 "valor_declarado": 300
}

```
