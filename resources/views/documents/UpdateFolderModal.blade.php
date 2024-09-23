<input type="hidden" name="edit_id" value="{{ $data->id }}">
<div class="mb-3">
    <label>
        Name
    </label>
    <input required type="text" class="form-control" name="name" id='folder-name' value="{{ $data->name }}">
</div>
@if($data->type ==1)
<div class="mb-3">
    <label>
        Upload
    </label>

    <div class="form-group">
        <div class="input-group mb-3">
            <input type="file" class="form-control" name="file" required="" style="width: 100%; height: auto;">
        </div>
    </div>

</div>
@endif


