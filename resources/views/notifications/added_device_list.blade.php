{{-- @foreach ($devices as $device) --}}
	@php
              $milliseconds = strtotime($device->last_deviate_time)*1000;
	@endphp
<tr  class="list-{{$device->id}}" data-device="{{$device->device_id}}" data-date="{{$device->last_deviate_time}}" data-milliseconds="{{$milliseconds}}">
	<td>
			@php 
			$equipment = \App\Device::where('sensor_id',$device->device_id)->first();
			@endphp
			@if($equipment==null || $equipment=='')

		<a href="#">
			{{(isset($device->name) && $device->name!='')?$device->name:$device->device_id}}
		</a>
		@else
		<a href="{{url('equipment-details')}}/{{$equipment->company_id}}/{{$equipment->device_id}}">
			{{(isset($equipment->name) && $equipment->name!='')?$equipment->name:$equipment->device_id}}
		</a>
		@endif
	</td>
	<td>
			@if($equipment==null || $equipment=='')
		<a>
			{{null}}
		</a>
		@else
		<a href="{{url('sensor-details')}}/{{$device->company_id}}/{{$device->device_id}}" >
			{{(isset($device->name) && $device->name!='')?$device->name:$device->device_id}}
		</a>
		@endif
	</td>
	{{-- <td>
		Not Triggering
	</td> --}}
			@if($equipment==null || $equipment=='')
	 <td class="text-center"><a href="{{url('equipment-details')}}/{{$device->company_id}}/{{$device->device_id}}" style="color:#212529;text-decoration:none;display:block;"><time class="timeago-{{$device->device_id}}" datetime="{{$device->temeprature_last_updated??''}}">{{ isset($device->last_deviate_time) && $device->last_deviate_time!=''?\Carbon\Carbon::parse($device->last_deviate_time)->diffForHumans():'---' }}</time></a>
      </td>
	  @else
	 <td class="text-center"><a href="{{url('equipment-details')}}/{{$equipment->company_id}}/{{$equipment->device_id}}" style="color:#212529;text-decoration:none;display:block;"><time class="timeago-{{$device->device_id}}" datetime="{{$device->temeprature_last_updated??''}}">{{ isset($device->last_deviate_time) && $device->last_deviate_time!=''?\Carbon\Carbon::parse($device->last_deviate_time)->diffForHumans():'---' }}</time></a>
      </td>
	  @endif
	<td class="text-center removeDevice" deviceId="{{$device->id}}">
		<svg class="fs-18" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36"><path fill="#207da9" d="M18 2a16 16 0 1 0 16 16A16 16 0 0 0 18 2Zm8 22.1a1.4 1.4 0 0 1-2 2l-6-6l-6 6.02a1.4 1.4 0 1 1-2-2l6-6.04l-6.17-6.22a1.4 1.4 0 1 1 2-2L18 16.1l6.17-6.17a1.4 1.4 0 1 1 2 2L20 18.08Z" class="clr-i-solid clr-i-solid-path-1"></path><path fill="none" d="M0 0h36v36H0z"></path></svg>
	</td>
</tr>

<input class="list-{{$device->id}} devicess" type="hidden" name="ids[]" value="{{$device->id}}">
{{-- @endforeach --}}
