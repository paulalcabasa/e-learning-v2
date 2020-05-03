@if ($errors->has($field))
    <span class="invalid-feedback text-danger" role="alert">
        <strong>{{ $errors->first($field) }}</strong>
    </span>
@endif