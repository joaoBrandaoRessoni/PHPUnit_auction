<?php

namespace Alura\Leilao\Model;

use Alura\Leilao\Model\Lance;
use DomainException;
use Exception;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;
    private $finalizado; 

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }

    public function recebeLance(Lance $lance)
    {   
        if($this->estaFinalizado()){
            throw new DomainException("Leilão finalizado não pode receber lances");
        }

        if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
            throw new DomainException("Usuário não pode propor 2 lances seguidos");
        }

        $totalLancesUsuario = $this->quantidadeLancesPorUsuario($lance->getUsuario());


        if ($totalLancesUsuario >= 5) {
            throw new DomainException("O usuário não pode propor mais de 5 exceções no mesmo leilão");
        }

        $this->lances[] = $lance;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    public function finaliza(){
        $this->finalizado = true;
    }

    public function estaFinalizado() {
        return $this->finalizado;
    }

    /**
     * Lance $lance
     */
    private function ehDoUltimoUsuario(Lance $lance): bool
    {
        $ultimoLance = $this->lances[array_key_last($this->lances)]->getUsuario();

        return $lance->getUsuario() == $ultimoLance;
    }

    private function quantidadeLancesPorUsuario(Usuario $usuario)
    {
        $totalLancesUsuario = array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() == $usuario) {
                    return $totalAcumulado + 1;
                }

                return $totalAcumulado ?? 0;
            },
            0
        );

        return $totalLancesUsuario;
    }
}
