<?php

declare(strict_types=1);

namespace BibliotecaClassificadora;

use InvalidArgumentException;

final class Classificador
{
  private array $historico;

  private const TERMOS_IGNORADOS = [
    'a',
    'as',
    'ao',
    'com',
    'da',
    'das',
    'de',
    'do',
    'dos',
    'e',
    'em',
    'na',
    'nas',
    'no',
    'nos',
    'o',
    'os',
    'para',
    'por',
    'sem',
    'um',
    'uma',
    'durante',
    'ate',
  ];

  /**
   * Cada registro precisa ter uma descrição e uma categoria.
   */
  public function __construct(array $historico)
  {
    foreach ($historico as $indice => $registro) {
      if (
        !is_array($registro)
        || !isset($registro['descricao'], $registro['categoria'])
        || !is_string($registro['descricao'])
        || !is_string($registro['categoria'])
        || trim($registro['categoria']) === ''
      ) {
        throw new InvalidArgumentException(
          "Registro inválido no histórico, índice {$indice}."
        );
      }
    }

    $this->historico = $historico;
  }

  public function classificar(string $descricao): array
  {
    $termosConsulta = $this->obterTermos($descricao);

    if ($termosConsulta === [])
      return $this->resultadoSemClassificacao();

    $pontuacoes = [];

    foreach ($this->historico as $registro) {
      $termosHistoricos = $this->obterTermos($registro['descricao']);
      $termosEncontrados = [];

      foreach ($termosConsulta as $termoConsulta) {
        foreach ($termosHistoricos as $termoHistorico) {
          if (
            strncmp(
              $termoHistorico,
              $termoConsulta,
              strlen($termoConsulta)
            ) === 0
          ) {
            $termosEncontrados[] = $termoConsulta;
            break;
          }
        }
      }

      if ($termosEncontrados === []) {
        continue;
      }

      $categoria = trim($registro['categoria']);
      $cobertura = count($termosEncontrados) / count($termosConsulta);

      if (!isset($pontuacoes[$categoria])) {
        $pontuacoes[$categoria] = [
          'pontuacao' => 0.0,
          'registros_semelhantes' => 0,
          'evidencias' => []
        ];
      }

      $pontuacoes[$categoria]['pontuacao'] += $cobertura;
      $pontuacoes[$categoria]['registros_semelhantes']++;
      $pontuacoes[$categoria]['evidencias'][] = [
        'descricao' => $registro['descricao'],
        'termos_encontrados' => array_values(array_unique($termosEncontrados))
      ];
    }

    if ($pontuacoes === []) {
      return $this->resultadoSemClassificacao();
    }

    uasort($pontuacoes, function (array $a, array $b): int {
      return $b['pontuacao'] <=> $a['pontuacao'];
    });

    $pontuacaoTotal = array_sum(array_column($pontuacoes, 'pontuacao'));

    foreach ($pontuacoes as $categoria => $dados) {
      $melhorResultado = $dados;
      break;
    }

    return [
      'categoria' => $categoria,
      'apoio_percentual' => (int) round($melhorResultado['pontuacao'] / $pontuacaoTotal * 100),
      'registros_semelhantes' => $melhorResultado['registros_semelhantes'],
      'evidencias' => array_slice($melhorResultado['evidencias'], 0, 3)
    ];
  }

  private function obterTermos(string $texto): array
  {
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);

    if ($texto === false) {
      throw new InvalidArgumentException("Não foi possível converter o texto para comparação.");
    }

    $texto = strtolower($texto);
    $texto = preg_replace('/[^a-z0-9+]/', ' ', $texto);
    $texto = trim($texto ?? '');

    $termos = preg_split('/\s+/', $texto);
    $termosIgnorados = self::TERMOS_IGNORADOS;

    $termos = array_filter($termos, function (string $termo) use ($termosIgnorados): bool {
      return strlen($termo) >= 3 && !in_array($termo, $termosIgnorados, true);
    });

    return $texto === '' ? [] : array_values(array_unique($termos));
  }

  private function resultadoSemClassificacao(): array
  {
    return [
      'categoria' => null,
      'apoio_percentual' => 0,
      'registros_semelhantes' => 0,
      'evidencias' => [],
    ];
  }
}
