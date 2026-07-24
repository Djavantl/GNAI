@props(['title'])
@php
    $normalizedTitle = preg_replace('/^\s*\d+\.\s*/', '', (string) $title);
@endphp

<div class="section-title">{{ $normalizedTitle }}</div>
