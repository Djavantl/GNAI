<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 8;
            $color = array(50 / 255, 43 / 255, 117 / 255);
            $text = "Página {$pageNumber} de {$pageCount}";
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = $canvas->get_width() - $textWidth - 50;
            $y = $canvas->get_height() - 35;

            $canvas->text($x, $y, $text, $font, $size, $color);
        });
    }
</script>
