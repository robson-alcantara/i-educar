habilitaComposicaoItinerario();
habilitaCamposFormacaoTecnica();

$j('#itinerary_type').change(habilitaComposicaoItinerario);
$j('#itinerary_composition').change(habilitaCamposFormacaoTecnica);

function habilitaComposicaoItinerario() {
    let types = [];

    if ($j('#itinerary_type').val()) {
        types = $j('#itinerary_type').val();
    }

    if (types.includes('6')) {
        $j('#itinerary_composition').removeAttr('disabled');
        $j('#itinerary_composition').trigger('chosen:updated');
    } else {
        $j('#itinerary_composition').attr('disabled', 'disabled');
        $j('#itinerary_composition').val([]).trigger('chosen:updated');
        habilitaCamposFormacaoTecnica();
    }
}

function habilitaCamposFormacaoTecnica() {
    console.log('teste');
    let compositions = [];

    if ($j('#itinerary_composition').val()) {
        compositions = $j('#itinerary_composition').val();
    }

    $j('#itinerary_course').attr('disabled', 'disabled');
    $j('#concomitant_itinerary').attr('disabled', 'disabled');

    if (compositions.includes('5')) {
        $j('#itinerary_course').removeAttr('disabled');
        $j('#concomitant_itinerary').removeAttr('disabled');
    }
}
