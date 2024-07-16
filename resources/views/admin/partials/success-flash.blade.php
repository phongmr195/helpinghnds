<div class="row">
    <div class="col-sm-12">
        @if (session('status_success'))
            <div class="alert alert-dismissable alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>
                    {!! session()->get('status_success') !!}
                </strong>
            </div>
        @endif
    </div>
</div>