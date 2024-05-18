<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/adminlte.js') }}"></script>
{{--<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>--}}
{{--<script src="{{ asset('js/demo.js') }}"></script>--}}
<script src="{{ asset('js/pages/dashboard3.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" />
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>

<script src="{{ asset('/plugins/select2/js/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">

<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    $(document).ready( function () {
        $('#myTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 50,
            language: {
                processing: "Подождите...",
                search: "Поиск:",
                lengthMenu: "Показать _MENU_ записей",
                info: "Записи с _START_ до _END_ из _TOTAL_ записей",
                infoEmpty: "Записи с 0 до 0 из 0 записей",
                infoFiltered: "(отфильтровано из _MAX_ записей)",
                loadingRecords: "Загрузка записей...",
                zeroRecords: "Записи отсутствуют.",
                emptyTable: "В таблице отсутствуют данные",
                paginate: {
                    first: "Первая",
                    previous: "Предыдущая",
                    next: "Следующая",
                    last: "Последняя"
                },
                aria: {
                    sortAscending: ": активировать для сортировки столбца по возрастанию",
                    sortDescending: ": активировать для сортировки столбца по убыванию"
                }
            }
        });
    });

</script>