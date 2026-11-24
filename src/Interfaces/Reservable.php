<?php
declare(strict_types=1);

namespace App\Interfaces;

interface Reservable
{
    public function reservar(string $nomUsuari): bool;
    public function cancelarReserva(): bool;
    public function estaReservat(): bool;
    public function getUsuariReserva(): ?string;

}

