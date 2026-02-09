<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->get_font("helvetica", "normal");
        $size = 8;
        $color = array(0, 0, 0);
        $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
        $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
        $rightMargin = 50;
        $x = 508;
        $y = $pdf->get_height() - 35;
        $pdf->page_text(
            $x,
            $y,
            $text,
            $font,
            $size,
            $color
        );
    }
</script>