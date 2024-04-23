<tr>
    <td class="text-center align-middle">{{ $department->id }}</td>
    <td class="text-center align-middle">{{ $department->bitrix_id }}</td>
    <td class="text-center align-middle">{{ $department->name }}</td>
    <td class="text-center align-middle">{!! $department->managers_format !!}</td>
    <td class="text-center align-middle">
        <a href="#" class="text-muted" data-toggle="modal" data-target="#modal-managers-department-{{ $department->id }}">
            <i class="fas fa-user-plus"></i>
        </a>
    </td>
</tr>
