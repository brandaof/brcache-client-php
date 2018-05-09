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
