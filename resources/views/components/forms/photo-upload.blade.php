@props([
    'name' => 'photo',
    'label' => 'Foto',
    'current' => null, // URL da foto existente (edit)
    'size' => '11rem'  // tamanho do círculo
])

<div class="photo-upload text-center mb-4">

    <label class="photo-upload__label text-title">
        {{ $label }}
    </label>

    <div class="photo-upload__wrapper" style="width: {{ $size }}; height: {{ $size }}">

        <!-- Container clicável -->
        <div class="photo-upload__preview" data-container>

            <!-- Estado vazio -->
            <div class="photo-upload__empty {{ $current ? 'd-none' : '' }}" data-empty>
                <i class="bi bi-camera"></i>
                <span>ADICIONAR<br>FOTO</span>
            </div>

            <!-- Imagem -->
            <img
                src="{{ $current }}"
                class="photo-upload__image {{ $current ? '' : 'd-none' }}"
                data-image
            >
        </div>

        <!-- Remover -->
        <button type="button"
                class="photo-upload__remove {{ $current ? '' : 'd-none' }}"
                data-remove>
            <i class="bi bi-trash"></i>
        </button>

    </div>

    <input type="file"
           name="{{ $name }}"
           class="d-none"
           accept="image/*"
           data-input>

    <input type="hidden" name="remove_photo" value="0" data-remove-flag>

    <div class="photo-upload__help">
        <small>Clique no círculo para selecionar</small>
        <small>JPG, PNG. Máx 2MB.</small>
    </div>
    <div class="photo-upload__divider"></div>
</div>
