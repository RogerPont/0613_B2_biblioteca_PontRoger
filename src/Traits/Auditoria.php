<?php
declare(strict_types=1);

namespace App\Traits;

trait Auditoria
{
    private array $historial = [];

    public function registrarAccio(string $accio, string $detalls = ''): void
    {
        $this->historial[] = [
            'data' => date('Y-m-d H:i:s'),
            'accio' => $accio,
            'detalls' => $detalls,
        ];
    }

    public function obtenirHistorial(): array
    {
        return $this->historial;
    }

    public function obtenirUltimaAccio(): ?array
    {
        if (empty($this->historial))
            return null;
        return $this->historial[count($this->historial) - 1];
    }


    public function netejarHistorial(): void
    {
        $this->historial = [];
    }

}

