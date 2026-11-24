<?php
declare(strict_types=1);

namespace App\Models;

use App\Exceptions\MaterialNoDisponibleException;
use App\Exceptions\MaterialNoPrestatException;
use App\Exceptions\MaterialNoTrobatException;
use App\Exceptions\UsuariNoTrobatException;
use App\Interfaces\Reservable;
use BadMethodCallException;
use DateTime;

class Biblioteca
{
    private array $materials = [];
    private array $usuaris = [];
    private array $prestecs = [];

    public function __construct(
        private string $nom
    ) {
    }

    public function afegirMaterial(Material $material): void
    {
        $this->materials[$material->getId()] = $material;
    }

    public function eliminarMaterial(int $id): bool
    {
        if (!isset($this->materials[$id])) {
            return false;
        }
        unset($this->materials[$id]);
        return true;
    }

    public function cercarPerId(int $id): ?Material
    {
        return $this->materials[$id] ?? null;
    }


    public function cercarPerTitol(string $titol): array
    {
        return array_values(array_filter(
            $this->materials,
            fn(Material $m): bool => stripos($m->getTitol(), $titol) !== false
        ));
    }

    public function cercarPerAutor(string $autor): array
    {
        return array_values(array_filter(
            $this->materials,
            fn(Material $m): bool => stripos($m->getAutor(), $autor) !== false
        ));
    }


    public function llistarDisponibles(): array
    {
        return array_values(array_filter(
            $this->materials,
            fn(Material $m): bool => $m->isDisponible()
        ));
    }

    public function llistarPrestats(): array
    {
        return array_values(array_filter(
            $this->materials,
            fn(Material $m): bool => !$m->isDisponible()
        ));
    }

    public function llistarPerTipus(string $tipus): array
    {
        return array_values(array_filter(
            $this->materials,
            fn(Material $m): bool => strcasecmp($m->getTipus(), $tipus) === 0
        ));
    }

    public function llistarPrestecsActius(): array
    {
        return array_values(array_filter(
            $this->prestecs,
            fn(Prestec $p): bool => $p->estaActiu()
        ));
    }

    public function llistarReserves(): array
    {
        return array_values(array_filter(
            $this->materials,
            fn(Material $m): bool => ($m instanceof Reservable) && $m->estaReservat()
        ));
    }

    public function llistarPrestecsUsuari(int $usuariId): array
    {
        return array_values(array_filter(
            $this->prestecs,
            fn(Prestec $p): bool => $p->getUsuari()->id === $usuariId
        ));
    }


    public function afegirUsuari(Usuari $usuari): void
    {
        $this->usuaris[$usuari->id] = $usuari;
    }

    public function cercarUsuari(int|string $identificador): ?Usuari
    {
        if (is_int($identificador)) {
            return $this->usuaris[$identificador] ?? null;
        }

        foreach ($this->usuaris as $usuari) {
            if (strcasecmp($usuari->nom, $identificador) === 0 || stripos($usuari->nom, $identificador) !== false) {
                return $usuari;
            }
        }

        return null;
    }


    public function prestarMaterial(int $materialId, int $usuariId, ?DateTime $dataPrestec = null): Prestec
    {
        $material = $this->cercarPerId($materialId);
        if ($material === null) {
            throw new MaterialNoTrobatException("No existeix cap material amb ID {$materialId}");
        }

        $usuari = $this->cercarUsuari($usuariId);
        if ($usuari === null) {
            throw new UsuariNoTrobatException("No existeix cap usuari amb ID {$usuariId}");
        }

        if (!$material->isDisponible()) {
            throw new MaterialNoDisponibleException($materialId, "El material ja està prestat");
        }

        if ($material instanceof Reservable && $material->estaReservat()) {
            if ($material->getUsuariReserva() !== $usuari->nom) {
                throw new MaterialNoDisponibleException(
                    $materialId,
                    "El material està reservat per l'usuari {$material->getUsuariReserva()}"
                );
            }
            // Si el reserva el mateix usuari, es cancel·la la reserva i es presta
            $material->cancelarReserva();
        }

        $material->prestar();
        $prestec = new Prestec($material, $usuari, 14, $dataPrestec);
        $this->prestecs[] = $prestec;
        $usuari->afegirPrestec($material);
        $usuari->afegirNotificacio("Préstec realitzat: {$material->getTitol()}");

        return $prestec;
    }

    public function retornarMaterial(int $materialId): float
    {
        $material = $this->cercarPerId($materialId);
        if ($material === null) {
            throw new MaterialNoTrobatException("No existeix cap material amb ID {$materialId}");
        }

        $prestecActiu = null;
        foreach ($this->prestecs as $prestec) {
            if ($prestec->estaActiu() && $prestec->getMaterial()->getId() === $materialId) {
                $prestecActiu = $prestec;
                break;
            }
        }

        if ($prestecActiu === null || $material->isDisponible()) {
            throw new MaterialNoPrestatException("El material amb ID {$materialId} no estava prestat");
        }

        $prestecActiu->retornar();
        $multa = $prestecActiu->calcularMulta();

        $material->retornar();
        $usuari = $prestecActiu->getUsuari();
        $usuari->eliminarPrestec($materialId);
        $usuari->afegirNotificacio("Retorn registrat: {$material->getTitol()}");

        return $multa;
    }

    public function reservarMaterial(int $materialId, int $usuariId): bool
    {
        $material = $this->cercarPerId($materialId);
        if ($material === null) {
            throw new MaterialNoTrobatException("No existeix cap material amb ID {$materialId}");
        }

        $usuari = $this->cercarUsuari($usuariId);
        if ($usuari === null) {
            throw new UsuariNoTrobatException("No existeix cap usuari amb ID {$usuariId}");
        }

        if (!($material instanceof Reservable)) {
            throw new MaterialNoDisponibleException($materialId, "Les revistes no es poden reservar");
        }

        if ($material->estaReservat()) {
            throw new MaterialNoDisponibleException(
                $materialId,
                "El material ja està reservat per {$material->getUsuariReserva()}"
            );
        }

        $reservat = $material->reservar($usuari->nom);
        if ($reservat) {
            $usuari->afegirNotificacio("Reserva realitzada: {$material->getTitol()}");
        }

        return $reservat;
    }

    public function __call(string $name, array $arguments): mixed
    {
        return match ($name) {
            'getLlibres' => $this->llistarPerTipus('Llibre'),
            'getRevistes' => $this->llistarPerTipus('Revista'),
            'getDVDs' => $this->llistarPerTipus('DVD'),
            'getMaterials' => $this->materials,
            'getUsuaris' => $this->usuaris,
            'getNom' => $this->nom,
            default => throw new BadMethodCallException("El mètode {$name}() no existeix a Biblioteca"),
        };
    }

    public function obtenirEstadistiques(): array
    {
        $multesPendents = 0.0;
        foreach ($this->llistarPrestecsActius() as $prestec) {
            $multesPendents += $prestec->calcularMulta();
        }

        return [
            'total' => count($this->materials),
            'disponibles' => count($this->llistarDisponibles()),
            'prestats' => count($this->llistarPrestats()),
            'reservats' => count($this->llistarReserves()),
            'perTipus' => [
                'Llibre' => count($this->llistarPerTipus('Llibre')),
                'Revista' => count($this->llistarPerTipus('Revista')),
                'DVD' => count($this->llistarPerTipus('DVD')),
            ],
            'multesPendents' => round($multesPendents, 2),
        ];
    }
}
