<?php

return new class extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $cod_acesso;
    var $data_hora;
    var $ip_externo;
    var $ip_interno;
    var $cod_pessoa;
    var $obs;
    var $sucesso;

    function Gerar()
    {
        $this->titulo = "Acesso - Detalhe";


        $this->cod_acesso=$_GET["cod_acesso"];

        $tmp_obj = new clsPortalAcesso( $this->cod_acesso );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('portal_acesso_lst.php');
        }


        if( $registro["cod_acesso"] )
        {
            $this->addDetalhe( array( "Acesso", "{$registro["cod_acesso"]}") );
        }
        if( $registro["data_hora"] )
        {
            $this->addDetalhe( array( "Data Hora", dataFromPgToBr( $registro["data_hora"], "d/m/Y H:i" ) ) );
        }
        if( $registro["ip_externo"] )
        {
            $this->addDetalhe( array( "Ip Externo", "{$registro["ip_externo"]}") );
        }
        if( $registro["ip_interno"] )
        {
            $this->addDetalhe( array( "Ip Interno", "{$registro["ip_interno"]}") );
        }
        if( $registro["cod_pessoa"] )
        {
            $this->addDetalhe( array( "Pessoa", "{$registro["cod_pessoa"]}") );
        }
        if( $registro["obs"] )
        {
            $this->addDetalhe( array( "Obs", "{$registro["obs"]}") );
        }
        if( ! is_null( $registro["sucesso"] ) )
        {
            $this->addDetalhe( array( "Sucesso", dbBool( $registro["sucesso"] ) ? "Sim": "Não" ) );
        }


        $this->url_novo = "portal_acesso_cad.php";
        $this->url_editar = "portal_acesso_cad.php?cod_acesso={$registro["cod_acesso"]}";

        $this->url_cancelar = "portal_acesso_lst.php";
        $this->largura = "100%";
    }

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Acesso" );
        $this->processoAp = "666";
    }
};


?>
