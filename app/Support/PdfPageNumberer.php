<?php

declare(strict_types=1);

namespace App\Support;

use Barryvdh\DomPDF\PDF;
use Exception;

final class PdfPageNumberer
{
    /**
     * @throws Exception
     */
    public static function apply(PDF $pdf): void
    {
        $pdf->render();

        $canvas = $pdf->getDomPDF()->getCanvas();

        $canvas->page_script(function (int $pageNumber, int $pageCount, $canvas, $fontMetrics): void {
            $font = $fontMetrics->getFont('helvetica', 'normal');
            $size = 8;
            $color = [50 / 255, 43 / 255, 117 / 255];
            $text = "Página {$pageNumber} de {$pageCount}";
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = $canvas->get_width() - $textWidth - 50;
            $y = $canvas->get_height() - 35;

            $canvas->text($x, $y, $text, $font, $size, $color);
        });
    }
}
