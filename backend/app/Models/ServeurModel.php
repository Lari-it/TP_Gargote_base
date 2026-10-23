<?php

namespace App\Models;

use CodeIgniter\Model;

class ServeurModel extends Model
{
    protected $table      = 'serveur';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected array $casts = [
        'id'    => 'int',
        'actif' => 'bool',
    ];

    protected $allowedFields = ['nom', 'actif'];

    protected $validationRules = [
        'nom'   => 'required|max_length[50]',
        'actif' => 'permit_empty|est_booleen',
    ];

    protected $validationMessages = [
        'nom' => [
            'required'   => 'Le nom du serveur est obligatoire.',
            'max_length' => 'Le nom ne doit pas dépasser 50 caractères.',
        ],
    ];

    public function listerActifs(): array
    {
        return $this->where('actif', true)->findAll();
    }
}
