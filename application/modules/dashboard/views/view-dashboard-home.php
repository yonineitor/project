
<div class="d-flex align-items-center p-3 text-white-50 bg-success rounded shadow-sm ">
    <div class="lh-100">
        <h6 class="mb-0 text-white lh-100"><?= _l('Dashboard')?></h6>
        <small>Hi <b><?= \Lib\Auth::user('username') ?></b></small>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-md-6">
        <div class="card animated fadeInDown faster ">
            <div class="card-body">
                <h4>Users</h4>
                <div class="list-group">
                    <!-- -->
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start ">
                        <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Jonatha q</h5>
                        <small>2 hours ago</small>
                        </div>
                        <p class="mb-1">lorem impsim exfdes teldrs.</p>
                        <small>59 events</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start ">
                        <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Jonatha q</h5>
                        <small>2 hours ago</small>
                        </div>
                        <p class="mb-1">lorem impsim exfdes teldrs.</p>
                        <small>559 events</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
