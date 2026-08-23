<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Verze aplikace
    |--------------------------------------------------------------------------
    |
    | Jediné místo, kde se drží číslo vydání (semver MAJOR.MINOR.PATCH).
    | Při vydání releasu se bumpne tady a stejné číslo jde na git tag
    | jako v<verze> — viz "Vydání nové verze" v docs/deployment.md.
    |
    | Číslo sestavení (git SHA) se sem nepíše, dopočítá se za běhu ze souboru
    | REVISION, který do release zapisuje Deployer. Viz AppVersionService.
    |
    */

    'version' => '13.1.0',

];
