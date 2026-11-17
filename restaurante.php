<?php


class Restaurante
{

    public string $nome = "Canto do Zé";
    public string $abordagem = "Restaurante de pratos casuais";
    private array $listatipos = ["Comidas", "Bebidas", "Sobremesas"];
    private array $listapratos = ["Arroz com Feijão"];

    public function servircomida(string $prato, int $numcliente, $listapratos): void
    {

        for ($i = 0; $i < count($listapratos); $i++) {

            //Percorre a lista de pratos, tendo como condição o $i ser menor que a quantia de elementos dentro da $listapratos.        
            //in_array serve para verificar se um elemento existe dentro de um array, retornando true para o caso de existir ou false para o contrário.
            //array_splice faz a exclusão do elemento e reorganiza as posições dos elementos restantes automaticamente, fazendo com que, assim, a lista não tenha quaLquer furo.

            if (in_array($prato, $this->listatipos) == false) {
                throw new InvalidArgumentException("Prato não encontrado! Por favor, tente novamente.");
            } else {
                array_splice($this->listatipos, $i, 1);

            }

        }
    }
}

class Cliente
{

    public string $numcliente = "";

}

$Restaurante = new Restaurante();
$Restaurante->servircomida("Arroz com Feijão", 2);

?>