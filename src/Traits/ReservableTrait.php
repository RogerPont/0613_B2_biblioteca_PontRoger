<?php
declare(strict_types=1);

namespace App\Traits;

trait ReservableTrait
{
    private ?string $usuariReserva = null;

    public function reservar(string $nomUsuari): bool
    {
        if ($this->estaReservat()) {
            return false;
        }
        $this->usuariReserva = $nomUsuari;
        $this->registrarAccio('reservat', $nomUsuari);
        return true;
    }

    public function cancelarReserva(): bool
    {
        if (!$this->estaReservat()) {
            return false;
        }
        $this->usuariReserva = null;
        $this->registrarAccio('reserva_cancel·lada');
        return true;
    }

    public function estaReservat(): bool
    {
        return $this->usuariReserva !== null;
    }

    public function getUsuariReserva(): ?string
    {
        return $this->usuariReserva;
    }
}

