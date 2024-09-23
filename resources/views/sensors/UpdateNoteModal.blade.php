
<input type="hidden" name="edit_id" value="{{ $data->id }}">
<div class="mb-3">
    <label>
        Name
    </label>
    <input required type="text" class="form-control" name="name" id='note-name' value="{{ $data->name }}" >
</div>
<div class="mb-3">
    @if (\Auth::user()->id==1)
    <label>
        Write Notes
     </label> 
     @else
     <label>
         Notes
         @endif
        </label> 

    <div class="form-group">
        <div class="input-group mb-3">
            <textarea class="form-control" name="notes" id="notes-area" cols="30" rows="10">{{ $data->notes }}</textarea>
        </div>
    </div>

</div>