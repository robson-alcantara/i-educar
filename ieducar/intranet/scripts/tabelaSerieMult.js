let opcoesCurso = [];
configuraCamposExibidos();

$j('#multiseriada').change(function(){
    configuraCamposExibidos();
});

$j('#ref_cod_escola').change(function(){
    atualizaOpcoesDeCurso();
});

$j('#btn_add_tab_add_1').click(function(){
    let lastComboId = $j('select[name^="mult_curso_id"]').length;
    let lastCombo = $j('select[name="mult_curso_id['+lastComboId+']"]');
    $j.each(opcoesCurso, function(key, curso) {
        lastCombo.append('<option value="' + key + '">' + curso + '</option>');
    });
});

if ($j('#cod_turma').val() > 0 && $j('#multiseriada').is(':checked')) {
    preencheTabelaSeriesDaTurma();
}

function preencheTabelaSeriesDaTurma() {
    let turmaId = $j('#cod_turma').val();

    var url = getResourceUrlBuilder.buildUrl(
        '/module/Api/Turma',
        'series-da-turma',
        {
            turma_id : turmaId
        }
    );

    var options = {
        url      : url,
        dataType : 'json',
        success  : function(response) {
            let linha = 0;
            $j("[name^=tr_turma_serie]").remove();

            $j.each(response.series_turma, function(key, serie_turma) {
                tab_add_1.addRow();
                linha = key + 1;
                $j('select[id="mult_curso_id['+ linha +']"]').val(serie_turma.ref_cod_curso);
                atualizaOpcoesDeSerie(document.getElementById('mult_curso_id[' + linha +']'), serie_turma.serie_id);
                $j('select[id="mult_boletim_id['+ linha +']"]').val(serie_turma.boletim_id);
                $j('select[id="mult_boletim_diferenciado_id['+ linha +']"]').val(serie_turma.boletim_diferenciado_id);
                $j('input[id="mult_padrao_ano_escolar['+ linha +']"]').val(serie_turma.padrao_ano_escolar);
            });
        }
    };

    getResources(options);
}

function configuraCamposExibidos() {
    let turmaMultisseriada = $j('#multiseriada').is(':checked');
    
    if (turmaMultisseriada) {
        $j('#tr_ref_cod_curso').hide();
        $j('#tr_ref_cod_serie').hide();
        $j('#tr_turma_serie').show();
        if ($j("[name^=tr_turma_serie]").length == 0) {
            tab_add_1.addRow();
            atualizaOpcoesDeCurso();
        }
    } else {
        $j('#tr_ref_cod_curso').show();
        $j('#tr_ref_cod_serie').show();
        $j('#tr_turma_serie').hide();
        $j("[name^=tr_turma_serie]").remove();
    }
}

function atualizaOpcoesDeCurso() {
    let escolaId = $j('#ref_cod_escola').val();

    var url = getResourceUrlBuilder.buildUrl(
        '/module/Api/Curso',
        'cursos-da-escola',
        {
            escola_id : escolaId
        }
    );

    var options = {
        url      : url,
        dataType : 'json',
        success  : function(response) {
            opcoesCurso = response.cursos;
            let combosCurso = $j('select[name^="mult_curso_id"]');
            let combosSerie = $j('select[name^="mult_serie_id"]');
            combosCurso.empty();
            combosSerie.empty();
            combosCurso.append('<option value="">Selecione um curso</option>');
            combosSerie.append('<option value="">Selecione uma série</option>');

            $j.each(response.cursos, function(key, curso) {
                combosCurso.append('<option value="' + key + '">' + curso + '</option>');
            });
        }
    };

    getResources(options);
}

function atualizaInformacoesComBaseNoCurso(inputCurso) {
    atualizaOpcoesDeSerie(inputCurso, 0);
    atualizaPadraoAnoEscolar(inputCurso);
}

function atualizaOpcoesDeSerie(input, value) {
    let instituicaoId = $j('#ref_cod_instituicao').val();
    let escolaId = $j('#ref_cod_escola').val();
    let cursoId = input.value;
    let linha = input.id.replace(/\D/g, '');

    let comboSerie = $j('select[id="mult_serie_id['+linha+']"]');
    comboSerie.empty();
    comboSerie.append('<option value="">Selecione uma série</option>');

    if (cursoId == '') {
        return;
    }

    var url = getResourceUrlBuilder.buildUrl(
        '/module/Api/Serie',
        'series',
        {
            instituicao_id : instituicaoId,
            escola_id : escolaId,
            curso_id : cursoId,
            ativo : 1
        }
    );

    var options = {
        url      : url,
        dataType : 'json',
        success  : function(response) {
            $j.each(response.series, function(key, serie) {
                comboSerie.append('<option value="' + serie.id + '">' + serie.nome + '</option>');
            });

            if (value > 0) {
                $j('select[id="mult_serie_id['+ linha +']"]').val(value);
            }
        }
    };

    getResources(options);
}

function atualizaPadraoAnoEscolar(input) {
    let cursoId = input.value;
    let linha = input.id.replace(/\D/g, '');

    if (cursoId == '') {
        return;
    }

    var url = getResourceUrlBuilder.buildUrl(
        '/module/Api/Curso',
        'dados-curso',
        {
            curso_id : cursoId
        }
    );

    var options = {
        url      : url,
        dataType : 'json',
        success  : function(response) {
            let padrao_ano_escolar = response.dados_curso['padrao_ano_escolar'] ? 1 : 0;
            $j('input[id="mult_padrao_ano_escolar['+ linha +']"]').val(padrao_ano_escolar);
        }
    };

    getResources(options);
}

function atualizaOpcoesAnoLetivo() {
    if ($j('#ano_letivo').prop('disabled')) {
        return;
    }

    let escolaId = $j('#ref_cod_escola').val();
    let series = [];
    let combosSeries = $j('select[name^="mult_serie_id"]');

    $j.each(combosSeries, function(key, serie){
        series.push(serie.value);
    });

    var url = getResourceUrlBuilder.buildUrl(
        '/module/DynamicInput/AnoLetivo',
        'anos_letivos_em_comum_series_da_escola',
        {
            escola_id : escolaId,
            series : series
        }
    );

    var options = {
        url      : url,
        dataType : 'json',
        success  : function(response) {
            let comboAnoLetivo = $j('#ano_letivo');
            comboAnoLetivo.empty();
            comboAnoLetivo.append('<option value="">Selecione um ano letivo</option>');

            $j.each(response.anos_letivos, function(key, ano) {
                comboAnoLetivo.append('<option value="' + ano + '">' + ano + '</option>');
            });
        }
    };

    getResources(options);
}
