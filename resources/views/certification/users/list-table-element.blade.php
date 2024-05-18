<tr>
    <td class="text-center align-middle">{{ $user->id }}</td>
    <td class="text-center align-middle">{!! $user->bitrix_profile_link !!}</td>
    <td class="text-center align-middle">
        <input type="checkbox" name="active" onchange="changeActiveStatus({{ $user->id }}, {{ $user->int_active }})" data-switch @if($user['active']) checked data-status="true" @else data-status="false" @endif data-off-color="danger" data-on-color="success">
    </td>
    <td class="text-left align-middle">
        <img src="{!! $user->photo_format !!}" alt="{{ $user['name'] }} {{ $user['lastname'] }}" class="img-circle img-size-32 mr-2">
        {!! !empty($user['name']) ? $user['name'] : "<b>Не указано</b>" !!}
    </td>
    <td class="align-middle">{!! $user->email_format !!}</td>
    <td class="text-center align-middle">
        <input type="checkbox" name="is_support" onchange="changeIsSupport({{ $user->id }}, {{ $user->int_is_support }})" data-switch @if($user['is_support']) checked data-status="true" @else data-status="false" @endif data-off-color="danger" data-on-color="success">
    </td>
    <td class="align-middle">{!! implode("<br>",$user->departments()) !!}</td>
    <td class="text-center align-middle">
        <b>{{ $user->lang ?? __('Не указан') }}</b>
    </td>
</tr>