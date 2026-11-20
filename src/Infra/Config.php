<?php
/**
 * Classe: Config
 * Responsável: Erik Carvalho Nogueira (RA 2026106090008)
 * Descrição: concentra as configurações fixas do sistema (nome, fuso horário, caminho do arquivo de dados, modo debug).
 */
declare(strict_types=1);

final class Config
{
    public const NOME_RESTAURANTE = 'Canto do Zé';
    public const FUSO_HORARIO = 'America/Porto_Velho';
    public const ARQUIVO_DADOS = 'restaurante.json';
    public const VARIAVEL_AMBIENTE_DADOS = 'RESTAURANTE_DADOS';
    public const VERSAO_DADOS = 1;
    public const CHAVE_PIX = '00000000-0000-4000-8000-000000000000';
    public const MODO_DEBUG = true;

    /** Classe utilitária: só tem membros estáticos, por isso não pode ser instanciada. */
    private function __construct()
    {
    }

    /** Pasta raiz do projeto (dois níveis acima de src/Infra). */
    public static function raiz(): string
    {
        return dirname(__DIR__, 2);
    }

    /**
     * Caminho do arquivo JSON com os dados do restaurante.
     * Pode ser trocado pela variável de ambiente RESTAURANTE_DADOS (usada pelos testes automatizados).
     */
    public static function caminhoDados(): string
    {
        $caminhoAlternativo = getenv(self::VARIAVEL_AMBIENTE_DADOS);
        if (is_string($caminhoAlternativo) && $caminhoAlternativo !== '') {
            return $caminhoAlternativo;
        }
        return self::raiz() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . self::ARQUIVO_DADOS;
    }

    /** Aplica fuso horário, codificação e relatório de erros da aplicação web. */
    public static function aplicarAmbiente(): void
    {
        date_default_timezone_set(self::FUSO_HORARIO);
        error_reporting(E_ALL);
        ini_set('display_errors', self::MODO_DEBUG ? '1' : '0');
        ini_set('default_charset', 'UTF-8');
    }
}
