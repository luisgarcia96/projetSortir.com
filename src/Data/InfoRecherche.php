<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints\Date;

class InfoRecherche
{
    /**
     * @var int
     */
    public $campus = 0;

    /**
     * @var string
     */
    public $motCle = '';

    /**
     * @var Date
     */
    public $dateDebut = null;

    /**
     * @var Date
     */
    public $dateFin = null;

    /**
     * @var bool
     */
    public $estOrganisateur = false;

    /**
     * @var bool
     */
    public $estInscrit = false;

    /**
     * @var bool
     */
    public $estPassee = false;
}