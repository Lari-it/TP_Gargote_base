<?php

namespace App\Models;

use CodeIgniter\Model;

class PlatDuJourModel extends Model
{
    protected $table      = 'plat_du_jour';
    protected $primaryKey = 'date_jour';
    protected $returnType = 'array';

    protected array $casts = [
        'date_jour' => 'date',
        'id_plat'   => 'int',
    ];

    protected $allowedFields = ['date_jour', 'id_plat'];
}
