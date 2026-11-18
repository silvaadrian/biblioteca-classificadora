<?php

declare(strict_types=1);

use BibliotecaClassificadora\Classificador;

require_once __DIR__ . '/../vendor/autoload.php';

$historico = [
  [
    'descricao' => 'Almoço durante visita ao cliente',
    'categoria' => 'Alimentação',
  ],
  [
    'descricao' => 'Café da manhã em hotel',
    'categoria' => 'Alimentação',
  ],
  [
    'descricao' => 'Uber para o aeroporto',
    'categoria' => 'Transporte',
  ],
  [
    'descricao' => 'Combustível do veículo',
    'categoria' => 'Transporte',
  ],
  [
    'descricao' => 'Diária de hospedagem',
    'categoria' => 'Hospedagem',
  ],
];

$classificador = new Classificador($historico);

$descricao = 'caf';
$resultado = $classificador->classificar($descricao);

print_r($resultado);
