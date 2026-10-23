<?php

namespace App\Models;

use CodeIgniter\Model;

class IngredientModel extends Model
{
    protected $table      = 'ingredient';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected array $casts = [
        'id' => 'int',
    ];

    protected $allowedFields = ['nom', 'unite'];

    protected $validationRules = [
        'nom'   => 'required|max_length[100]|is_unique[ingredient.nom]',
        'unite' => 'required|max_length[20]',
    ];

    protected $validationMessages = [
        'nom' => [
            'required'   => 'Le nom de l\'ingrédient est obligatoire.',
            'max_length' => 'Le nom ne doit pas dépasser 100 caractères.',
            'is_unique'  => 'Cet ingrédient existe déjà.',
        ],
        'unite' => [
            'required'   => 'L\'unité est obligatoire.',
            'max_length' => 'L\'unité ne doit pas dépasser 20 caractères.',
        ],
    ];

    public function listerAvecStock(): array
    {
        return $this->db->table('v_stock_actuel')->get()->getResultArray();
    }
}
