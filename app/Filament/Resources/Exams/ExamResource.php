<?php

namespace App\Filament\Resources\Exams;

use App\Filament\Resources\Exams\Pages\CreateExam;
use App\Filament\Resources\Exams\Pages\EditExam;
use App\Filament\Resources\Exams\Pages\ListExams;
use App\Filament\Resources\Exams\Schemas\ExamForm;
use App\Filament\Resources\Exams\Tables\ExamsTable;
use App\Models\Exam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

        // 1. Agrupamos en "Usuarios"
    protected static string | UnitEnum | null $navigationGroup = 'Examenes';

    // 3. Personalizamos las etiquetas para que todo aparezca en español
    protected static ?string $navigationLabel = 'Examenes';

    protected static ?string $pluralModelLabel = 'Examenes';

    protected static ?string $modelLabel = 'Examene';

    protected static ?string $recordTitleAttribute = 'Examene';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return ExamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'teacherCourseContent.cicloCourseTeacher.cicloCourse.academicCycle',
                'teacherCourseContent.cicloCourseTeacher.cicloCourse.course',
                'teacherCourseContent.cicloCourseTeacher.teacher.user',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExams::route('/'),
            'create' => CreateExam::route('/create'),
            'edit' => EditExam::route('/{record}/edit'),
        ];
    }
}
