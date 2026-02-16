<form 
    data-dynamic-filter 
    data-target="#students-table"
    class="row g-2"
>

@foreach($fields as $field)
    <div class="col-md-{{ $field['col'] ?? 3 }}">
        <input
            type="text"
            name="{{ $field['name'] }}"
            class="form-control"
            placeholder="{{ $field['placeholder'] }}"
            value="{{ request($field['name']) }}"
            data-filter-input
        >
    </div>
@endforeach

</form>
