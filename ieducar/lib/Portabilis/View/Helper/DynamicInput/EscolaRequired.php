<?php

use Illuminate\Support\Facades\Session;

require_once 'App/Model/NivelTipoUsuario.php';

class Portabilis_View_Helper_DynamicInput_EscolaRequired extends Portabilis_View_Helper_DynamicInput_Escola
{
    public function escolaRequired($options = [])
    {
        $nivelUsuario = Session::get('nivel');

        if ($nivelUsuario == App_Model_NivelTipoUsuario::ESCOLA) {
            $options['options']['required'] = true;
        }

        parent::escola($options);
    }
}
