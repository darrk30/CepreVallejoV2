<?php

namespace App\Filament\Alumno\Resources\Reviews\Pages;

use App\Filament\Alumno\Resources\Reviews\ReviewResource;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected string $view = 'filament.alumno.pages.reviews-list';

    public function getTitle(): string
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getReviews(): LengthAwarePaginator
    {
        return Review::query()
            ->where('user_id', auth()->id())
            ->where('estado', 'activo')
            ->orderBy('id')
            ->paginate(12);
    }

    public function deleteReviewAction(): Action
    {
        return Action::make('deleteReview')
            ->label('Eliminar')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Borrar Comentario')
            ->modalDescription('¿Está seguro/a de hacer esto?')
            ->modalSubmitActionLabel('Borrar')
            ->action(function (array $arguments) {
                Review::query()
                    ->where('user_id', auth()->id())
                    ->findOrFail($arguments['record'])
                    ->update(['estado' => 'eliminado']);

                Notification::make()
                    ->title('Comentario eliminado')
                    ->success()
                    ->send();
            });
    }

    public function getResumenHtmlAttribute(): string
    {
        $html = strip_tags($this->mensaje, '<strong><b><em><i><ul><ol><li><br><p>');
        $textoPlano = strip_tags($html);

        if (mb_strlen($textoPlano) <= 140) {
            return $html;
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $contador = 0;
        $truncado = false;

        $recorrer = function ($nodo) use (&$recorrer, &$contador, &$truncado) {
            foreach (iterator_to_array($nodo->childNodes) as $hijo) {
                if ($truncado) {
                    $nodo->removeChild($hijo);
                    continue;
                }
                if ($hijo->nodeType === XML_TEXT_NODE) {
                    $restante = 140 - $contador;
                    if (mb_strlen($hijo->textContent) > $restante) {
                        $hijo->textContent = mb_substr($hijo->textContent, 0, $restante) . '…';
                        $truncado = true;
                    }
                    $contador += mb_strlen($hijo->textContent);
                } else {
                    $recorrer($hijo);
                }
            }
        };

        $recorrer($dom->documentElement);

        return trim(str_replace(['<div>', '</div>'], '', $dom->saveHTML($dom->documentElement)));
    }
}