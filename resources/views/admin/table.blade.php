@push('styles')
<!-- Datatables-->
<link rel="stylesheet" href="{{ asset('angle/vendor/datatables.net-bs4/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('angle/vendor/datatables.net-keytable-bs/css/keyTable.bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('angle/vendor/datatables.net-responsive-bs/css/responsive.bootstrap.css') }}">
@endpush

@push('vendor-scripts')
<script src="{{ asset('angle/vendor/jquery-slimscroll/jquery.slimscroll.js') }}"></script><!-- SPARKLINE-->
<script src="{{ asset('angle/vendor/jquery-sparkline/jquery.sparkline.js') }}"></script><!-- Datatables-->
<script src="{{ asset('angle/vendor/datatables.net/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-buttons/js/dataTables.buttons.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-buttons-bs/js/buttons.bootstrap.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-buttons/js/buttons.colVis.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-buttons/js/buttons.flash.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-buttons/js/buttons.html5.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-buttons/js/buttons.print.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-keytable/js/dataTables.keyTable.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-responsive/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('angle/vendor/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
<script src="{{ asset('angle/vendor/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        
        $.extend(true, $.fn.dataTable.defaults, {
            "searching": false,
            "lengthChange": false,
            "processing": true,
            "paging": true,
            "ordering": true, 
            "info": true,
            "responsive": true,
            
            "footerCallback": function(tfoot, data, start, end, display) {
                if (data.length === 0) {
                    $(tfoot).find('th').each(function(index, th) {
                        $(th).empty();
                    });
                }
            },
            
            "language": {
                sSearch: '<em class="fas fa-search"></em>',
                sLengthMenu: '_MENU_ registros por página',
                info: 'Total _TOTAL_ registros',
                zeroRecords: 'No se han encontrado resultados',
                infoEmpty: 'No hay registros disponibles',
                infoFiltered: '(filtrado de _MAX_ registros totales)',
                processing: "Procesando...",
                oPaginate: {
                    sNext: '<em class="fa fa-caret-right"></em>',
                    sPrevious: '<em class="fa fa-caret-left"></em>'
                }
            },            
        });  

        var isDone = false;
        
        $('.datatables').on('click', '[data-destroy]', function(e) {
            var $self = $(this);
            
            if (isDone) {
                isDone = false;
                return;
            }            
            
            e.preventDefault();
            
            swal({
                title: "{{ __('Are you sure to delete this record?') }}",
                buttons: {
                    cancel: {
                        text: "{{ __('No') }}",
                        value: null,
                        visible: true,
                        className: "bg-muted",
                        closeModal: true
                    },
                    confirm: {
                        text: "{{ __('Yes') }}",
                        value: true,
                        visible: true,
                        className: "bg-danger",
                        closeModal: true
                    }
                }
            }).then(function(isConfirm) {
                if (isConfirm) {
                    isDone = true;
                    $self.trigger('click');
                }
            });
        });    
    });
</script>
@endpush