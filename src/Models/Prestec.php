<?php
declare(strict_types=1);

namespace App\Models;

class Prestec
{
    private \DateTime $dataPrestec;
    private ?\DateTime $dataRetorn = null;

    public function __construct(
        private Material $material,
        private Usuari $usuari,
        private int $diesLimitPrestec = 14,
        ?\DateTime $dataPrestec = null
    ) {
        $this->dataPrestec = $dataPrestec ?? new \DateTime();
    }

    public function retornar(): void
    {
        $this->dataRetorn = new \DateTime();
    }

    public function estaActiu(): bool
    {
        return $this->dataRetorn === null;
    }

    public function calcularDiesRetard(): int
    {
        $diesPendents = $this->getDiesPendents();
        return $diesPendents < 0 ? abs($diesPendents) : 0;
    }

    public function calcularMulta(): float
    {
        $diesRetard = $this->calcularDiesRetard();
        if ($diesRetard <= 0) {
            return 0.0;
        }
        return $this->material->calcularMulta($diesRetard);
    }

    public function estaVencut(): bool
    {
        return $this->getDiesPendents() < 0;
    }

    public function getDiesPendents(): int
    {
        $dataReferencia = $this->dataRetorn ?? new \DateTime();
        $diff = $this->dataPrestec->diff($dataReferencia);
        $diesTranscorreguts = $diff->invert ? 0 : (int) $diff->days;

        return $this->diesLimitPrestec - $diesTranscorreguts;
    }

    public function getMaterial(): Material
    {
        return $this->material;
    }

    public function getUsuari(): Usuari
    {
        return $this->usuari;
    }

    public function getDataPrestec(): \DateTime
    {
        return $this->dataPrestec;
    }

    public function getDataRetorn(): ?\DateTime
    {
        return $this->dataRetorn;
    }

    public function getDiesLimitPrestec(): int
    {
        return $this->diesLimitPrestec;
    }
}
