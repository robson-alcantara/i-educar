<?php

use App\Services\UrlPresigner;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Model\RecursosRealizacaoProvas;
use iEducar\Modules\Educacenso\Model\VeiculoTransporteEscolar;
use iEducar\Support\View\SelectOptions;

require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/clsPmieducarInstituicao.inc.php";
require_once 'image_check.php';
require_once 'App/Model/ZonaLocalizacao.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class AlunoController extends Portabilis_Controller_Page_EditController
{
    use LegacyAddressingFields;

    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';

    protected $_titulo = 'Cadastro de aluno';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;

    protected $_processoAp = 578;

    protected $_deleteOption = true;

    protected $cod_aluno;

    // Variáveis para controle da foto
    var $objPhoto;

    var $arquivoFoto;

    var $file_delete;

    var $caminho_det;

    var $caminho_lst;

    protected $_formMap = array(
        'pessoa' => array(
            'label' => 'Pessoa',
            'help' => '',
        ),

        // 'rg' => array(
        //   'label'  => 'Documento de identidade (RG)',
        //   'help'   => '',
        // ),

        'justificativa_falta_documentacao' => array(
            'label' => 'Justificativa para a falta de documentação',
            'help' => '',
        ),

        'certidao_nascimento' => array(
            'label' => 'Certidão de Nascimento',
            'help' => '',
        ),

        'certidao_casamento' => array(
            'label' => 'Certidão de Casamento',
            'help' => '',
        ),

        'pai' => array(
            'label' => 'Pai',
            'help' => '',
        ),

        'mae' => array(
            'label' => 'Mãe',
            'help' => '',
        ),

        'responsavel' => array(
            'label' => 'Responsável',
            'help' => '',
        ),


        'alfabetizado' => array(
            'label' => 'Alfabetizado',
            'help' => '',
        ),

        'emancipado' => [
            'label' => 'Emancipado'
        ],

        'transporte' => array(
            'label' => 'Transporte escolar público',
            'help' => '',
        ),

        'id' => array(
            'label' => 'Código aluno',
            'help' => '',
        ),

        'aluno_inep_id' => array(
            'label' => 'Código INEP',
            'help' => '',
        ),

        'aluno_estado_id' => array(
            'label' => 'Código rede estadual',
            'help' => '',
        ),


        'deficiencias' => array(
            'label' => 'Deficiências / habilidades especiais',
            'help' => '',
        ),

        'laudo_medico' => array(
            'label' => 'Laudo médico',
            'help' => '',
        ),

        'documento' => array(
            'label' => 'Documentos',
            'help' => '',
        ),

        /* *******************
           ** Dados médicos **
           ******************* */
        'sus' => array('label' => 'Número da Carteira do SUS'),

        'altura' => array('label' => 'Altura/Metro'),

        'peso' => array('label' => 'Peso/Kg'),

        'grupo_sanguineo' => array('label' => 'Grupo sanguíneo'),

        'fator_rh' => array('label' => 'Fator RH'),

        'alergia_medicamento' => array('label' => 'O aluno é alérgico a algum medicamento?'),

        'desc_alergia_medicamento' => array('label' => 'Quais?'),

        'alergia_alimento' => array('label' => 'O aluno é alérgico a algum alimento?'),

        'desc_alergia_alimento' => array('label' => 'Quais?'),

        'doenca_congenita' => array('label' => 'O aluno possui doença congênita?'),

        'desc_doenca_congenita' => array('label' => 'Quais?'),

        'fumante' => array('label' => 'O aluno é fumante?'),

        'doenca_caxumba' => array('label' => 'O aluno já contraiu caxumba?'),

        'doenca_sarampo' => array('label' => 'O aluno já contraiu sarampo?'),

        'doenca_rubeola' => array('label' => 'O aluno já contraiu rubeola?'),

        'doenca_catapora' => array('label' => 'O aluno já contraiu catapora?'),

        'doenca_escarlatina' => array('label' => 'O aluno já contraiu escarlatina?'),

        'doenca_coqueluche' => array('label' => 'O aluno já contraiu coqueluche?'),

        'doenca_outras' => array('label' => 'Outras doenças que o aluno já contraiu'),

        'epiletico' => array('label' => 'O aluno é epilético?'),

        'epiletico_tratamento' => array('label' => 'Está em tratamento?'),

        'hemofilico' => array('label' => 'O aluno é hemofílico?'),

        'hipertenso' => array('label' => 'O aluno tem hipertensão?'),

        'asmatico' => array('label' => 'O aluno é asmático?'),

        'diabetico' => array('label' => 'O aluno é diabético?'),

        'insulina' => array('label' => 'Depende de insulina?'),

        'tratamento_medico' => array('label' => 'O aluno faz algum tratamento médico?'),

        'desc_tratamento_medico' => array('label' => 'Qual?'),

        'medicacao_especifica' => array('label' => 'O aluno está ingerindo medicação específica?'),

        'desc_medicacao_especifica' => array('label' => 'Qual?'),

        'acomp_medico_psicologico' => array('label' => 'O aluno tem acompanhamento médico ou psicológico?'),

        'desc_acomp_medico_psicologico' => array('label' => 'Motivo?'),

        'restricao_atividade_fisica' => array('label' => 'O aluno tem restrição a alguma atividade física?'),

        'desc_restricao_atividade_fisica' => array('label' => 'Qual?'),

        'fratura_trauma' => array('label' => 'O aluno sofreu alguma fratura ou trauma?'),

        'desc_fratura_trauma' => array('label' => 'Qual?'),

        'plano_saude' => array('label' => 'O aluno possui algum plano de saúde?'),

        'desc_plano_saude' => array('label' => 'Qual?'),

        'aceita_hospital_proximo' => array('label' => '<b>Em caso de emergência, autorizo levar meu(minha) filho(a) para o Hospital ou Clínica mais próximos:</b>'),

        'desc_aceita_hospital_proximo' => array('label' => 'Responsável'),

        'responsavel' => array('label' => 'Nome'),

        'responsavel_parentesco' => array('label' => 'Parentesco'),

        'responsavel_parentesco_telefone' => array('label' => 'Telefone'),

        'responsavel_parentesco_celular' => array('label' => 'Celular'),

        /************
         * MORADIA
         ************/

        'moradia' => array('label' => 'Moradia'),

        'material' => array('label' => 'Material'),

        'casa_outra' => array('label' => 'Outro'),

        'moradia_situacao' => array('label' => 'Situação'),

        'quartos' => array('label' => 'Número de quartos'),

        'sala' => array('label' => 'Número de salas'),

        'copa' => array('label' => 'Número de copas'),

        'banheiro' => array('label' => 'Número de banheiros'),

        'garagem' => array('label' => 'Número de garagens'),

        'empregada_domestica' => array('label' => 'Possui empregada doméstica?'),

        'automovel' => array('label' => 'Possui automóvel?'),

        'motocicleta' => array('label' => 'Possui motocicleta?'),

        'geladeira' => array('label' => 'Possui geladeira?'),

        'fogao' => array('label' => 'Possui fogão?'),

        'maquina_lavar' => array('label' => 'Possui máquina de lavar?'),

        'microondas' => array('label' => 'Possui microondas?'),

        'video_dvd' => array('label' => 'Possui vídeo/DVD?'),

        'televisao' => array('label' => 'Possui televisão?'),

        'telefone' => array('label' => 'Possui telefone?'),

        'recursos_tecnologicos' => array('label' => 'Possui acesso à recursos tecnológicos?'),

        'quant_pessoas' => array('label' => 'Quantidades de pessoas residentes no lar'),

        'renda' => array('label' => 'Renda familiar em R$'),

        'agua_encanada' => array('label' => 'Possui água encanada?'),

        'poco' => array('label' => 'Possui poço?'),

        'energia' => array('label' => 'Possui energia?'),

        'esgoto' => array('label' => 'Possui esgoto?'),

        'fossa' => array('label' => 'Possui fossa?'),

        'lixo' => array('label' => 'Possui lixo?'),

        /************
         * PROVA INEP
         ************/
        'recursos_prova_inep' => array('label' => 'Recursos necessários para realização de provas'),

        'recebe_escolarizacao_em_outro_espaco' => array('label' => 'Recebe escolarização em outro espaço (diferente da escola)'),

        'transporte_rota' => array(
            'label' => 'Rota',
            'help' => '',
        ),

        'transporte_ponto' => array(
            'label' => 'Ponto de embarque',
            'help' => '',
        ),

        'transporte_destino' => array(
            'label' => 'Destino (Caso for diferente da rota)',
            'help' => '',
        ),

        'transporte_observacao' => array(
            'label' => 'Observações',
            'help' => '',
        )
    );


    protected function _preConstruct()
    {
        $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";

        $this->breadcrumb("{$nomeMenu} aluno", [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }


    protected function _initNovo()
    {
        return false;
    }


    protected function _initEditar()
    {
        return false;
    }


    public function Gerar()
    {
        $this->url_cancelar = '/intranet/educar_aluno_lst.php';

        $configuracoes = new clsPmieducarConfiguracoesGerais();
        $configuracoes = $configuracoes->detalhe();

        $labels_botucatu = config('legacy.app.mostrar_aplicacao') == 'botucatu';

        if ($configuracoes["justificativa_falta_documentacao_obrigatorio"]) {
            $this->inputsHelper()->hidden('justificativa_falta_documentacao_obrigatorio');
        }

        $cod_aluno = $_GET['id'];

        if ($cod_aluno or $_GET['person']) {
            if ($_GET['person']) {
                $this->cod_pessoa_fj = $_GET['person'];
                $this->inputsHelper()->hidden('person', array('value' => $this->cod_pessoa_fj));
            } else {
                $db = new clsBanco();
                $this->cod_pessoa_fj = $db->CampoUnico("select ref_idpes from pmieducar.aluno where cod_aluno = '$cod_aluno'");
            }

            $documentos = new clsDocumento();
            $documentos->idpes = $this->cod_pessoa_fj;
            $documentos = $documentos->detalhe();
        }

        $foto = false;

        if (is_numeric($this->cod_pessoa_fj)) {
            $objFoto = new ClsCadastroFisicaFoto($this->cod_pessoa_fj);
            $detalheFoto = $objFoto->detalhe();
            if (count($detalheFoto)) {
                $foto = $detalheFoto['caminho'];
            }
        } else {
            $foto = false;
        }

        if ($foto) {
            $this->campoRotulo('fotoAtual_', 'Foto atual', '<img height="117" src="' . (new UrlPresigner())->getPresignedUrl($foto)  . '"/>');
            $this->inputsHelper()->checkbox('file_delete', array('label' => 'Excluir a foto'));
            $this->campoArquivo('file', 'Trocar foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 2MB</span>');
        } else {
            $this->campoArquivo('file', 'Foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 2MB</span>');
        }


        // código aluno
        $options = array('label' => _cl('aluno.detalhe.codigo_aluno'), 'disabled' => true, 'required' => false, 'size' => 25);
        $this->inputsHelper()->integer('id', $options);

        // código aluno inep
        $options = array('label' => $this->_getLabel('aluno_inep_id'), 'required' => false, 'size' => 25, 'max_length' => 12);

        if (!$configuracoes['mostrar_codigo_inep_aluno']) {
            $this->inputsHelper()->hidden('aluno_inep_id', array('value' => null));
        } else {
            $this->inputsHelper()->integer('aluno_inep_id', $options);
        }

        // código aluno rede estadual
        $this->campoRA(
            "aluno_estado_id",
            "Código rede estadual do aluno (RA)",
            $this->aluno_estado_id,
            FALSE
        );

        // código aluno sistema
        if (config('legacy.app.alunos.mostrar_codigo_sistema')) {
            $options = array(
                'label' => config('legacy.app.alunos.codigo_sistema'),
                'required' => false,
                'size' => 25,
                'max_length' => 30
            );
            $this->inputsHelper()->text('codigo_sistema', $options);
        }

        // nome
        $options = array('label' => $this->_getLabel('pessoa'), 'size' => 68);
        $this->inputsHelper()->simpleSearchPessoa('nome', $options);

        // data nascimento
        $options = array('label' => 'Data de nascimento', 'disabled' => true, 'required' => false, 'size' => 25, 'placeholder' => '');
        $this->inputsHelper()->date('data_nascimento', $options);

        // rg
        // $options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false, 'size' => 25);
        // $this->inputsHelper()->integer('rg', $options);

        $options = array(
            'required' => $required,
            'label' => 'RG / Data emissão',
            'placeholder' => 'Documento identidade',
            'value' => $documentos['rg'],
            'max_length' => 25,
            'size' => 27,
            'inline' => true
        );

        $this->inputsHelper()->text('rg', $options);

        // data emissão rg
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emiss\u00e3o',
            'value' => $documentos['data_exp_rg'],
            'size' => 19
        );

        $this->inputsHelper()->date('data_emissao_rg', $options);

        $selectOptions = array( null => 'Órgão emissor' );
        $orgaos        = new clsOrgaoEmissorRg();
        $orgaos        = $orgaos->lista();

        foreach ($orgaos as $orgao)
          $selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];

        $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

        $options = array(
          'required'  => false,
          'label'     => '',
          'value'     => $documentos['idorg_exp_rg'],
          'resources' => $selectOptions,
          'inline'    => true
        );

        $this->inputsHelper()->select('orgao_emissao_rg', $options);


        // uf emissão rg

        $options = array(
          'required' => false,
          'label'    => '',
          'value'    => $documentos['sigla_uf_exp_rg']
        );

        $helperOptions = array(
          'attrName' => 'uf_emissao_rg'
        );

        $this->inputsHelper()->uf($options, $helperOptions);

        $nisPisPasep = '';
        // cpf
        if (is_numeric($this->cod_pessoa_fj)) {
            $fisica = new clsFisica($this->cod_pessoa_fj);
            $fisica = $fisica->detalhe();
            $valorCpf = is_numeric($fisica['cpf']) ? int2CPF($fisica['cpf']) : '';
            $nisPisPasep = int2Nis($fisica['nis_pis_pasep']);
        }

        $this->campoCpf("id_federal", "CPF", $valorCpf);

        $options = [
            'required' => false,
            'label' => 'NIS (PIS/PASEP)',
            'placeholder' => '',
            'value' => $nisPisPasep,
            'max_length' => 11,
            'size' => 20
        ];

        $this->inputsHelper()->integer('nis_pis_pasep', $options);

        // tipo de certidao civil
        $escolha_certidao = 'Tipo certidão civil';
        $selectOptions = array(
            null => $escolha_certidao,
            'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
            91 => 'Nascimento (antigo formato)',
            'certidao_casamento_novo_formato' => 'Casamento (novo formato)',
            92 => 'Casamento (antigo formato)'
        );


        // caso certidao nascimento novo formato tenha sido informado,
        // considera este o tipo da certidão
        if (!empty($documentos['certidao_nascimento'])) {
            $tipoCertidaoCivil = 'certidao_nascimento_novo_formato';
        } else if (!empty($documentos['certidao_casamento'])) {
            $tipoCertidaoCivil = 'certidao_casamento_novo_formato';
        } else {
            $tipoCertidaoCivil = $documentos['tipo_cert_civil'];
        }

        $options = array(
            'required' => false,
            'label' => 'Tipo certidão civil',
            'value' => $tipoCertidaoCivil,
            'resources' => $selectOptions,
            'inline' => true
        );

        $this->inputsHelper()->select('tipo_certidao_civil', $options);

        // termo certidao civil
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Termo',
            'value' => $documentos['num_termo'],
            'max_length' => 8,
            'inline' => true
        );

        $this->inputsHelper()->integer('termo_certidao_civil', $options);

        // livro certidao civil
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Livro',
            'value' => $documentos['num_livro'],
            'max_length' => 8,
            'size' => 15,
            'inline' => true
        );

        $this->inputsHelper()->text('livro_certidao_civil', $options);

        // folha certidao civil
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Folha',
            'value' => $documentos['num_folha'],
            'max_length' => 4,
            'inline' => true
        );

        $this->inputsHelper()->integer('folha_certidao_civil', $options);

        // certidao nascimento (novo padrão)
        $placeholderCertidao = 'Certidão nascimento';
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderCertidao,
            'value' => $documentos['certidao_nascimento'],
            'max_length' => 32,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->integer('certidao_nascimento', $options);

        // certidao casamento (novo padrão)
        $placeholderCertidao = 'Certidão casamento';
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderCertidao,
            'value' => $documentos['certidao_casamento'],
            'max_length' => 32,
            'size' => 50,
        );

        $this->inputsHelper()->integer('certidao_casamento', $options);

        // uf emissão certidão civil
        $options = array(
            'required' => false,
            'label' => 'Estado emissão / Data emissão',
            'value' => $documentos['sigla_uf_cert_civil'],
            'inline' => true
        );

        $helperOptions = array(
            'attrName' => 'uf_emissao_certidao_civil'
        );

        $this->inputsHelper()->uf($options, $helperOptions);

        // data emissão certidão civil
        $placeholderEmissao = 'Data emissão';
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderEmissao,
            'value' => $documentos['data_emissao_cert_civil'],
            'inline' => true
        );

        $this->inputsHelper()->date('data_emissao_certidao_civil', $options);

        $options = array(
            'label' => '',
            'required' => false
          );

        // cartório emissão certidão civil
        $labelCartorio = 'Cartório emissão';
        $options = array(
            'required' => false,
            'label' => $labelCartorio,
            'value' => $documentos['cartorio_cert_civil'],
            'cols' => 45,
            'max_length' => 200,
        );

        $this->inputsHelper()->textArea('cartorio_emissao_certidao_civil', $options);

        // justificativa_falta_documentacao
        $resources = array(
            null => 'Selecione',
            1 => 'O(a) aluno(a) não possui os documentos pessoais solicitados',
            2 => 'A escola não dispõe ou não recebeu os documentos pessoais do(a) aluno(a)'
        );

        $options = array('label' => $this->_getLabel('justificativa_falta_documentacao'),
            'resources' => $resources,
            'required' => false,
            'label_hint' => 'Pelo menos um dos documentos: CPF, NIS, Certidão de Nascimento (novo formato) deve ser informado para não precisar justificar a ausência de documentação',
            'disabled' => true);

        $this->inputsHelper()->select('justificativa_falta_documentacao', $options);

        // Passaporte
        $labelPassaporte = 'Passaporte';
        $options = array(
            'required' => false,
            'label' => $labelPassaporte,
            'value' => $documentos['passaporte'],
            'cols' => 45,
            'max_length' => 20
        );

        $this->inputsHelper()->text('passaporte', $options);

        // pai
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',

            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_um', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_um', $options);

        //dois
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_dois', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_dois', $options);

        //tres
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_tres', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_tres', $options);

        //quatro
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_quatro', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_quatro', $options);

        //cinco
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',

            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_cinco', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_cinco', $options);

        $this->inputPai();

        // mãe
        $this->inputMae();
        /*    // pai
            $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
            $this->inputsHelper()->text('pai', $options);


            // mãe
            $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
            $this->inputsHelper()->text('mae', $options);*/

        // responsável

        // tipo

        $label = $this->_getLabel('responsavel');

        /*$tiposResponsavel = array(null           => $label,
                                  'pai'          => 'Pai',
                                  'mae'          => 'M&atilde;e',
                                  'outra_pessoa' => 'Outra pessoa');*/
        $tiposResponsavel = array(null => 'Informe uma Pessoa primeiro');
        $options = array(
            'label' => 'Responsável',
            'resources' => $tiposResponsavel,
            'required' => true,
            'inline' => true
        );

        $this->inputsHelper()->select('tipo_responsavel', $options);

        // nome
        $helperOptions = array('objectName' => 'responsavel');
        $options = array('label' => '', 'size' => 50, 'required' => true);

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);

        // transporte publico

        $tiposTransporte = array(
            null => 'Selecione',
            'nenhum' => 'N&atilde;o utiliza',
            'municipal' => 'Municipal',
            'estadual' => 'Estadual'
        );

        $options = array(
            'label' => $this->_getLabel('transporte'),
            'resources' => $tiposTransporte,
            'required' => true
        );

        $this->inputsHelper()->select('tipo_transporte', $options);

        $veiculos = VeiculoTransporteEscolar::getDescriptiveValues();
        $helperOptions = ['objectName' => 'veiculo_transporte_escolar'];
        $options = [
            'label' => 'Veículo utilizado',
            'required' => true,
            'options' => [
                'all_values' => $veiculos
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            // Cria lista de rotas
            $obj_rota = new clsModulesRotaTransporteEscolar();
            $obj_rota->setOrderBy(' descricao asc ');
            $lista_rota = $obj_rota->lista();
            $rota_resources = array("" => "Selecione uma rota");
            foreach ($lista_rota as $reg) {
                $rota_resources["{$reg['cod_rota_transporte_escolar']}"] = "{$reg['descricao']}";
            }

            // Transporte Rota
            $options = array('label' => $this->_getLabel('transporte_rota'), 'required' => false, 'resources' => $rota_resources);
            $this->inputsHelper()->select('transporte_rota', $options);

            // Ponto de Embarque
            $options = array('label' => $this->_getLabel('transporte_ponto'), 'required' => false, 'resources' => array("" => "Selecione uma rota acima"));
            $this->inputsHelper()->select('transporte_ponto', $options);

            // Transporte Destino
            $options = array('label' => $this->_getLabel('transporte_destino'), 'required' => false);
            $this->inputsHelper()->simpleSearchPessoaj('transporte_destino', $options);

            // Transporte observacoes
            $options = array('label' => $this->_getLabel('transporte_observacao'), 'required' => false, 'size' => 50, 'max_length' => 255);
            $this->inputsHelper()->textArea('transporte_observacao', $options);

        // religião
        $this->inputsHelper()->religiao(array('required' => false, 'label' => 'Religião'));

        // Benefícios
        $helperOptions = array('objectName' => 'beneficios');
        $options = array(
            'label' => 'Benefícios',
            'size' => 250,
            'required' => false,
            'options' => array('value' => null)
        );

        $this->inputsHelper()->multipleSearchBeneficios('', $options, $helperOptions);

        // Deficiências / habilidades especiais
        $helperOptions = array('objectName' => 'deficiencias');
        $options = array(
            'label' => $this->_getLabel('deficiencias'),
            'size' => 50,
            'required' => false,
            'options' => array('value' => null)
        );

        $this->inputsHelper()->multipleSearchDeficiencias('', $options, $helperOptions);

        // alfabetizado
        $options = array('label' => $this->_getLabel('alfabetizado'), 'value' => 'checked');
        $this->inputsHelper()->checkbox('alfabetizado', $options);

        if (config('legacy.app.alunos.nao_apresentar_campo_alfabetizado')) {
            $this->inputsHelper()->hidden('alfabetizado');
        }

        $options = ['label' => $this->_getLabel('emancipado')];
        $this->inputsHelper()->checkbox('emancipado', $options);

        $this->campoArquivo('documento', $this->_getLabel('documento'), $this->documento, 40, "<br/> <span id='span-documento' style='font-style: italic; font-size= 10px;''> São aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho máximo: 2MB</span>");

        $this->inputsHelper()->hidden('url_documento');

        $this->campoArquivo('laudo_medico', $this->_getLabel('laudo_medico'), $this->laudo_medico, 40, "<br/> <span id='span-laudo_medico' style='font-style: italic; font-size= 10px;''> São aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho máximo: 2MB</span>");

        $this->inputsHelper()->hidden('url_laudo_medico');

        $laudo = config('legacy.app.alunos.laudo_medico_obrigatorio');

        if ($laudo == 1) {
            $this->inputsHelper()->hidden('url_laudo_medico_obrigatorio');
        }

        /* *************************************
           ** Dados para a Aba 'Ficha médica' **
           ************************************* */

        // Histórico de altura e peso

        $this->campoTabelaInicio("historico_altura_peso", "Histórico de altura e peso", array('Data', 'Altura (m)', 'Peso (kg)'));

        $this->inputsHelper()->date('data_historico');

        $this->inputsHelper()->numeric('historico_altura');

        $this->inputsHelper()->numeric('historico_peso');

        $this->campoTabelaFim();

        // Fim histórico de altura e peso

        // altura
        $options = array('label' => $this->_getLabel('altura'), 'size' => 5, 'max_length' => 4, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->numeric('altura', $options);

        // peso
        $options = array('label' => $this->_getLabel('peso'), 'size' => 5, 'max_length' => 6, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->numeric('peso', $options);

        // grupo_sanguineo
        $options = array('label' => $this->_getLabel('grupo_sanguineo'), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('grupo_sanguineo', $options);

        // fator_rh
        $options = array('label' => $this->_getLabel('fator_rh'), 'size' => 5, 'max_length' => 1, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('fator_rh', $options);

        // sus
        $options = array('label' => $this->_getLabel('sus'), 'size' => 20, 'max_length' => 20, 'required' => config('legacy.app.fisica.exigir_cartao_sus'), 'placeholder' => '');
        $this->inputsHelper()->text('sus', $options);

        // alergia_medicamento
        $options = array('label' => $this->_getLabel('alergia_medicamento'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('alergia_medicamento', $options);

        // desc_alergia_medicamento
        $options = array('label' => $this->_getLabel('desc_alergia_medicamento'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_alergia_medicamento', $options);

        // alergia_alimento
        $options = array('label' => $this->_getLabel('alergia_alimento'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('alergia_alimento', $options);

        // desc_alergia_alimento
        $options = array('label' => $this->_getLabel('desc_alergia_alimento'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_alergia_alimento', $options);

        // doenca_congenita
        $options = array('label' => $this->_getLabel('doenca_congenita'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_congenita', $options);

        // desc_doenca_congenita
        $options = array('label' => $this->_getLabel('desc_doenca_congenita'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_doenca_congenita', $options);

        // fumante
        $options = array('label' => $this->_getLabel('fumante'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('fumante', $options);

        // doenca_caxumba
        $options = array('label' => $this->_getLabel('doenca_caxumba'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_caxumba', $options);

        // doenca_sarampo
        $options = array('label' => $this->_getLabel('doenca_sarampo'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_sarampo', $options);

        // doenca_rubeola
        $options = array('label' => $this->_getLabel('doenca_rubeola'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_rubeola', $options);

        // doenca_catapora
        $options = array('label' => $this->_getLabel('doenca_catapora'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_catapora', $options);

        // doenca_escarlatina
        $options = array('label' => $this->_getLabel('doenca_escarlatina'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_escarlatina', $options);

        // doenca_coqueluche
        $options = array('label' => $this->_getLabel('doenca_coqueluche'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_coqueluche', $options);

        // doenca_outras
        $options = array('label' => $this->_getLabel('doenca_outras'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('doenca_outras', $options);

        // epiletico
        $options = array('label' => $this->_getLabel('epiletico'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('epiletico', $options);

        // epiletico_tratamento
        $options = array('label' => $this->_getLabel('epiletico_tratamento'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('epiletico_tratamento', $options);

        // hemofilico
        $options = array('label' => $this->_getLabel('hemofilico'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('hemofilico', $options);

        // hipertenso
        $options = array('label' => $this->_getLabel('hipertenso'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('hipertenso', $options);

        // asmatico
        $options = array('label' => $this->_getLabel('asmatico'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('asmatico', $options);

        // diabetico
        $options = array('label' => $this->_getLabel('diabetico'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('diabetico', $options);

        // insulina
        $options = array('label' => $this->_getLabel('insulina'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('insulina', $options);

        // tratamento_medico
        $options = array('label' => $this->_getLabel('tratamento_medico'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('tratamento_medico', $options);

        // desc_tratamento_medico
        $options = array('label' => $this->_getLabel('desc_tratamento_medico'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_tratamento_medico', $options);

        // medicacao_especifica
        $options = array('label' => $this->_getLabel('medicacao_especifica'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('medicacao_especifica', $options);

        // desc_medicacao_especifica
        $options = array('label' => $this->_getLabel('desc_medicacao_especifica'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_medicacao_especifica', $options);

        // acomp_medico_psicologico
        $options = array('label' => $this->_getLabel('acomp_medico_psicologico'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('acomp_medico_psicologico', $options);

        // desc_acomp_medico_psicologico
        $options = array('label' => $this->_getLabel('desc_acomp_medico_psicologico'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_acomp_medico_psicologico', $options);

        // restricao_atividade_fisica
        $options = array('label' => $this->_getLabel('restricao_atividade_fisica'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('restricao_atividade_fisica', $options);

        // desc_restricao_atividade_fisica
        $options = array('label' => $this->_getLabel('desc_restricao_atividade_fisica'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_restricao_atividade_fisica', $options);

        // fratura_trauma
        $options = array('label' => $this->_getLabel('fratura_trauma'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('fratura_trauma', $options);

        // desc_fratura_trauma
        $options = array('label' => $this->_getLabel('desc_fratura_trauma'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_fratura_trauma', $options);

        // plano_saude
        $options = array('label' => $this->_getLabel('plano_saude'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('plano_saude', $options);

        // desc_plano_saude
        $options = array('label' => $this->_getLabel('desc_plano_saude'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_plano_saude', $options);

        // Levar para hospital mais próximo
        $options = array('label' => $this->_getLabel('aceita_hospital_proximo'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('aceita_hospital_proximo', $options);

        // responsável hospital
        $options = array('label' => $this->_getLabel('desc_aceita_hospital_proximo'), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_aceita_hospital_proximo', $options);

        $this->campoRotulo('tit_dados_responsavel', 'Em caso de emergência, caso não seja encontrado pais ou responsáveis, avisar');

        // responsavel
        $options = array('label' => $this->_getLabel('responsavel'), 'size' => 50, 'max_length' => 50, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('responsavel', $options);

        // responsavel_parentesco
        $options = array('label' => $this->_getLabel('responsavel_parentesco'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('responsavel_parentesco', $options);

        // responsavel_parentesco_telefone
        $options = array('label' => $this->_getLabel('responsavel_parentesco_telefone'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('responsavel_parentesco_telefone', $options);

        // responsavel_parentesco_celular
        $options = array('label' => $this->_getLabel('responsavel_parentesco_celular'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('responsavel_parentesco_celular', $options);

        $moradias = array(
            null => 'Selecione',
            'A' => 'Apartamento',
            'C' => 'Casa',
            'O' => 'Outro'
        );

        $options = array(
            'label' => $this->_getLabel('moradia'),
            'resources' => $moradias,
            'required' => false,
            'inline' => true
        );

        $this->inputsHelper()->select('moradia', $options);

        $materiais_moradia = array(
            'A' => 'Alvenaria',
            'M' => 'Madeira',
            'I' => 'Mista'
        );

        $options = array(
            'label' => null,
            'resources' => $materiais_moradia,
            'required' => false,
            'inline' => true
        );

        $this->inputsHelper()->select('material', $options);

        $options = array('label' => null, 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => 'Descreva');
        $this->inputsHelper()->text('casa_outra', $options);

        $situacoes = array(
            null => 'Selecione',
            '1' => 'Alugado',
            '2' => 'Próprio',
            '3' => 'Cedido',
            '4' => 'Financiado',
            '5' => 'Outros'
        );

        $options = array(
            'label' => $this->_getLabel('moradia_situacao'),
            'resources' => $situacoes,
            'required' => false
        );

        $this->inputsHelper()->select('moradia_situacao', $options);

        $options = array('label' => $this->_getLabel('quartos'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->integer('quartos', $options);

        $options = array('label' => $this->_getLabel('sala'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->integer('sala', $options);

        $options = array('label' => $this->_getLabel('copa'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->integer('copa', $options);

        $options = array('label' => $this->_getLabel('banheiro'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->integer('banheiro', $options);

        $options = array('label' => $this->_getLabel('garagem'), 'size' => 2, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->integer('garagem', $options);

        $options = array('label' => $this->_getLabel('empregada_domestica'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('empregada_domestica', $options);

        $options = array('label' => $this->_getLabel('automovel'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('automovel', $options);

        $options = array('label' => $this->_getLabel('motocicleta'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('motocicleta', $options);

        $options = array('label' => $this->_getLabel('geladeira'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('geladeira', $options);

        $options = array('label' => $this->_getLabel('fogao'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('fogao', $options);

        $options = array('label' => $this->_getLabel('maquina_lavar'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('maquina_lavar', $options);

        $options = array('label' => $this->_getLabel('microondas'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('microondas', $options);

        $options = array('label' => $this->_getLabel('video_dvd'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('video_dvd', $options);

        $options = array('label' => $this->_getLabel('televisao'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('televisao', $options);

        $options = array('label' => $this->_getLabel('telefone'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('telefone', $options);


        $obrigarRecursosTecnologicos = (bool)config('legacy.app.alunos.obrigar_recursos_tecnologicos');
        $this->CampoOculto('obrigar_recursos_tecnologicos', (int) $obrigarRecursosTecnologicos);

        $helperOptions = array('objectName'  => 'recursos_tecnologicos');
        $recursosTecnologicos = [
            'Internet' => 'Acesso à internet (em casa)',
            'Computador' => 'Computador',
            'Smartphone' => 'Smartphone (celular)',
            'WhatsApp' => 'WhatsApp',
            'Nenhum' => 'Nenhum',
        ];

        $options = [
            'label' => $this->_getLabel('recursos_tecnologicos'),
            'size' => 50,
            'required' => $obrigarRecursosTecnologicos,
            'options' => [
                'values' => $this->recursos_tecnologicos,
                'all_values' => $recursosTecnologicos,
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('_', $options, $helperOptions);

        $options = array('label' => $this->_getLabel('quant_pessoas'), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->integer('quant_pessoas', $options);

        $options = array('label' => $this->_getLabel('renda'), 'size' => 5, 'max_length' => 10, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->numeric('renda', $options);

        $options = array('label' => $this->_getLabel('agua_encanada'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('agua_encanada', $options);

        $options = array('label' => $this->_getLabel('poco'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('poco', $options);

        $options = array('label' => $this->_getLabel('energia'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('energia', $options);

        $options = array('label' => $this->_getLabel('esgoto'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('esgoto', $options);

        $options = array('label' => $this->_getLabel('fossa'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('fossa', $options);

        $options = array('label' => $this->_getLabel('lixo'), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('lixo', $options);

        $recursosProvaInep = RecursosRealizacaoProvas::getDescriptiveValues();
        $helperOptions = array('objectName'  => 'recursos_prova_inep');
        $options = array(
            'label' => $this->_getLabel('recursos_prova_inep'),
            'label_hint' => '<a href="#" class="open-dialog-recursos-prova-inep">Regras do preenchimento dos recursos necessários para realização de provas</a>',
            'size' => 50,
            'required' => false,
            'options' => array(
                'values' => $this->recursos_prova_inep,
                'all_values' => $recursosProvaInep));
        $this->inputsHelper()->multipleSearchCustom('_', $options, $helperOptions);

        $selectOptions = array(
            1 => 'Não recebe escolarização fora da escola',
            2 => 'Em hospital',
            3 => 'Em domicílio',
        );

        $options = array(
            'required' => false,
            'label' => $this->_getLabel('recebe_escolarizacao_em_outro_espaco'),
            'resources' => $selectOptions
        );

        $this->inputsHelper()->select('recebe_escolarizacao_em_outro_espaco', $options);

        // Projetos
        $this->campoTabelaInicio("projetos", "Projetos", array("Projeto", "Data inclusão"), "Data desligamento", 'Turno');

        $this->inputsHelper()->text('projeto_cod_projeto', array('required' => false));

        $this->inputsHelper()->date('projeto_data_inclusao', array('required' => false));

        $this->inputsHelper()->date('projeto_data_desligamento', array('required' => false));

        $this->inputsHelper()->select('projeto_turno', array('required' => false, 'resources' => array('' => "Selecione", 1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno', 4 => 'Integral')));

        $this->campoTabelaFim();

        // Fim projetos

        $this->inputsHelper()->simpleSearchMunicipio('pessoa-aluno', array('required' => false, 'size' => 57), array('objectName' => 'naturalidade_aluno'));

        $enderecamentoObrigatorio = false;
        $desativarCamposDefinidosViaCep = true;

        $this->viewAddress();

        // zona localização
        $zonas = App_Model_ZonaLocalizacao::getInstance();
        $zonas = $zonas->getEnums();
        $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localiza&ccedil;&atilde;o', $zonas);

        $options = array(
            'label' => '',
            'placeholder' => 'Zona localização',
            'value' => $this->zona_localizacao,
            'disabled' => $desativarCamposDefinidosViaCep,
            'resources' => $zonas,
            'required' => $enderecamentoObrigatorio
        );

        $this->inputsHelper()->select('zona_localizacao', $options);

        $options = [
            'label' => 'País de residência',
            'value' => $this->pais_residencia ?: PaisResidencia::BRASIL ,
            'resources' => PaisResidencia::getDescriptiveValues(),
            'required' => true,
        ];

        $this->inputsHelper()->select('pais_residencia', $options);

        Portabilis_View_Helper_Application::loadJavascript($this, [
            '/modules/Cadastro/Assets/Javascripts/Endereco.js',
        '/modules/Cadastro/Assets/Javascripts/Addresses.js',
        ]);

        $this->loadResourceAssets($this->getDispatcher());

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $obrigarCamposCenso = FALSE;
        $obrigarDocumentoPessoa = FALSE;
        $obrigarTelefonePessoa = FALSE;

        if ($instituicao && isset($instituicao['obrigar_campos_censo'])) {
            $obrigarCamposCenso = dbBool($instituicao['obrigar_campos_censo']);
        }
        if ($instituicao && isset($instituicao['obrigar_documento_pessoa'])) {
            $obrigarDocumentoPessoa = dbBool($instituicao['obrigar_documento_pessoa']);
        }
        if ($instituicao && isset($instituicao['obrigar_telefone_pessoa'])) {
            $obrigarTelefonePessoa = dbBool($instituicao['obrigar_telefone_pessoa']);
        }
        $this->CampoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);
        $this->CampoOculto('obrigar_documento_pessoa', (int) $obrigarDocumentoPessoa);
        $this->CampoOculto('obrigar_telefone_pessoa', (int) $obrigarTelefonePessoa);

        $racas         = new clsCadastroRaca();
        $racas         = $racas->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, TRUE);

        foreach ($racas as $raca) {
            $selectOptions[$raca['cod_raca']] = $raca['nm_raca'];
        }

        $selectOptions = array(null => 'Selecione') + Portabilis_Array_Utils::sortByValue($selectOptions);

        $this->campoLista('cor_raca', 'Raça', $selectOptions, $this->cod_raca, '', FALSE, '', '', '', $obrigarCamposCenso);

        $zonas = array(
            '' => 'Selecione',
            1  => 'Urbana',
            2  => 'Rural',
        );

        $options = array(
          'label'       => 'Zona Localização',
          'value'       => $this->zona_localizacao_censo,
          'resources'   => $zonas,
          'required'    => $obrigarCamposCenso,
        );

        $this->inputsHelper()->select('zona_localizacao_censo', $options);

        $options = [
            'label' => 'Localização diferenciada',
            'resources' => SelectOptions::localizacoesDiferenciadasPessoa(),
            'required' => false,
        ];

        $this->inputsHelper()->select('localizacao_diferenciada', $options);

        $tiposNacionalidade = array(
            '1'  => 'Brasileiro',
            '2'  => 'Naturalizado brasileiro',
            '3'  => 'Estrangeiro'
        );

        $options = array(
            'label'       => 'Nacionalidade',
            'resources'   => $tiposNacionalidade,
            'required'    => $obrigarCamposCenso,
            'inline'      => TRUE,
            'value'       => $this->tipo_nacionalidade
        );

        $this->inputsHelper()->select('tipo_nacionalidade', $options);

        // pais origem

        $options = array(
          'label'       => '',
          'placeholder' => 'Informe o nome do pais',
          'required'    => $obrigarCamposCenso
        );

        $hiddenInputOptions = array(
            'options' => array(
                'value' => $this->pais_origem_id
            )
        );

        $helperOptions = array(
          'objectName'         => 'pais_origem',
          'hiddenInputOptions' => $hiddenInputOptions
        );
        $this->inputsHelper()->simpleSearchPaisSemBrasil('nome', $options, $helperOptions);
    }


    protected function inputPai()
    {
        $this->addParentsInput('pai');
    }

    protected function inputMae()
    {
        $this->addParentsInput('mae', 'mãe');
    }


    protected function addParentsInput($parentType, $parentTypeLabel = '')
    {
        if (!$parentTypeLabel) {
            $parentTypeLabel = $parentType;
        }

        $parentId = $this->{$parentType . '_id'};

        // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
        //pela antiga interface do cadastro de alunos.

        $hiddenInputOptions = array('options' => array('value' => $parentId));
        $helperOptions = array('objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions);

        $options = array(
            'label' => "Pessoa {$parentTypeLabel}",
            'size' => 69,
            'required' => false
        );

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
    }
}

?>
