<?php
declare(strict_types=1);

namespace App\Data;

use App\Models\Biblioteca;
use App\Models\DVD;
use App\Models\Llibre;
use App\Models\Revista;
use App\Models\Usuari;
use DateTime;

use App\Models\Biblioteca;
use App\Models\DVD;
use App\Models\Llibre;
use App\Models\Revista;
use App\Models\Usuari;
use DateTime;

/**
 * Generador de dades inicials de prova per a la Biblioteca Digital.
 */
class DataGenerator
{
    public static function crear(): Biblioteca
    {
        $biblioteca = new Biblioteca('Biblioteca Digital Lacetània');

        // 1. Afegir 4 Llibres (IDs 1 a 4)
        $biblioteca->afegirMaterial(new Llibre(1, 'El nom de la rosa', 'Umberto Eco', 1980, 512));
        $biblioteca->afegirMaterial(new Llibre(2, 'La plaça del Diamant', 'Mercè Rodoreda', 1962, 256));
        $biblioteca->afegirMaterial(new Llibre(3, '1984', 'George Orwell', 1949, 328));
        $biblioteca->afegirMaterial(new Llibre(4, 'Clean Code', 'Robert C. Martin', 2008, 464));

        // 2. Afegir 3 Revistes (IDs 5 a 7)
        $biblioteca->afegirMaterial(new Revista(5, 'National Geographic', 'RBA Revistes', 2024, 285));
        $biblioteca->afegirMaterial(new Revista(6, 'Sàpiens', 'Som Sàpiens', 2024, 264));
        $biblioteca->afegirMaterial(new Revista(7, 'Investigación y Ciencia', 'Prensa Científica', 2023, 556));

        // 3. Afegir 3 DVDs (IDs 8 a 10)
        $biblioteca->afegirMaterial(new DVD(8, 'Interstellar', 'Christopher Nolan', 2014, 169));
        $biblioteca->afegirMaterial(new DVD(9, 'Inception', 'Christopher Nolan', 2010, 148));
        $biblioteca->afegirMaterial(new DVD(10, 'The Matrix', 'Lana i Lilly Wachowski', 1999, 136));

        // 4. Afegir 4 Usuaris (IDs 1 a 4)
        $usuari1 = new Usuari(1, 'Anna Puig', 'anna.puig@mail.com');
        $usuari2 = new Usuari(2, 'Marc Soler', 'marc.soler@mail.com');
        $usuari3 = new Usuari(3, 'Laura Vila', 'laura.vila@mail.com');
        $usuari4 = new Usuari(4, 'Jordi Serra', 'jordi.serra@mail.com');

        $biblioteca->afegirUsuari($usuari1);
        $biblioteca->afegirUsuari($usuari2);
        $biblioteca->afegirUsuari($usuari3);
        $biblioteca->afegirUsuari($usuari4);

        // 5. Crear 2 préstecs actius (un d'ells vençut amb data de fa 21 dies -> 7 dies de retard = 3.50 €)
        $biblioteca->prestarMaterial(1, 1, new DateTime('-21 days')); // Préstec vençut (Llibre #1 a Anna Puig)
        $biblioteca->prestarMaterial(8, 2, new DateTime('-3 days'));  // Préstec dins de termini (DVD #8 a Marc Soler)

        // 6. Crear 1 reserva activa (Llibre #2 reservat per Laura Vila)
        $biblioteca->reservarMaterial(2, 3);

        // Netegem les notificacions generades durant la càrrega inicial
        $usuari1->obtenirNotificacions();
        $usuari2->obtenirNotificacions();
        $usuari3->obtenirNotificacions();
        $usuari4->obtenirNotificacions();

        return $biblioteca;
    }
    public static function crear(): Biblioteca
    {
        $biblioteca = new Biblioteca('Biblioteca Digital Lacetània');

        // 1. Afegir 4 Llibres (IDs 1 a 4)
        $biblioteca->afegirMaterial(new Llibre(1, 'El nom de la rosa', 'Umberto Eco', 1980, 512));
        $biblioteca->afegirMaterial(new Llibre(2, 'La plaça del Diamant', 'Mercè Rodoreda', 1962, 256));
        $biblioteca->afegirMaterial(new Llibre(3, '1984', 'George Orwell', 1949, 328));
        $biblioteca->afegirMaterial(new Llibre(4, 'Clean Code', 'Robert C. Martin', 2008, 464));

        // 2. Afegir 3 Revistes (IDs 5 a 7)
        $biblioteca->afegirMaterial(new Revista(5, 'National Geographic', 'RBA Revistes', 2024, 285));
        $biblioteca->afegirMaterial(new Revista(6, 'Sàpiens', 'Som Sàpiens', 2024, 264));
        $biblioteca->afegirMaterial(new Revista(7, 'Investigación y Ciencia', 'Prensa Científica', 2023, 556));

        // 3. Afegir 3 DVDs (IDs 8 a 10)
        $biblioteca->afegirMaterial(new DVD(8, 'Interstellar', 'Christopher Nolan', 2014, 169));
        $biblioteca->afegirMaterial(new DVD(9, 'Inception', 'Christopher Nolan', 2010, 148));
        $biblioteca->afegirMaterial(new DVD(10, 'The Matrix', 'Lana i Lilly Wachowski', 1999, 136));

        // 4. Afegir 4 Usuaris (IDs 1 a 4)
        $usuari1 = new Usuari(1, 'Anna Puig', 'anna.puig@mail.com');
        $usuari2 = new Usuari(2, 'Marc Soler', 'marc.soler@mail.com');
        $usuari3 = new Usuari(3, 'Laura Vila', 'laura.vila@mail.com');
        $usuari4 = new Usuari(4, 'Jordi Serra', 'jordi.serra@mail.com');

        $biblioteca->afegirUsuari($usuari1);
        $biblioteca->afegirUsuari($usuari2);
        $biblioteca->afegirUsuari($usuari3);
        $biblioteca->afegirUsuari($usuari4);

        // 5. Crear 2 préstecs actius (un d'ells vençut amb data de fa 21 dies -> 7 dies de retard = 3.50 €)
        $biblioteca->prestarMaterial(1, 1, new DateTime('-21 days')); // Préstec vençut (Llibre #1 a Anna Puig)
        $biblioteca->prestarMaterial(8, 2, new DateTime('-3 days'));  // Préstec dins de termini (DVD #8 a Marc Soler)

        // 6. Crear 1 reserva activa (Llibre #2 reservat per Laura Vila)
        $biblioteca->reservarMaterial(2, 3);

        // Netegem les notificacions generades durant la càrrega inicial
        $usuari1->obtenirNotificacions();
        $usuari2->obtenirNotificacions();
        $usuari3->obtenirNotificacions();
        $usuari4->obtenirNotificacions();

        return $biblioteca;
    }
}
