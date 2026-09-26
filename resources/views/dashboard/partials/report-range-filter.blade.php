{{-- Shared date range filter for every report tab (sits below the page header, no card wrapper) --}}
<form id="reportRangeForm" method="GET" action="{{ route('reports.index') }}"
    class="report-range-form no-print mb-4">
    <input type="hidden" name="tab" id="reportRangeTab" value="{{ $tab }}">

    <div class="nav nav-pills report-range-presets">
        <button type="submit" name="range" value="today"
            class="nav-link {{ $range === 'today' ? 'active' : '' }}">Today</button>
        <button type="submit" name="range" value="week"
            class="nav-link {{ $range === 'week' ? 'active' : '' }}">This Week</button>
        <button type="submit" name="range" value="month"
            class="nav-link {{ $range === 'month' ? 'active' : '' }}">This Month</button>
        <button type="submit" name="range" value="quarter"
            class="nav-link {{ $range === 'quarter' ? 'active' : '' }}">This Quarter</button>
        <button type="submit" name="range" value="year"
            class="nav-link {{ $range === 'year' ? 'active' : '' }}">This Year</button>
    </div>

    <div class="report-range-dates">
        <div>
            <label class="form-label small text-muted mb-1" for="reportFrom">From</label>
            <input type="date" class="form-control form-control-sm" id="reportFrom" name="from"
                value="{{ request('from', $from->format('Y-m-d')) }}">
        </div>
        <div>
            <label class="form-label small text-muted mb-1" for="reportTo">To</label>
            <input type="date" class="form-control form-control-sm" id="reportTo" name="to"
                value="{{ request('to', $to->format('Y-m-d')) }}">
        </div>
        <button type="submit" name="range" value="custom"
            class="btn btn-dark btn-sm rounded-pill {{ $range === 'custom' ? 'active' : '' }}">Apply</button>
    </div>
</form>

