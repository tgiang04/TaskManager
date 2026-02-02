<!-- BEGIN: main -->
<div class="taskmanager-calendar">
    <div class="page-header">
        <h1>{LANG.calendar}</h1>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <a href="#" class="btn btn-sm btn-outline-secondary">&laquo; {LANG.calendar_month}</a>
                </div>
                <div class="col-md-4 text-center">
                    <h5 class="mb-0">{LANG.calendar_month} {MONTH}/{YEAR}</h5>
                </div>
                <div class="col-md-4 text-right">
                    <a href="#" class="btn btn-sm btn-outline-secondary">{LANG.calendar_month} &raquo;</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered calendar-table">
                    <thead>
                        <tr>
                            <th class="text-center">Chủ nhật</th>
                            <th class="text-center">Thứ 2</th>
                            <th class="text-center">Thứ 3</th>
                            <th class="text-center">Thứ 4</th>
                            <th class="text-center">Thứ 5</th>
                            <th class="text-center">Thứ 6</th>
                            <th class="text-center">Thứ 7</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="calendar-day">
                                <div class="day-number">1</div>
                                <div class="day-tasks">
                                    <!-- Tasks will be populated here -->
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.calendar-table td {
    height: 100px;
    vertical-align: top;
    padding: 5px;
}
.day-number {
    font-weight: bold;
    margin-bottom: 5px;
}
.day-tasks {
    font-size: 12px;
}
</style>
<!-- END: main -->
