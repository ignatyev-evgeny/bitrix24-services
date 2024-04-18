@dump($user)
<tr>
    <td class="text-center">{{ $user['id'] }}</td>
    <td class="text-center">{{ $user['bitrix_id'] }}</td>
    <td class="text-center">{{ $user['active'] }}</td>
    <td class="text-center">{{ $user['name'] }} {{ $user['lastname'] }}</td>
    <td class="text-center">{{ $user['personal_gender'] }}</td>
    <td class="text-center">{{ $user['personal_birthday'] }}</td>
    <td class="text-center">{{ $user['email'] }}</td>
    <td class="text-center">{{ $user['is_administrator'] }}</td>
    <td class="text-center">
        <input type="checkbox" name="my-checkbox" checked data-switch-manager @if($user['is_manager']) data-status="true" @else data-status="false" @endif data-off-color="danger" data-on-color="success">
    </td>
    <td class="text-center">{{ $user['is_manager'] }}</td>
    <td class="text-center">{{ $user['last_login'] }}</td>
    <td class="text-center">{{ $user['date_register'] }}</td>
    <td class="text-center">{{ $user['lang'] }}</td>
</tr>

<script>
    $("input[data-switch-manager]").each(function(){
        // console.log($(this).attr('data-status'))
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })
</script>