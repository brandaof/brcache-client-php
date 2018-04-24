# BRCache client PHP

O BRCache é uma ótima opção como cache para aplicações PHP. É rápido, permite o uso de conexões persistentes, suporta armazenamento em memória e disco, tem suporte transacional. Não é necessário o carregamento de nenhum arquivo de configuração. Ele pode ser usado para fazer o cache de páginas, compartilhamento de variáveis globais e manipulação de sessões.

## Instalando o cliente BRCache

Para usar o BRCache em uma aplicação PHP é necessário fazer o download de um cliente. Uma opção é o cliente fornecido pela equipe do BRCache disponível no Sourceforge. Depois de feito o download, deve-se descompacta-lo.

ex:
```
unzip <origem>/brcache-client-yy-xx.zip -d <destino>;
```

A variável <origem> é o local onde o arquivo está e <destino> é o local onde será descompactado. A pasta de destino tem que ser informada em include_path (php.ini).

As operações no cache são feitas com o uso da classe BRCacheConnection. Ela fica localizada no script brandao\brcache\BRCacheConnection.php.

```php
class BRCacheConnection{

    public function close(){...}
 
    public function isClosed(){...}
 
    public function replace($key, $value, $timeToLive = 0, $timeToIdle = 0){...}
 
    public function replaceValue($key, $oldValue, 
             $newValue, $cmp, $timeToLive = 0, $timeToIdle = 0){...}
 
    public function putIfAbsent($key, $value, $timeToLive = 0, $timeToIdle = 0){...}

    public function put($key, $value, $timeToLive = 0, $timeToIdle = 0){...}
 
    public function set($key, $value, $timeToLive = 0, $timeToIdle = 0){...}
 
    public function get($key, $forUpdate = false){...}
 
    public function remove($key){...}

    public function removeValue($key, $value, $cmp){...}
 
    public function setAutoCommit($value){...}
 
    public function isAutoCommit(){...}
 
    public function commit(){...}
 
    public function rollback(){...}
 
    public function getHost(){...}
 
    public function getPort(){...}
 
}
```

- close(): Fecha a conexão com o servidor.
- isClosed(): Verifica se a conexão está fechada.
- replace(): Substitui o valor associado à chave somente se ele existir.
- replaceValue(): Substitui o valor associado à chave somente se ele for igual a um determinado valor.
- putIfAbsent(): Associa o valor à chave somente se a chave não estiver associada a um valor.
- put(): Associa o valor à chave.
- set(): Associa o valor à chave somente se a chave não estiver associada a um valor.
- get(): Obtém o valor associado à chave bloqueando ou não seu acesso as demais transações.
- remove(): Remove o valor associado à chave.
- removeValue(): Remove o valor associado à chave somente se ele for igual a um determinado valor.
- setAutoCommit(): Define o modo de confirmação automática.
- isAutoCommit(): Obtém o estado atual do modo de confirmação automática.
- commit(): Confirma todas as operações da transação atual e libera todos os bloqueios detidos por essa conexão.
- rollback(): Desfaz todas as operações da transação atual e libera todos os bloqueios detidos por essa conexão.
- getHost(): Obtém o endereço do servidor.
- getPort(): Obtém a porta do servidor.

## Adicionando itens

São oferecidos vários métodos para inserir um item no cache. Cada um com sua particularidade.

### Método put

O método put associa um valor a uma chave, mesmo que ela exista. Ele retorna true, se já existir um valor associado à chave, ou false, se não existir um valor associado à chave.

```php
$result = $con->put('key', $value);

if($result){
    echo 'replaced';
}
else{
    echo 'stored';
}
```

### Método replace

O método replace substitui o valor associado à chave somente se ele existir. Ele retorna true, se o valor for substituído, ou false, se o valor não for armazenado.

```php
$result = $con->replace('key', $value);

if($result){
    echo 'replaced';
}
else{
    echo 'not stored';
}
```

### Método replaceValue

O método replaceValue substitui o valor associado à chave somente se ele existir e for igual a um determinado valor. Ele retorna true, se o valor for substituído, ou false, se o valor não for armazenado.

```php
$result = $con->replaceValue('key', 
$value,
function($a, $b){
    return strcmp($a,$b) == 0;
});

if($result){
    echo 'replaced';
}
else{
    echo 'not stored';
}
```

### Método putIfAbsent

O método putIfAbsent associa o valor à chave somente se a chave não estiver associada a um valor. Esse método tem uma particularidade. Quando existe um valor associado a chave, o mesmo é retornado, mas será lançada uma exceção se ele expirar no momento da sua recuperação.

```php
try{
    $currentValue = $con->putIfAbsent('key', $value);
    if($currentValue == null){
        echo 'stored';
    }
    else{
        echo 'not stored';
    }
}
catch(CacheException $e){
    if(e->getCode() == 1030){
        //o valor atual expirou
    }
    throw e;
}
```

### Método set

O método set associa o valor à chave somente se a chave não estiver associada a um valor. Ele retorna true, se o valor for associado à chave, ou false, se ele for descartado.

```php
$result = $con->set('key', $value);
if(result){
    echo 'stored';
}
else{
    echo 'not stored';
}
```

### Obtendo itens.

Um valor é obtido com o uso do método get. Ele retorna o valor associado à chave ou null.

```php
$value = $con->get('key');

if($value != null){
    echo 'value exists';
}
else{
    echo 'value not found';
}
```

## Removendo itens.

São oferecidos vários métodos para remover um item do cache.

### Método remove.

O método remove apaga o valor associado à chave. Ele retorna true, se o valor for removido, ou false, se ele não existir.

```php
$result = $con->remove('key');

if($result){
    echo 'removed';
}
else{
    echo 'not found';
}
```

### Método removeValue.

O método removeValue remove o valor associado à chave somente se ele existir e for igual a um determinado valor. Ele retorna true, se o valor for removido, ou false, se ele não existir ou for diferente do valor informado.

```php
$result = $con->remove(
'key',
function($a, $b){
    return strcmp($a,$b) == 0;
});

if($result){
    echo 'removed';
}
else{
    echo 'not found';
}
```

## Exemplo

No exemplo a seguir o BRCache é usado para fazer o cache de páginas. Esse exemplo apenas ilustra o uso da classe BRCacheConnection.

```php
<?php
require_once 'brandao/brcache/BRCacheConnection.php';

function getURI(){
  $serverRoot   = str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']);
  $docRoot      = str_replace('\\','/', __DIR__);
  $relativePath = str_replace($serverRoot,'', $docRoot);
  $query        = $_SERVER['QUERY_STRING'];
  $uri          = str_replace($relativePath . '/index.php', '', $_SERVER['REQUEST_URI']);
  $uri          = str_replace('?' . $_SERVER['QUERY_STRING'], '', $uri);
  return $uri;
}

$uri     = getURI();
$con     = new BRCacheConnection();
$content = $con->get($uri);

if($content != null){
  echo $content;
  exit;
}

ob_start();

include $uri;

$content = ob_get_contents();

$con->put($uri, $content, 300000);

ob_end_flush();
?>
```

No trecho abaixo o URI no formato  /&lt;path&gt;/index.php&lt;uri&gt; é convertido no formato &lt;uri&gt;.

```php
function getURI(){
 $serverRoot   = str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']);
 $docRoot      = str_replace('\\','/', __DIR__);
 $relativePath = str_replace($serverRoot,'', $docRoot);
 $query        = $_SERVER['QUERY_STRING'];
 $uri          = str_replace($relativePath . '/index.php', '', $_SERVER['REQUEST_URI']);
 $uri          = str_replace('?' . $_SERVER['QUERY_STRING'], '', $uri);
 return $uri;
}
```

No trecho abaixo é feita a conexão com o servidor BRCache. Opcionalmente pode-se informar o host, porta e se a conexão será persistente.

```php
$con = new BRCacheConnection();
```
No trecho abaixo ocorre a tentativa de obter o conteúdo da página e colocar em $content.

```php
$content = $con->get($uri);
```

No trecho abaixo, se $content for diferente de null o conteúdo da página é enviado ao cliente.

```php
if($content != null){
  echo $content;
  exit;
}
```

No trecho abaixo é ativado o buffer de saída, incluída a página no buffer  e coloca em $content.

```php
ob_start();
include $uri;
$content = ob_get_contents();
```

No trecho abaixo, o conteúdo da página é enviado ao cache. Ele ficará disponível por 5 minutos.

```php
$con->put($uri, $content, 300000);
```

No trecho abaixo o buffer de saída é descarregado e desativo.

```php
ob_end_flush();
```

