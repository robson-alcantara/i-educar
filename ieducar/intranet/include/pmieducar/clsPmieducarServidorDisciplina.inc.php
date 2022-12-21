<?php

use iEducar\Legacy\Model;

class clsPmieducarServidorDisciplina extends Model
{

    public function __construct(
        public $ref_cod_disciplina = null,
        public  $ref_ref_cod_instituicao = null,
        public $ref_cod_servidor = null,
        public  $ref_cod_curso = null,
        public $ref_cod_funcao = null
    ) {
        $this->_schema = 'pmieducar.';
        $this->_tabela = $this->_schema . 'servidor_disciplina';

        $this->_campos_lista = $this->_todos_campos = 'ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_curso, ref_cod_funcao';
    }

    /**
     * Cria um novo registro.
     *
     * @return bool
     * @throws Exception
     */
    public function cadastra()
    {
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso) &&
            is_numeric($this->ref_cod_funcao)
        ) {
            $db = new clsBanco();

            $campos = '';
            $valores = '';
            $gruda = '';

            $campos .= "{$gruda}ref_cod_disciplina";
            $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
            $gruda = ', ';

            $campos .= "{$gruda}ref_ref_cod_instituicao";
            $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";

            $campos .= "{$gruda}ref_cod_servidor";
            $valores .= "{$gruda}'{$this->ref_cod_servidor}'";

            $campos .= "{$gruda}ref_cod_curso";
            $valores .= "{$gruda}'{$this->ref_cod_curso}'";

            $campos .= "{$gruda}ref_cod_funcao";
            $valores .= "{$gruda}'{$this->ref_cod_funcao}'";

            $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");

            return true;
        }

        return false;
    }

    /**
     * Edita os dados de um registro.
     *
     * @return bool
     * @throws Exception
     */
    public function edita()
    {
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso)
        ) {
            $db = new clsBanco();
            $gruda = '';
            $set = '';

            if ($set) {
                $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_curso = '{$this->ref_cod_curso}'");

                return true;
            }
        }

        return false;
    }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     * @throws Exception
     */
    public function lista(
        $int_ref_cod_disciplina = null,
        $int_ref_ref_cod_instituicao = null,
        $int_ref_cod_servidor = null,
        $int_ref_cod_curso = null,
        $int_ref_cod_funcao = null
    ) {
        $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
        $filtros = '';

        $whereAnd = ' WHERE ';

        if (is_numeric($int_ref_cod_disciplina)) {
            $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_ref_cod_instituicao)) {
            $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_servidor)) {
            $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_curso)) {
            $filtros .= "{$whereAnd} ref_cod_curso = '{$int_ref_cod_curso}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_funcao)) {
            $filtros .= "{$whereAnd} (ref_cod_funcao = '{$int_ref_cod_funcao}' OR ref_cod_funcao is null)";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();

        $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

    /**
     * Retorna um array com os dados de um registro.
     *
     * @return array
     * @throws Exception
     */
    public function detalhe()
    {
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso)
        ) {
            $db = new clsBanco();
            $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}'");
            $db->ProximoRegistro();

            return $db->Tupla();
        }

        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function existe(): bool
    {
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso) &&
            is_numeric($this->ref_cod_funcao)
        ) {
            $db = new clsBanco();

            $sql = "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_curso = '{$this->ref_cod_curso}' AND ref_cod_funcao = '{$this->ref_cod_funcao}'";

            $db->Consulta($sql);
            if ($db->ProximoRegistro()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Exclui um registro.
     *
     * @return bool
     */
    public function excluir()
    {
        if (is_numeric($this->ref_cod_disciplina) &&
            is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor) &&
            is_numeric($this->ref_cod_curso)
        ) {
        }

        return false;
    }

    /**
     * Exclui todos os registros de disciplinas de um servidor.
     *
     * @param null $funcao
     * @return bool
     * @throws Exception
     */
    public function excluirTodos($funcao = null)
    {
        if (is_numeric($this->ref_ref_cod_instituicao) &&
            is_numeric($this->ref_cod_servidor)) {
            $where = '';

            if (is_array($funcao) && count($funcao) && $funcao[0] !== '') {
                $filter = array_filter($funcao, fn($item) => ctype_digit((string) $item));
                $funcao = implode(',', $filter);
                $where = "AND ref_cod_funcao in ({$funcao})";
            }

            $db = new clsBanco();
            $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' {$where}");

            return true;
        }

        return false;
    }
}
