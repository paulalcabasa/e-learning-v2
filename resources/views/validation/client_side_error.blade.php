<span class="text-danger" role="alert">
    <strong class="invalid-feedback" id="{{ $field_id }}_err"></strong>
</span>

{{-- Configuration for modal closed callback and reset invalid field feedback --}}
@push('scripts')
    <script>
        $(".modal").on("hidden.bs.modal", function () {
            $('#{{ $field_id }}_err').text('');
            $('#file_name').val('');
		});
    </script>
@endpush