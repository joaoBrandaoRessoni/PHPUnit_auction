<?php

namespace Alura\Leilao\tests\Models;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use DomainException;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{


    /**
     * @dataProvider geraLances
     */
    public function testLeilaoReceberLances(int $numLances, Leilao $leilao, array $valores)
    {

        static::assertCount($numLances, $leilao->getLances());

        foreach ($valores as $i => $valorEsperado) {
            static::assertEquals($valorEsperado, $leilao->getLances()[$i]->getValor());
        }
    }

    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario(){
        $this->expectException(DomainException::class);

        $leilao = new Leilao("Brasília Amarela");
        $joao = new Usuario("João");
        $maria = new Usuario("Maria");

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 3000));
        $leilao->recebeLance(new Lance($maria, 3500));
        $leilao->recebeLance(new Lance($joao, 4000));
        $leilao->recebeLance(new Lance($maria, 4500));
        $leilao->recebeLance(new Lance($joao, 5000));
        $leilao->recebeLance(new Lance($maria, 5500));

        $leilao->recebeLance(new Lance($joao, 6000));
    }

    public function testLeilaoNaoDeveReceberLancesRepetidos(){
        $this->expectException(DomainException::class);

        $leilao = new Leilao("Variante");
        $ana = new Usuario("Ana");

        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));
    }

    public static function geraLances()
    {
        $joao = new Usuario("João");
        $maria = new Usuario("Maria");

        $leilao2Lance = new Leilao("Fiat 147 0KM");
        $leilao2Lance->recebeLance(new Lance($joao, 1000));
        $leilao2Lance->recebeLance(new Lance($maria, 2000));

        $leilao1Lance = new Leilao("Fusca 1972 0KM");
        $leilao1Lance->recebeLance(new Lance($maria, 5000));

        return [
            "Lances-2" => [2, $leilao2Lance, [1000, 2000]],
            "Lances-1" => [1, $leilao1Lance, [5000]]
        ];
    }
}
