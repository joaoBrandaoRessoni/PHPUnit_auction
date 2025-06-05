<?php

namespace Alura\Leilao\tests\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\service\Avaliador;
use DomainException;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    private Avaliador $leiloeiro;

    /**
     * Ordem de execução
     * 1 - setUpBeforeClass()
     * 2 - dataProvider
     * 3 - setUp()
     * 4 - Teste
     * 5 - tearDown()
     * 6 - tearDownAfterClass()
     * */

    protected function setUp() : void{
        $this->leiloeiro = new Avaliador();
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveEncontrarOMaiorValorDeLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiorValor = $this->leiloeiro->getMaiorValor();

        self::assertEquals(2500, $maiorValor);
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveEncontrarOMenorValorDeLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $menorValor = $this->leiloeiro->getMenorValor();

        self::assertEquals(1700, $menorValor);
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveBuscarOsTresMaioresLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiores = $this->leiloeiro->getMaioresLances();

        static::assertCount(3, $maiores);
        static::assertEquals(2500, $maiores[0]->getValor());
        static::assertEquals(2200, $maiores[1]->getValor());
        static::assertEquals(2000, $maiores[2]->getValor());
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado(){
        $this->expectException(DomainException::class);

        $leilao = new Leilao("Fiat 147 0KM");
        $leilao->recebeLance(new Lance(new Usuario("Teste"), 5000));
        $leilao->finaliza();

        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoVazioNaoPodeSerAvaliado(){
        $this->expectException(DomainException::class);

        $leilao = new Leilao("Fusca Azul");
        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeReceberLances(){
        $this->expectException(DomainException::class);

        $leilao = new Leilao("Fiat 147 0KM");
        $leilao->recebeLance(new Lance(new Usuario("Teste"), 5000));
        $leilao->finaliza();

        $leilao->recebeLance(new Lance(new Usuario("Maria"), 2000));
    }

    public static function leilaoEmOrdemCrescente()
    {
        $leilao = new Leilao("Fiat 147 0KM");
        $joao = new Usuario("João");
        $maria = new Usuario("Maria");
        $ana = new Usuario("Ana");
        $jose = new Usuario("José");

        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($jose, 2200));
        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($joao, 1700));

        return [$leilao];
    }

    public static function leilaoEmOrdemDecrescente()
    {
        $leilao = new Leilao("Fiat 147 0KM");
        $joao = new Usuario("João");
        $maria = new Usuario("Maria");
        $ana = new Usuario("Ana");
        $jose = new Usuario("José");

        $leilao->recebeLance(new Lance($ana, 2500));
        $leilao->recebeLance(new Lance($jose, 2200));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 1700));

        return [$leilao];
    }

    public static function leilaoEmOrdemAleatoria()
    {
        $leilao = new Leilao("Fiat 147 0KM");
        $joao = new Usuario("João");
        $maria = new Usuario("Maria");
        $ana = new Usuario("Ana");
        $jose = new Usuario("José");

        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 1700));
        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($jose, 2200));

        return [$leilao];
    }

    public static function entregaLeiloes() {
        return [
            "Ordem Crescente" => self::leilaoEmOrdemCrescente(),
            "Ordem Descrescente" => self::leilaoEmOrdemDecrescente(),
            "Ordem Aletoria" => self::leilaoEmOrdemAleatoria(),
        ];
    }
}
