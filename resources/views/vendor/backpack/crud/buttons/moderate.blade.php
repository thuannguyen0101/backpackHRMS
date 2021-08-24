
@if ($crud->hasAccess('status'))
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="form-check form-switch">
        <input class="form-check-input" onchange="getid({{$entry->id}})" type="checkbox" id="flexSwitchCheckChecked" {{$entry->status === 1 ? 'checked':'' }}>
    </div>
@endif
