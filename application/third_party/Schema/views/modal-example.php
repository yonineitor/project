<div class="modal fade bd-example-modal-lg" tabindex="-1"aria-hidden="true"  id="help">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-dark ">
<pre class="text-light">
    user:
        id:
            type: int(8)
            unsigned: true
            auto_increment: true
            primary: true
        email:
            type: varchar
            constraint: 120
            null: false
        password:
            type: varchar(120)
            null: false
        name:
            type: varchar(120)
            null: false
            default: ''
        company_id:
            type: int(8)
            index: true
            default: 0
            null: false
        comments:
            type: text
            null: true
        status:
            type: smallint(1)
            null: true
        gender:
            type: enum('MALE','FEMALE')
            default: 'MALE'
        updated_at:
            type: datetime
            default: null
            null: true
        created_at:
            type: datetime
            default: null
            null: true
</pre>
            </div>
        </div>
    </div>
</div>