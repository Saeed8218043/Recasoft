@if($data['type']=='DEVICE')
<tr class="list-{{$data['device']['deviceId']}}">
	<td>
		<span class="copyable d-inline-block">
			{{$data['device']['deviceId']??''}}
		</span>
	</td>
	<td>
		@if($data['device']['isClaimed']==true)
			<del>{{$data['device']['deviceType']??''}}</del>
		@else
			{{$data['device']['deviceType']??''}}
		@endif
		
	</td>
	<td>
		@if($data['device']['isClaimed']==true)
			Device already claimed
		@else
			Device not claimed
		@endif
	</td>
	<td class="text-center removeDevice" deviceId="{{$data['device']['deviceId']}}">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="color: rgb(244, 81, 108); vertical-align: -0.125em; transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36" class="iconify fs-18" data-icon="clarity:remove-solid"><path fill="currentColor" d="M18 2a16 16 0 1 0 16 16A16 16 0 0 0 18 2Zm8 22.1a1.4 1.4 0 0 1-2 2l-6-6l-6 6.02a1.4 1.4 0 1 1-2-2l6-6.04l-6.17-6.22a1.4 1.4 0 1 1 2-2L18 16.1l6.17-6.17a1.4 1.4 0 1 1 2 2L20 18.08Z" class="clr-i-solid clr-i-solid-path-1"></path><path fill="none" d="M0 0h36v36H0z"></path></svg>
	</td>
</tr>
@if($data['device']['isClaimed']!=true)
	<input class="list-{{$data['device']['deviceId']}} claimDevicess" type="hidden" name="ids[]" value="{{$data['device']['deviceId']}}">
@endif
@else
<tr class="list-{{$data['kit']['kitId']}}">
	<td>
		<span class="copyable d-inline-block">
			{{$data['kit']['kitId']??''}}
		</span>
	</td>
	<td>
		{{$data['kit']['displayName']??''}}

		{{-- @if($data['kit']['isClaimed']==true)
			<del>{{$data['device']['deviceType']??''}}</del>
		@else
			{{$data['device']['deviceType']??''}}
		@endif --}}
		
	</td>
	<td>
		{{count($data['kit']['devices'])}}
		<br>
		@if($claimedDevices>1)
		 
		  {{$claimedDevices}} devices are already claimed
		@endif
		@if($claimedDevices==1)
		 {{$claimedDevices}} device  already claimed
		@endif

	{{-- 	@if($data['kit']['isClaimed']==true)
			Device already claimed
		@else
			Device not claimed
		@endif --}}
	</td>
	<td class="text-center removeDevice" deviceId="{{$data['kit']['kitId']}}">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="color: rgb(244, 81, 108); vertical-align: -0.125em; transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36" class="iconify fs-18" data-icon="clarity:remove-solid"><path fill="currentColor" d="M18 2a16 16 0 1 0 16 16A16 16 0 0 0 18 2Zm8 22.1a1.4 1.4 0 0 1-2 2l-6-6l-6 6.02a1.4 1.4 0 1 1-2-2l6-6.04l-6.17-6.22a1.4 1.4 0 1 1 2-2L18 16.1l6.17-6.17a1.4 1.4 0 1 1 2 2L20 18.08Z" class="clr-i-solid clr-i-solid-path-1"></path><path fill="none" d="M0 0h36v36H0z"></path></svg>
	</td>
</tr>
{{-- @if($data['kit']['isClaimed']!=true) --}}
	<input class="list-{{$data['kit']['kitId']}} claimDevicess" type="hidden" name="kitIds[]" value="{{$data['kit']['kitId']}}">
{{-- @endif --}}
@endif

