<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\EstadoMatricula;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'estado'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasRoles;
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function ciclo_academico()
    {
        return $this->belongsTo(AcademicCycle::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscription::class);
    }

    public function librosCreados()
    {
        return $this->hasMany(Libro::class);
    }

    public function videosCreados()
    {
        return $this->hasMany(Video::class);
    }

    public function anunciosCreados()
    {
        return $this->hasMany(Anuncio::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin'    => $this->can('access_admin_panel'),
            'profesor' => $this->can('access_teacher_panel'),
            // Filament llama a canAccessPanel() como parte del callback de
            // Auth::attemptWhen() en el login (ver Filament\Auth\Pages\Login).
            // Si devuelve false aquí, Laravel NUNCA crea la sesión ni dispara
            // el evento Login: el alumno sin matrícula activa directamente no
            // puede iniciar sesión (no solo se le bloquea después de entrar).
            'alumno'   => $this->can('access_student_panel') && $this->tieneMatriculaActiva(),
            default    => false,
        };
    }

    /**
     * ¿Tiene al menos una Inscription con estado_matricula = activa?
     * Usado por canAccessPanel() (bloquea el login) y por el middleware
     * EnsureStudentHasActiveEnrollment (bloquea una sesión ya iniciada si
     * la matrícula se inactiva mientras el alumno sigue conectado).
     */
    public function tieneMatriculaActiva(): bool
    {
        return $this->student
            ?->inscripciones()
            ->where('estado_matricula', EstadoMatricula::ACTIVA->value)
            ->exists() ?? false;
    }
}
