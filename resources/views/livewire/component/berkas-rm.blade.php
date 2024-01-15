<div>
    @if($isLoading)
    <div class="d-flex justify-content-center">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    @else
    @if(isset($berkas))
    <div class="row">
        @foreach($berkas as $item)
        <div class="col-5 col-sm-3">
            <a href="https://simrs.rsbhayangkaranganjuk.com/webapps/berkasrawat/{{ $item->lokasi_file }}"
                data-toggle="lightbox" data-width="1280" data-height="700" data-title="{{ $item->lokasi_file }}">
                <img src="https://simrs.rsbhayangkaranganjuk.com/webapps/berkasrawat/{{ $item->lokasi_file }}"
                    class="img-thumbnail" alt="{{ $item->lokasi_file }}">
            </a>
        </div>
        @endforeach
    </div>
    @else
    <h5>Data Kosong</h5>
    @endif
    @endif
</div>

@section('plugins.EkkoLightBox', true)

@push('css')
<style>
    .lightbox {
        z-index: 100000;
    }
</style>
@endpush

@push('js')
<script>
    $('#btn-rm').on('click', function(event){
        event.preventDefault();
        let rm = $(this).data('rm');
        @this.set('rm', rm);
        $('#modal-rm').modal('show');
    });

    $.once(function(){
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox({
                alwaysShowClose: true,
                showArrows: true,
                wrapping: false,
                onShown: function() {
                    console.log('Checking our the events huh?');
                },
                onNavigate: function(direction, itemIndex) {
                    console.log('Navigating '+direction+'. Current item: '+itemIndex);
                }
            });
        });
    });
</script>
@endpush