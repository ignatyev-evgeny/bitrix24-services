<tr>
    <td class="text-center align-middle">{{ $department->id }}</td>
    <td class="text-center align-middle">{{ $department->bitrix_id }}</td>
    <td class="text-center align-middle">{{ $department->name }}</td>
    <td class="text-center align-middle">{{ $department->departmentNameByID($department->parent, $department->portal) }}</td>
    <td class="text-center align-middle">
        <a href="#" class="text-muted">
            <i class="fas fa-user-plus"></i>
        </a>
    </td>
</tr>
