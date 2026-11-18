<?php

declare(strict_types=1);

use BibliotecaClassificadora\Classificador;

require_once __DIR__ . '/../vendor/autoload.php';

function verificarIgual($esperado, $recebido, string $caso): void
{
  if ($esperado !== $recebido) {
    throw new RuntimeException(
      sprintf(
        "%s\nEsperado: %s\nRecebido: %s",
        $caso,
        var_export($esperado, true),
        var_export($recebido, true)
      )
    );
  }

  echo "[OK] {$caso}" . PHP_EOL;
}

$historico = [
  [
    'descricao' => 'Almoço com cliente',
    'categoria' => 'Alimentação',
  ],
  [
    'descricao' => 'Almoço durante a viagem',
    'categoria' => 'Alimentação',
  ],
  [
    'descricao' => 'Uber até o aeroporto',
    'categoria' => 'Transporte',
  ],
  [
    'descricao' => 'Diária de hotel',
    'categoria' => 'Hospedagem',
  ],
];

$classificador = new Classificador($historico);

$resultadoAlmoco = $classificador->classificar('almo');

verificarIgual(
  'Alimentação',
  $resultadoAlmoco['categoria'],
  'Classifica um prefixo conhecido'
);

verificarIgual(
  2,
  $resultadoAlmoco['registros_semelhantes'],
  'Conta os registros semelhantes'
);

$resultadoDesconhecido = $classificador->classificar('cinema');

verificarIgual(
  null,
  $resultadoDesconhecido['categoria'],
  'Retorna null quando não há correspondência'
);

$erroCapturado = false;

try {
  new Classificador([
    [
      'descricao' => 'Almoço com cliente',
      'categoria' => '',
    ],
  ]);
} catch (InvalidArgumentException $erro) {
  $erroCapturado = true;
}

verificarIgual(
  true,
  $erroCapturado,
  'Rejeita um registro com categoria vazia'
);

echo 'Todos os testes passaram.' . PHP_EOL;
