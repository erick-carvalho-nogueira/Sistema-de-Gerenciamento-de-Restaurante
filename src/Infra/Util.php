<?php
/**
 * Classe: Util
 * Responsável: Erik Carvalho Nogueira (RA 2026106090008)
 * Descrição: utilitários estáticos de formatação (moeda, data, HTML) e de conversão segura dos dados digitados.
 */
declare(strict_types=1);

final class Util
{
    /** Classe utilitária: só tem métodos estáticos, por isso não pode ser instanciada. */
    private function __construct()
    {
    }

    // ---------------------------------------------------------------- formatação

    /** 1234.5 → "R$ 1.234,50" */
    public static function moeda(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    /** 0.10 → "10%"; 0.125 → "12,5%" */
    public static function percentual(float $fracao): string
    {
        $texto = number_format($fracao * 100, 1, ',', '.');
        return rtrim(rtrim($texto, '0'), ',') . '%';
    }

    /** Valores em dinheiro são sempre arredondados para centavos. */
    public static function arredondar(float $valor): float
    {
        return round($valor, 2);
    }

    /** Escapa o texto para exibir em HTML (evita injeção de HTML/JavaScript). */
    public static function escapar($texto): string
    {
        if (is_array($texto) || is_object($texto)) {
            return '';
        }
        return htmlspecialchars((string) $texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function agora(): string
    {
        return date('Y-m-d H:i:s');
    }

    public static function hoje(): string
    {
        return date('Y-m-d');
    }

    /** "2026-11-19 14:30:00" → "19/11/2026 14:30" */
    public static function formatarDataHora(?string $dataIso): string
    {
        if ($dataIso === null || $dataIso === '') {
            return '—';
        }
        $momento = strtotime($dataIso);
        return $momento === false ? $dataIso : date('d/m/Y H:i', $momento);
    }

    // ---------------------------------------------------------------- texto

    /** Remove espaços repetidos/pontas; devolve '' para valores que não são texto. */
    public static function limparTexto($texto): string
    {
        if ($texto !== null && !is_scalar($texto)) {
            return '';
        }
        $texto = (string) $texto;
        $limpo = preg_replace('/\s+/u', ' ', $texto);
        if ($limpo === null) { // texto com bytes inválidos em UTF-8
            $limpo = preg_replace('/\s+/', ' ', $texto) ?? '';
        }
        return trim($limpo);
    }

    /** Quantidade de caracteres (e não de bytes), para validar nomes com acento. */
    public static function tamanho(string $texto): int
    {
        return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
    }

    // ---------------------------------------------------------------- conversões

    /**
     * Converte "59,90", "1.234,56", "59.9" ou um número em float.
     * Lança DadosInvalidosException se não for um número válido.
     */
    public static function paraFloat($valor, string $campo = 'valor'): float
    {
        if (is_int($valor) || is_float($valor)) {
            $numero = (float) $valor;
        } else {
            if ($valor !== null && !is_string($valor)) {
                throw DadosInvalidosException::campo($campo, 'deve ser um número válido');
            }
            $texto = trim(str_replace(['R$', ' '], '', (string) $valor));
            if (strpos($texto, ',') !== false) {
                // formato brasileiro: ponto separa milhar e vírgula separa decimais
                $texto = str_replace(',', '.', str_replace('.', '', $texto));
            }
            if ($texto === '' || !is_numeric($texto)) {
                throw DadosInvalidosException::campo($campo, 'deve ser um número válido');
            }
            $numero = (float) $texto;
        }

        if (!is_finite($numero)) {
            throw DadosInvalidosException::campo($campo, 'deve ser um número válido');
        }
        return $numero;
    }

    /** Converte "12" ou 12 em int. Lança DadosInvalidosException para qualquer outra coisa. */
    public static function paraInt($valor, string $campo = 'valor'): int
    {
        if (is_int($valor)) {
            return $valor;
        }
        if (is_float($valor) && is_finite($valor) && floor($valor) === $valor) {
            return (int) $valor;
        }
        if ($valor === null || !is_string($valor) || preg_match('/^\s*-?\d{1,15}\s*$/', $valor) !== 1) {
            throw DadosInvalidosException::campo($campo, 'deve ser um número inteiro');
        }
        return (int) trim($valor);
    }

    /** "1", "on", "sim", "true" e true → true; o resto → false (usado em checkboxes). */
    public static function paraBool($valor): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }
        if (!is_scalar($valor)) {
            return false;
        }
        return in_array(strtolower(trim((string) $valor)), ['1', 'true', 'on', 'sim', 's', 'yes', 'y'], true);
    }
}
