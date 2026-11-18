# Biblioteca Classificadora

MVP de uma biblioteca PHP que sugere categorias de despesas com base em um histórico de descrições já classificadas.

## Funcionamento atual

A classe **Classificador** recebe o histórico por meio do construtor. Para uma nova descrição, ela:

- converte acentos e letras maiúsculas para uma forma normalizada;
- ignora palavras comuns e termos com menos de três caracteres;
- compara os termos digitados com o início dos termos encontrados no histórico;
- soma a pontuação por categoria e retorna evidências da sugestão.

Esta versão usa correspondência textual e pontuação. Ela não conecta a um banco de dados nem usa um modelo externo de IA. O programa que utiliza a biblioteca é responsável por fornecer o histórico.

## Resultado da classificação

O método **classificar()** retorna:

- **categoria**: categoria sugerida ou null quando não há correspondência;
- **apoio_percentual**: proporção da pontuação atribuída à categoria sugerida;
- **registros_semelhantes**: quantidade de registros históricos que tiveram termos correspondentes;
- **evidencias**: até três descrições históricas e os termos encontrados.

**apoio_percentual** não é uma probabilidade calibrada. Por exemplo, um único registro correspondente pode resultar em 100%.

## Requisitos

- PHP 8.3 ou superior, dentro da linha 8.x;
- extensão iconv habilitada.

## Instalação

Clone o repositório e gere o autoload do Composer:

~~~bash
git clone https://github.com/silvaadrian/biblioteca-classificadora.git
cd biblioteca-classificadora
composer install
~~~

## Uso

~~~php
<?php

declare(strict_types=1);

use BibliotecaClassificadora\Classificador;

require_once __DIR__ . '/vendor/autoload.php';

$historico = [
    [
        'descricao' => 'Almoço durante visita ao cliente',
        'categoria' => 'Alimentação',
    ],
    [
        'descricao' => 'Almoço em viagem',
        'categoria' => 'Alimentação',
    ],
    [
        'descricao' => 'Uber para o aeroporto',
        'categoria' => 'Transporte',
    ],
];

$classificador = new Classificador($historico);
$resultado = $classificador->classificar('almo');

print_r($resultado);
~~~

## Executar o exemplo e as verificações

Na raiz do projeto:

~~~bash
php exemplos/exemplo-basico.php
php tests/ClassificadorTest.php
~~~

## Estrutura

~~~text
composer.json
src/Classificador.php
exemplos/exemplo-basico.php
tests/ClassificadorTest.php
README.md
~~~
