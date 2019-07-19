<div class="card animated fadeInDown faster" v-cloak>
    <div class="card-body p-2">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" 
                    data-toggle="pill" 
                    href="#content-activities" 
                    role="tab" 
                    aria-controls="content-activities" 
                    aria-selected="true" > Activities <span class="badge badge-light"> {{ activities.length}} </span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    data-toggle="pill" 
                    href="#content-sessions"
                    role="tab"
                    aria-controls="nav-comparedata"
                    aria-selected="true"> <i class="fas fa-wifi"></i> Sessions </a>
            </li>
        </ul>
        <hr class="mt-0 mb-2">

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="content-activities" role="tabpanel" >
                <table class="table table-hover table-sm borderless">
                    <thead class="thead-light">
                        <th>Time</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Notes</th>
                    </thead>
                    <tbody>
                        <tr v-for="item in activities">
                            <td>{{ helper.timeFormat(item.create_at)  }}</td>
                            <td>{{ item.user }}</td>
                            <td v-html="item.event"></td>
                            <td>
                                <div v-show="item.data.type=='set_values'">
                                    Assigned values
                                    <ul class="mb-0 pb-0">
                                        <li v-for="(value, key) in item.data.fields"><span class="text-muted">{{key}}:</span> {{ value}}</li>
                                    </ul>
                                </div>
                                <div v-show="item.data.type==='notes'">{{ item.data.comments }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade " id="content-sessions" role="tabpanel" aria-labelledby="content-sessions">
                <?php 
                    $lastSession = [];
                    foreach( $_['sessions'] as $auth ) : 
                            if(isset($lastSession[$auth['device_id']]))
                                continue;
                            $lastSession[$auth['device_id']] = true;
                        ?>
                        <div class="mb-2 border-bottom">
                            <div class="float-right">
                                <?php if( count($auth['location']) > 0): ?>
                                    <span><?= $auth['location']['city']?>, <?= $auth['location']['region_name']?></span>
                                <?php endif;?>
                            </div>
                            <h6>Device <?= $auth['device_name']?></h6>
                            <p class="text-muted">Last connection <?= $auth['updated_at']?></p>
                        </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
