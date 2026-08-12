<?php

namespace App\Support;

class HtmlTruncator
{
    public static function resumen(string $mensaje, int $limite = 140): string
    {
        $html = strip_tags($mensaje, '<strong><b><em><i><ul><ol><li><br><p>');
        $textoPlano = strip_tags($html);

        if (mb_strlen($textoPlano) <= $limite) {
            return $html;
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $contador = 0;
        $truncado = false;

        $recorrer = function ($nodo) use (&$recorrer, &$contador, &$truncado, $limite) {
            foreach (iterator_to_array($nodo->childNodes) as $hijo) {
                if ($truncado) {
                    $nodo->removeChild($hijo);
                    continue;
                }
                if ($hijo->nodeType === XML_TEXT_NODE) {
                    $restante = $limite - $contador;
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