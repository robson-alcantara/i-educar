<?php

namespace App\Models;

use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyRegistration
 *
 * @property integer              $id
 * @property boolean              $isTransferred
 * @property boolean              $isAbandoned
 * @property boolean              $isCanceled
 * @property boolean              $bloquear_troca_de_situacao
 * @property boolean              $dependencia
 * @property integer              $cod_matricula
 * @property integer              $ano
 * @property LegacyStudentAbsence $studentAbsence
 * @property LegacyStudentScore   $studentScore
 * @property LegacyCourse         $course
 * @property Collection           $enrollments
 */
class LegacyRegistration extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_matricula';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_ref_cod_serie',
        'ref_ref_cod_escola',
        'ref_cod_curso',
        'ref_cod_aluno',
        'data_cadastro',
        'ano',
        'ref_usuario_cad',
        'dependencia',
        'ativo',
        'aprovado',
        'data_matricula',
        'ultima_matricula',
        'bloquear_troca_de_situacao'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'data_matricula'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_matricula;
    }

    public function isLockedToChangeStatus(): bool
    {
        return $this->bloquear_troca_de_situacao;
    }

    /**
     * @return boolean
     */
    public function getIsDependencyAttribute()
    {
        return $this->dependencia;
    }

    /**
     * @return int
     */
    public function getYearAttribute()
    {
        return $this->ano;
    }

    /**
     * Relação com o aluno.
     *
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    /**
     * Relação com a escola.
     *
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_ref_cod_escola');
    }

    /**
     * Relação com a série.
     *
     * @deprecated
     * @see grade()
     *
     * @return BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(LegacyLevel::class, 'ref_ref_cod_serie');
    }

    /**
     * Relação com a série.
     *
     * @return BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo(LegacyGrade::class, 'ref_ref_cod_serie');
    }

    /**
     * Relação com o curso.
     *
     * @return BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }

    /**
     * @return HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula');
    }

    /**
     * @return HasMany
     */
    public function activeEnrollments()
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula')->where('ativo', 1);
    }

    /**
     * @return HasOne
     */
    public function lastEnrollment()
    {
        $hasOne = $this->hasOne(LegacyEnrollment::class, 'ref_cod_matricula');

        $hasOne->getQuery()->orderByDesc('sequencial');

        return $hasOne;
    }

    /**
     * @return HasMany
     */
    public function exemptions()
    {
        return $this->hasMany(LegacyDisciplineExemption::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('matricula.ativo', 1);
    }

    public function getIsTransferredAttribute()
    {
        return $this->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO;
    }

    public function getIsAbandonedAttribute()
    {
        return $this->aprovado == App_Model_MatriculaSituacao::ABANDONO;
    }

    public function getIsCanceledAttribute()
    {
        return $this->ativo === 0;
    }

    /**
     * @return HasOne
     */
    public function studentAbsence()
    {
        return $this->hasOne(LegacyStudentAbsence::class, 'matricula_id');
    }

    /**
     * @return HasOne
     */
    public function studentScore()
    {
        return $this->hasOne(LegacyStudentScore::class, 'matricula_id');
    }

    /**
     * @return HasOne
     */
    public function studentDescriptiveOpinion()
    {
        return $this->hasOne(LegacyStudentDescriptiveOpinion::class, 'matricula_id');
    }

    /**
     * @return HasMany
     */
    public function dependencies()
    {
        return $this->hasMany(LegacyDisciplineDependence::class, 'ref_cod_matricula', 'cod_matricula');
    }

    /**
     * @return LegacyEvaluationRule
     */
    public function getEvaluationRule()
    {
        $evaluationRuleGradeYear = $this->hasOne(LegacyEvaluationRuleGradeYear::class, 'serie_id', 'ref_ref_cod_serie')
            ->where('ano_letivo', $this->ano)
            ->firstOrFail();

        if ($this->school->utiliza_regra_diferenciada && $evaluationRuleGradeYear->differentiatedEvaluationRule) {
            return $evaluationRuleGradeYear->differentiatedEvaluationRule;
        }

        return $evaluationRuleGradeYear->evaluationRule;
    }

    /**
     * @return string
     */
    public function getStatusDescriptionAttribute()
    {
        return (new RegistrationStatus())->getDescriptiveValues()[(int) $this->aprovado];
    }

    public function scopeMale(Builder $query): Builder
    {
        return $query->join('pmieducar.aluno', 'aluno.cod_aluno', '=', 'matricula.ref_cod_aluno')
            ->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('aluno.ativo', 1)
            ->where('sexo', 'M');
    }

    public function scopeFemale(Builder $query): Builder
    {
        return $query->join('pmieducar.aluno', 'aluno.cod_aluno', '=', 'matricula.ref_cod_aluno')
            ->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('aluno.ativo', 1)
            ->where('sexo', 'F');
    }

    public function scopeLastYear(Builder $query): Builder
    {
        return $query->where('matricula.ano', date('Y') - 1);
    }

    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->where('matricula.ano', date('Y'));
    }
}
