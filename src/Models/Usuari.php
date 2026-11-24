<?php
declare(strict_types=1);

namespace App\Models;

use App\Exceptions\EmailInvalidException;
use DateTime;

class Usuari
{
    private int $id;
    private string $nom;
    private string $email;
    private array $materialsPrestats = [];
    private \DateTime $dataRegistre;
    private array $notificacions = [];

    public function __construct(int $id, string $nom, string $email)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->dataRegistre = new \DateTime();

        if (!$this->validarEmail($email)) {
            throw new EmailInvalidException("Email invàlid");
        }
    }

    private function validarEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function __get(string $propietat): mixed
    {
        return match ($propietat) {
            'id' => $this->id,
            'nom' => $this->nom,
            'email' => $this->email,
            'dataRegistre' => $this->dataRegistre,
            default => null,
        };
    }

    public function __set(string $propietat, mixed $valor): void
    {
        if ($propietat !== 'email') {
            throw new \LogicException("No es pot modificar la propietat {$propietat}");
        }
        if (!$this->validarEmail((string) $valor)) {
            throw new EmailInvalidException("Email invàlid");
        }
        $this->email = (string) $valor;
    }
    public function __sleep(): array
    {
        return ['id', 'nom', 'email', 'materialsPrestats', 'dataRegistre'];
    }

    public function __wakeup(): void
    {
        $this->notificacions = [];
    }

    public function __toString(): string
    {
        $num = count($this->materialsPrestats);
        return "{$this->nom} <{$this->email}> - {$num} préstecs";
    }

    public function afegirPrestec(Material $material): void
    {
        $this->materialsPrestats[$material->getId()] = $material;
    }

    public function eliminarPrestec(int $materialId): void
    {
        unset($this->materialsPrestats[$materialId]);
    }

    public function getMaterialsPrestats(): array
    {
        return $this->materialsPrestats;
    }

    public function getNumeroMaterialsPrestats(): int
    {
        return count($this->materialsPrestats);
    }

    public function afegirNotificacio(string $missatge): void
    {
        $this->notificacions[] = $missatge;
    }

    public function obtenirNotificacions(): array
    {
        $notis = $this->notificacions;
        $this->notificacions = [];
        return $notis;
    }
}

