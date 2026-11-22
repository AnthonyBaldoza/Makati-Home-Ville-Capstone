@extends('layouts.app')
@section('title', 'Facility Reservations')
@section('content')
<style>
    /* Sariling buttons: laging kita, hindi nakadepende sa hover */
    .rv-btn, .rv-btn-out, .rv-btn-danger {
        display:inline-block; padding:9px 16px; border-radius:8px; font:inherit; font-weight:700; font-size:14px;
        cursor:pointer; text-decoration:none; line-height:1.2; border:2px solid #1f4d3d;
    }
    .rv-btn { background:#1f4d3d; color:#fff; }
    .rv-btn:hover { background:#163a2e; border-color:#163a2e; color:#fff; }
    .rv-btn-out { background:#fff; color:#1f4d3d; }
    .rv-btn-out:hover { background:#e8f1ed; color:#1f4d3d; }
    .rv-btn-danger { background:#b91c1c; border-color:#b91c1c; color:#fff; }
    .rv-btn-danger:hover { background:#991b1b; border-color:#991b1b; color:#fff; }

    .rv-bar { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin:12px 0; }
    .rv-bar select { padding:8px 10px; border:1px solid #cbd5d0; border-radius:8px; }
    .rv-alert { padding:10px 14px; border-radius:8px; margin:8px 0; font-size:14px; }
    .rv-alert.err { background:#fde8e8; color:#9b1c1c; border:1px solid #f5b5b5; }
    .rv-alert.ok { background:#e3f4ea; color:#1f5c4d; border:1px solid #b6dfc6; }
    .rv-legend { display:flex; gap:14px; flex-wrap:wrap; font-size:13px; margin-top:10px; }
    .rv-legend i { display:inline-block; width:12px; height:12px; border-radius:3px; margin-right:5px; vertical-align:middle; }
    .rv-modal { position:fixed; inset:0; background:rgba(0,0,0,.45); display:none; align-items:center; justify-content:center; z-index:1000; padding:16px; }
    .rv-modal.open { display:flex; }
    .rv-box { background:#fff; color:#1b2b24; border-radius:12px; padding:20px; width:100%; max-width:440px; max-height:90vh; overflow:auto; }
    .rv-box label { display:block; font-weight:700; font-size:13px; margin:12px 0 4px; }
    .rv-box input, .rv-box select, .rv-box textarea { width:100%; padding:9px 10px; border:1px solid #cbd5d0; border-radius:8px; box-sizing:border-box; font:inherit; }
    .rv-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:16px; }
    .rv-err { color:#9b1c1c; background:#fde8e8; border-radius:8px; padding:8px 10px; margin-top:10px; font-size:13px; display:none; }
    .rv-badge { display:inline-block; padding:2px 10px; border-radius:999px; color:#fff; font-size:12px; }
    .rv-row { margin:6px 0; font-size:14px; }
    .fc-event { cursor:pointer; }

    /* Chat */
    #chatFab { position:fixed; right:20px; bottom:20px; width:56px; height:56px; border-radius:50%; background:#1f4d3d; color:#fff; border:none; font-size:26px; cursor:pointer; z-index:900; box-shadow:0 4px 14px rgba(0,0,0,.3); }
    #chatBadge { position:absolute; top:-4px; right:-4px; background:#dc2626; color:#fff; border-radius:999px; font-size:12px; min-width:20px; height:20px; line-height:20px; text-align:center; display:none; }
    #chatPanel { position:fixed; right:20px; bottom:88px; width:380px; max-width:calc(100vw - 24px); height:540px; max-height:calc(100vh - 110px); background:#fff; color:#1b2b24; border-radius:14px; box-shadow:0 8px 28px rgba(0,0,0,.28); display:none; flex-direction:column; z-index:900; overflow:hidden; }
    #chatPanel.open { display:flex; }
    .ch-head { background:#1f4d3d; color:#fff; padding:12px 14px; display:flex; align-items:center; gap:8px; font-weight:700; }
    .ch-head button { background:none; border:none; color:#fff; cursor:pointer; font-size:18px; }
    .ch-list, .ch-msgs { flex:1; overflow:auto; padding:10px; background:#f5f8f6; }
    .ch-thread { padding:10px; background:#fff; border-radius:8px; margin-bottom:8px; cursor:pointer; border:1px solid #e1e8e4; }
    .ch-thread b { display:block; } .ch-thread small { color:#667; }
    .ch-dot { background:#dc2626; color:#fff; border-radius:999px; font-size:11px; padding:1px 7px; float:right; }
    .ch-m { max-width:85%; margin:6px 0; padding:8px 10px; border-radius:12px; font-size:14px; word-wrap:break-word; overflow-wrap:anywhere; }
    .ch-m small { display:block; opacity:.75; font-size:11px; margin-top:3px; }
    .ch-m.me { background:#1f4d3d; color:#fff; margin-left:auto; }
    .ch-m.them { background:#fff; border:1px solid #e1e8e4; }
    .ch-m.gone { background:#eef1ef; color:#7a8680; font-style:italic; border:1px dashed #c9d2cd; }
    .ch-m img, .ch-m video { max-width:100%; border-radius:8px; display:block; margin-bottom:4px; }
    .ch-m audio { width:210px; max-width:100%; display:block; margin-bottom:4px; }
    .ch-m a.ch-file { display:block; padding:8px 10px; background:rgba(255,255,255,.18); border:1px solid rgba(128,128,128,.4); border-radius:8px; color:inherit; margin-bottom:4px; text-decoration:underline; }
    .ch-acts { margin-top:4px; }
    .ch-acts button { background:none; border:none; color:inherit; font-size:12px; cursor:pointer; text-decoration:underline; padding:2px 10px 0 0; }
    .ch-form { display:flex; gap:6px; padding:8px; border-top:1px solid #e1e8e4; align-items:center; background:#fff; }
    .ch-form input[type=text] { flex:1; min-width:0; padding:9px; border:1px solid #cbd5d0; border-radius:8px; font:inherit; }
    .ch-ic { background:#e8f1ed; border:1px solid #1f4d3d; color:#1f4d3d; border-radius:8px; width:38px; height:38px; font-size:18px; cursor:pointer; flex:none; }
    .ch-note { display:none; background:#fde8e8; color:#9b1c1c; font-size:12px; padding:6px 10px; }
    .ch-prev { display:none; padding:6px 10px; background:#fff8e1; border-top:1px solid #e1e8e4; font-size:13px; align-items:center; gap:8px; }
    .ch-prev img { height:36px; border-radius:6px; }
    .ch-prev button { margin-left:auto; background:none; border:none; font-size:16px; cursor:pointer; color:#9b1c1c; }
    .ch-rec { display:none; gap:8px; padding:10px; border-top:1px solid #e1e8e4; align-items:center; background:#fff; }
    .ch-rec .dot { width:12px; height:12px; border-radius:50%; background:#dc2626; animation:chblink 1s infinite; }
    @keyframes chblink { 50% { opacity:.2; } }
</style>

<div class="card">
    <h1>Facility Reservations</h1>

    <div class="rv-bar">
        <select id="facilityFilter">
            <option value="">Lahat ng facilities</option>
            @foreach ($facilities as $f)
                <option value="{{ $f->id }}">{{ $f->name }}</option>
            @endforeach
        </select>
        <button type="button" class="rv-btn" id="btnNew">+ Reserve a facility</button>
        <a class="rv-btn-out" href="{{ route('reservations.list') }}">Table view</a>
    </div>

    <div id="rvAlert" class="rv-alert" hidden></div>
    <div id="calendar"></div>

    <div class="rv-legend">
        <span><i style="background:#1f5c4d"></i>Approved</span>
        <span><i style="background:#b8860b"></i>Pending</span>
        <span><i style="background:#b91c1c"></i>Declined</span>
        <span><i style="background:#7f1d1d"></i>FULL / Occupied na ang date</span>
    </div>
</div>

{{-- Reserve / Edit form --}}
<div class="rv-modal" id="formModal">
    <div class="rv-box">
        <h3 id="formTitle" style="margin:0">Reserve a facility</h3>
        <input type="hidden" id="fId">

        <label>Facility</label>
        <select id="fFacility">
            @foreach ($facilities as $f)
                <option value="{{ $f->id }}">{{ $f->name }}</option>
            @endforeach
        </select>

        <label>Date</label>
        <input type="date" id="fDate">

        <label>Start time</label>
        <input type="time" id="fStart">

        <label>End time</label>
        <input type="time" id="fEnd">

        <label>Purpose</label>
        <input type="text" id="fPurpose" maxlength="255">

        <p style="font-size:13px;color:#667;margin:10px 0 0" id="fNote"></p>
        <div class="rv-err" id="fErr"></div>

        <div class="rv-actions">
            <button type="button" class="rv-btn" id="fSave">Save</button>
            <button type="button" class="rv-btn-out" data-close="formModal">Close</button>
        </div>
    </div>
</div>

{{-- Details / Review --}}
<div class="rv-modal" id="detailModal">
    <div class="rv-box">
        <h3 style="margin:0" id="dTitle">Reservation</h3>
        <div id="dBody"></div>

        <div id="declineBox" style="display:none">
            <label>Bakit idedecline? (kailangan)</label>
            <textarea id="declineMsg" rows="3" maxlength="500" placeholder="Halimbawa: May activity na po sa oras na yan..."></textarea>
        </div>
        <div class="rv-err" id="dErr"></div>

        <div class="rv-actions" id="dActions"></div>
    </div>
</div>

{{-- Chat --}}
<button type="button" id="chatFab" title="Chat tungkol sa facility reservation">
    💬<span id="chatBadge"></span>
</button>
<div id="chatPanel">
    <div class="ch-head">
        <button type="button" id="chBack" style="display:none" title="Bumalik">←</button>
        <span id="chTitle" style="flex:1">Facility Chat</span>
        <button type="button" id="chClose" title="Isara">✕</button>
    </div>
    <div class="ch-list" id="chList"></div>
    <div class="ch-msgs" id="chMsgs" style="display:none"></div>
    <div class="ch-note" id="chNote"></div>
    <div class="ch-prev" id="chPreview"></div>
    <div class="ch-form" id="chForm" style="display:none">
        <button type="button" class="ch-ic" id="chAttach" title="Mag-attach ng photo, video, o file">📎</button>
        <input type="file" id="chFile" hidden accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip">
        <input type="text" id="chInput" maxlength="1000" placeholder="Mag-type ng message...">
        <button type="button" class="ch-ic" id="chMic" title="Mag-record ng voice message">🎤</button>
        <button type="button" class="rv-btn" id="chSend">Send</button>
    </div>
    <div class="ch-rec" id="chRec">
        <span class="dot"></span><b>Nagre-record...</b><span id="chRecTime">0:00</span>
        <span style="flex:1"></span>
        <button type="button" class="rv-btn-out" id="chRecCancel">Cancel</button>
        <button type="button" class="rv-btn" id="chRecSend">Send</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
(function () {
    const IS_STAFF = @json($isStaff);
    const ME = {{ (int) auth()->id() }};
    const URLS = {
        feed: "{{ route('reservations.calendar.feed') }}",
        store: "{{ route('reservations.calendar.store') }}",
        base: "{{ url('/reservations-calendar') }}",
        threads: "{{ url('/reservation-chat/threads') }}",
        chat: "{{ url('/reservation-chat') }}"
    };
    const CSRF = "{{ csrf_token() }}";
    const $ = (s) => document.querySelector(s);
    const esc = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const todayStr = new Date().toLocaleDateString('en-CA');
    const COLORS = { Approved: '#1f5c4d', Pending: '#b8860b', Declined: '#b91c1c' };
    let fullKeys = new Set();
    let current = null;

    async function api(url, method, body) {
        const isForm = body instanceof FormData;
        const headers = { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' };
        if (body && !isForm) { headers['Content-Type'] = 'application/json'; }
        const res = await fetch(url, { method: method || 'GET', headers, body: body ? (isForm ? body : JSON.stringify(body)) : null });
        let data = {};
        try { data = await res.json(); } catch (e) {}
        if (!res.ok) {
            let msg = data.message || 'May error. Subukan ulit.';
            if (data.errors) { msg = Object.values(data.errors)[0][0]; }
            throw new Error(msg);
        }
        return data;
    }

    function showAlert(msg, type) {
        const el = $('#rvAlert');
        el.textContent = msg;
        el.className = 'rv-alert ' + (type === 'ok' ? 'ok' : 'err');
        el.hidden = false;
        clearTimeout(showAlert.t);
        showAlert.t = setTimeout(() => { el.hidden = true; }, 7000);
    }
    function showErr(sel, msg) { const el = $(sel); el.textContent = msg; el.style.display = msg ? 'block' : 'none'; }
    function openModal(id) { $('#' + id).classList.add('open'); }
    function closeModal(id) { $('#' + id).classList.remove('open'); }
    document.querySelectorAll('[data-close]').forEach((b) => b.addEventListener('click', () => closeModal(b.dataset.close)));

    function pretty(t) {
        const [h, m] = t.split(':').map(Number);
        return ((h % 12) || 12) + ':' + String(m).padStart(2, '0') + ' ' + (h >= 12 ? 'PM' : 'AM');
    }

    /* ---------------- Calendar ---------------- */
    const cal = new FullCalendar.Calendar($('#calendar'), {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
        buttonText: { today: 'Today', dayGridMonth: 'Calendar', listMonth: 'List' },
        events: (info, ok, fail) => {
            const q = new URLSearchParams({ start: info.startStr, end: info.endStr, facility_id: $('#facilityFilter').value });
            api(URLS.feed + '?' + q.toString()).then((events) => {
                fullKeys = new Set(events.filter((e) => e.extendedProps && e.extendedProps.type === 'full')
                    .map((e) => e.extendedProps.facility_id + '|' + e.extendedProps.date));
                ok(events);
            }).catch((e) => { showAlert(e.message, 'err'); fail(e); });
        },
        dateClick: (info) => {
            if (info.dateStr < todayStr) { return showAlert('Hindi na pwedeng mag-reserve sa nakaraang date.', 'err'); }
            const f = $('#facilityFilter').value;
            if (f && fullKeys.has(f + '|' + info.dateStr)) {
                return showAlert('OCCUPIED / FULL na ang date na ito. Wala nang available na oras. Pumili ng ibang date.', 'err');
            }
            openForm(null, info.dateStr);
        },
        eventClick: (info) => {
            info.jsEvent.preventDefault();
            const p = info.event.extendedProps;
            if (p.type === 'full') {
                return showAlert('OCCUPIED / FULL na ang date na ito para sa facility na iyon. Sa ibang date lang may bakante.', 'err');
            }
            openDetails(p);
        }
    });
    cal.render();
    $('#facilityFilter').addEventListener('change', () => cal.refetchEvents());

    /* ---------------- Reserve / Edit form ---------------- */
    function openForm(p, dateStr) {
        showErr('#fErr', '');
        $('#fId').value = p ? p.reservation_id : '';
        $('#formTitle').textContent = p ? 'I-edit ang reservation' : 'Reserve a facility';
        $('#fFacility').value = p ? p.facility_id : ($('#facilityFilter').value || $('#fFacility').options[0].value);
        $('#fDate').value = p ? p.date : (dateStr || todayStr);
        $('#fStart').value = p ? p.start : '09:00';
        $('#fEnd').value = p ? p.end : '10:00';
        $('#fPurpose').value = p ? (p.purpose || '') : '';
        $('#fNote').textContent = IS_STAFF
            ? 'Auto-Approved ang reservation mo bilang HOA/Staff.'
            : 'Magiging Pending muna ito hanggang ma-approve ng HOA/Staff.';
        openModal('formModal');
    }
    $('#btnNew').addEventListener('click', () => openForm(null, todayStr));

    $('#fSave').addEventListener('click', async () => {
        showErr('#fErr', '');
        const id = $('#fId').value;
        const body = {
            facility_id: $('#fFacility').value,
            reservation_date: $('#fDate').value,
            start_time: $('#fStart').value,
            end_time: $('#fEnd').value,
            purpose: $('#fPurpose').value
        };
        try {
            const d = await api(id ? URLS.base + '/' + id : URLS.store, id ? 'PUT' : 'POST', body);
            closeModal('formModal');
            cal.refetchEvents();
            showAlert(d.message || 'Na-save!', 'ok');
        } catch (e) { showErr('#fErr', e.message); }
    });

    /* ---------------- Details / Approve / Decline ---------------- */
    function openDetails(p) {
        current = p;
        showErr('#dErr', '');
        $('#declineBox').style.display = 'none';
        $('#declineMsg').value = '';

        $('#dTitle').textContent = p.facility;
        $('#dBody').innerHTML =
            '<div class="rv-row"><b>Reserved by:</b> ' + esc(p.user_name) + '</div>' +
            '<div class="rv-row"><b>Date:</b> ' + esc(p.date) + '</div>' +
            '<div class="rv-row"><b>Oras:</b> ' + pretty(p.start) + ' - ' + pretty(p.end) + '</div>' +
            '<div class="rv-row"><b>Purpose:</b> ' + esc(p.purpose || '-') + '</div>' +
            '<div class="rv-row"><b>Status:</b> <span class="rv-badge" style="background:' + (COLORS[p.status] || '#555') + '">' + esc(p.status) + '</span></div>' +
            (p.status === 'Declined' && p.decline_message
                ? '<div class="rv-row" style="background:#fde8e8;padding:8px 10px;border-radius:8px"><b>Dahilan ng decline:</b> ' + esc(p.decline_message) + '</div>' : '');

        const owner = p.user_id === ME;
        const canEdit = owner && (IS_STAFF || p.status === 'Pending');
        const btn = [];
        if (IS_STAFF && !owner) {
            if (p.status !== 'Approved') btn.push('<button type="button" class="rv-btn" data-act="approve">✔ Approve</button>');
            if (p.status !== 'Declined') btn.push('<button type="button" class="rv-btn-danger" data-act="decline">✖ Decline</button>');
            btn.push('<button type="button" class="rv-btn-out" data-act="chat">💬 Message</button>');
        }
        if (!IS_STAFF && owner) { btn.push('<button type="button" class="rv-btn-out" data-act="chat">💬 Chat HOA/Staff</button>'); }
        if (canEdit) {
            btn.push('<button type="button" class="rv-btn-out" data-act="edit">✏️ Edit</button>');
            btn.push('<button type="button" class="rv-btn-danger" data-act="delete">🗑 Delete</button>');
        }
        btn.push('<button type="button" class="rv-btn-out" data-act="close">Close</button>');
        $('#dActions').innerHTML = btn.join('');
        openModal('detailModal');
    }

    $('#dActions').addEventListener('click', async (ev) => {
        const act = ev.target.dataset.act;
        if (!act || !current) return;
        showErr('#dErr', '');

        try {
            if (act === 'close') { closeModal('detailModal'); }
            else if (act === 'edit') { closeModal('detailModal'); openForm(current); }
            else if (act === 'chat') {
                closeModal('detailModal');
                openChat(IS_STAFF ? current.user_id : ME, IS_STAFF ? current.user_name : 'HOA / Staff');
            }
            else if (act === 'delete') {
                if (!confirm('Siguradong buburahin ang reservation na ito?')) return;
                const d = await api(URLS.base + '/' + current.reservation_id, 'DELETE');
                closeModal('detailModal'); cal.refetchEvents(); showAlert(d.message, 'ok');
            }
            else if (act === 'approve') {
                const d = await api(URLS.base + '/' + current.reservation_id + '/review', 'POST', { action: 'approve' });
                closeModal('detailModal'); cal.refetchEvents(); showAlert(d.message, 'ok');
            }
            else if (act === 'decline') {
                $('#declineBox').style.display = 'block';
                ev.target.dataset.act = 'confirm-decline';
                ev.target.textContent = 'Confirm Decline';
                $('#declineMsg').focus();
            }
            else if (act === 'confirm-decline') {
                const msg = $('#declineMsg').value.trim();
                if (!msg) { return showErr('#dErr', 'Mag-type muna ng message kung bakit idedecline bago ituloy.'); }
                const d = await api(URLS.base + '/' + current.reservation_id + '/review', 'POST', { action: 'decline', message: msg });
                closeModal('detailModal'); cal.refetchEvents(); showAlert(d.message, 'ok');
            }
        } catch (e) { showErr('#dErr', e.message); }
    });

    /* ---------------- Chat ---------------- */
    const MAX_FILE = 10 * 1024 * 1024;
    let chatOpen = false, curThread = null, lastSig = null;
    let pendingFile = null, editingId = null;
    const bodies = {};
    let rec = null, recChunks = [], recStream = null, recTimer = null, recStart = 0;

    function chatNote(msg) {
        const el = $('#chNote');
        el.textContent = msg;
        el.style.display = msg ? 'block' : 'none';
        clearTimeout(chatNote.t);
        if (msg) { chatNote.t = setTimeout(() => { el.style.display = 'none'; }, 7000); }
    }

    function setBadge(n) {
        const b = $('#chatBadge');
        b.textContent = n > 99 ? '99+' : n;
        b.style.display = n > 0 ? 'block' : 'none';
    }

    async function refreshThreads() {
        try {
            const d = await api(URLS.threads);
            setBadge(d.total_unread);
            if (chatOpen && curThread === null) { renderThreads(d.threads); }
        } catch (e) {}
    }

    function renderThreads(threads) {
        $('#chList').innerHTML = threads.length ? threads.map((t) =>
            '<div class="ch-thread" data-id="' + t.resident_id + '" data-name="' + esc(t.name) + '">' +
            (t.unread ? '<span class="ch-dot">' + t.unread + '</span>' : '') +
            '<b>' + esc(t.name) + '</b><small>' + esc((t.last || 'Wala pang message').slice(0, 60)) + '</small></div>'
        ).join('') : '<p style="color:#667;font-size:14px">Wala pang residents.</p>';
    }

    $('#chList').addEventListener('click', (ev) => {
        const el = ev.target.closest('.ch-thread');
        if (el) openThread(Number(el.dataset.id), el.dataset.name);
    });

    function showList() {
        cancelRec(); cancelEdit(); clearPending();
        curThread = null;
        $('#chTitle').textContent = 'Facility Chat';
        $('#chBack').style.display = 'none';
        $('#chList').style.display = 'block';
        $('#chMsgs').style.display = 'none';
        $('#chForm').style.display = 'none';
        refreshThreads();
    }

    function openThread(id, name) {
        cancelRec(); cancelEdit(); clearPending();
        curThread = id; lastSig = null;
        $('#chTitle').textContent = name;
        $('#chBack').style.display = IS_STAFF ? 'inline' : 'none';
        $('#chList').style.display = 'none';
        $('#chMsgs').style.display = 'block';
        $('#chForm').style.display = 'flex';
        loadMessages();
        $('#chInput').focus();
    }

    function size(n) {
        if (!n) return '';
        return n > 1048576 ? (n / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(n / 1024)) + ' KB';
    }

    function bubble(m) {
        const cls = m.mine ? 'me' : 'them';
        if (m.deleted) { return '<div class="ch-m ' + cls + ' gone">Na-unsend ang message na ito</div>'; }
        bodies[m.id] = m.body;
        let inner = '';
        const a = m.attachment;
        if (a) {
            const u = esc(a.url);
            if (a.kind === 'image') inner += '<a href="' + u + '" target="_blank"><img src="' + u + '" alt="' + esc(a.name) + '"></a>';
            else if (a.kind === 'video') inner += '<video src="' + u + '" controls preload="metadata"></video>';
            else if (a.kind === 'audio') inner += '<audio src="' + u + '" controls preload="metadata"></audio>';
            else inner += '<a class="ch-file" href="' + u + '" target="_blank">📄 ' + esc(a.name) + ' (' + size(a.size) + ')</a>';
        }
        if (m.body) inner += '<div>' + esc(m.body) + '</div>';
        const meta = '<small>' + (m.mine ? '' : esc(m.sender) + ' · ') + esc(m.time) + (m.edited ? ' · edited' : '') + '</small>';
        const acts = m.mine
            ? '<div class="ch-acts">' + (m.body ? '<button type="button" data-ch="edit" data-id="' + m.id + '">✏️ Edit</button>' : '') +
              '<button type="button" data-ch="del" data-id="' + m.id + '">🗑 Unsend</button></div>'
            : '';
        return '<div class="ch-m ' + cls + '">' + inner + meta + acts + '</div>';
    }

    async function loadMessages() {
        if (curThread === null) return;
        try {
            const d = await api(URLS.chat + '/' + curThread);
            const sig = d.messages.map((m) => m.id + ':' + m.v).join(',');
            if (sig !== lastSig) {
                const box = $('#chMsgs');
                box.innerHTML = d.messages.length ? d.messages.map(bubble).join('')
                    : '<p style="color:#667;font-size:14px">Wala pang message. Mag-hi na! 👋</p>';
                box.scrollTop = box.scrollHeight;
                lastSig = sig;
            }
            refreshThreads();
        } catch (e) {}
    }

    $('#chMsgs').addEventListener('click', async (ev) => {
        const b = ev.target.closest('button[data-ch]');
        if (!b) return;
        const id = Number(b.dataset.id);
        if (b.dataset.ch === 'edit') { startEdit(id); }
        else if (b.dataset.ch === 'del') {
            if (!confirm('I-unsend ang message na ito? Mawawala rin ito sa kausap mo.')) return;
            try { await api(URLS.chat + '/message/' + id, 'DELETE'); if (editingId === id) cancelEdit(); lastSig = null; loadMessages(); }
            catch (e) { chatNote(e.message); }
        }
    });

    /* edit */
    function startEdit(id) {
        clearPending();
        editingId = id;
        $('#chInput').value = bodies[id] || '';
        $('#chInput').focus();
        $('#chSend').textContent = 'Save';
        $('#chAttach').disabled = true; $('#chMic').disabled = true;
        const p = $('#chPreview');
        p.style.display = 'flex';
        p.innerHTML = '<span>✏️ Ine-edit mo ang message</span><button type="button" id="chCancelEdit" title="Cancel">✕</button>';
        $('#chCancelEdit').addEventListener('click', cancelEdit);
    }
    function cancelEdit() {
        if (editingId === null) return;
        editingId = null;
        $('#chInput').value = '';
        $('#chSend').textContent = 'Send';
        $('#chAttach').disabled = false; $('#chMic').disabled = false;
        $('#chPreview').style.display = 'none';
        $('#chPreview').innerHTML = '';
    }

    /* attachments */
    function setPending(file) {
        if (file.size > MAX_FILE) { chatNote('Masyadong malaki ang file. Hanggang 10 MB lang.'); return; }
        pendingFile = file;
        const p = $('#chPreview');
        p.style.display = 'flex';
        const thumb = file.type.startsWith('image/') ? '<img src="' + URL.createObjectURL(file) + '" alt="">' : '📎';
        p.innerHTML = thumb + '<span>' + esc(file.name) + ' (' + size(file.size) + ')</span><button type="button" id="chClearFile" title="Alisin">✕</button>';
        $('#chClearFile').addEventListener('click', clearPending);
    }
    function clearPending() {
        pendingFile = null;
        $('#chFile').value = '';
        if (editingId === null) { $('#chPreview').style.display = 'none'; $('#chPreview').innerHTML = ''; }
    }
    $('#chAttach').addEventListener('click', () => $('#chFile').click());
    $('#chFile').addEventListener('change', (e) => { if (e.target.files[0]) setPending(e.target.files[0]); });

    /* send */
    async function sendMsg() {
        const input = $('#chInput');
        const body = input.value.trim();
        if (curThread === null) return;

        if (editingId !== null) {
            if (!body) { return chatNote('Hindi pwedeng walang laman ang message.'); }
            try { await api(URLS.chat + '/message/' + editingId, 'PUT', { body }); cancelEdit(); lastSig = null; loadMessages(); }
            catch (e) { chatNote(e.message); }
            return;
        }

        if (!body && !pendingFile) return;
        const fd = new FormData();
        fd.append('body', body);
        if (pendingFile) fd.append('file', pendingFile);
        $('#chSend').disabled = true;
        try {
            await api(URLS.chat + '/' + curThread, 'POST', fd);
            input.value = ''; clearPending(); lastSig = null; loadMessages();
        } catch (e) { chatNote(e.message); }
        $('#chSend').disabled = false;
    }
    $('#chSend').addEventListener('click', sendMsg);
    $('#chInput').addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); sendMsg(); } });

    /* voice message */
    function fmtSec(s) { return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0'); }

    async function startRec() {
        if (curThread === null) return;
        if (!navigator.mediaDevices || !window.MediaRecorder) {
            return chatNote('Kailangan ng HTTPS para sa voice message (o hindi suportado ng browser). Gamitin ang https:// na link ng site.');
        }
        try { recStream = await navigator.mediaDevices.getUserMedia({ audio: true }); }
        catch (e) { return chatNote('Hindi ma-access ang mic. I-allow ang microphone permission sa browser.'); }

        const types = ['audio/webm;codecs=opus', 'audio/webm', 'audio/ogg;codecs=opus', 'audio/mp4'];
        const mt = types.find((t) => MediaRecorder.isTypeSupported(t)) || '';
        rec = new MediaRecorder(recStream, mt ? { mimeType: mt } : undefined);
        recChunks = [];
        rec.ondataavailable = (e) => { if (e.data && e.data.size) recChunks.push(e.data); };
        rec.start();
        recStart = Date.now();
        $('#chRecTime').textContent = '0:00';
        recTimer = setInterval(() => { $('#chRecTime').textContent = fmtSec(Math.floor((Date.now() - recStart) / 1000)); }, 500);
        $('#chForm').style.display = 'none';
        $('#chRec').style.display = 'flex';
    }

    function endRecUi() {
        clearInterval(recTimer);
        if (recStream) { recStream.getTracks().forEach((t) => t.stop()); }
        recStream = null; rec = null; recChunks = [];
        $('#chRec').style.display = 'none';
        if (curThread !== null) { $('#chForm').style.display = 'flex'; }
    }

    function cancelRec() {
        if (!rec) return;
        rec.onstop = null;
        try { rec.stop(); } catch (e) {}
        endRecUi();
    }

    function finishRec() {
        if (!rec) return;
        const r = rec;
        const thread = curThread;
        r.onstop = async () => {
            const type = r.mimeType || 'audio/webm';
            const ext = type.includes('ogg') ? 'ogg' : (type.includes('mp4') ? 'm4a' : 'webm');
            const file = new File([new Blob(recChunks, { type })], 'voice-message.' + ext, { type });
            endRecUi();
            if (file.size < 800) { return chatNote('Masyadong maikli ang recording.'); }
            if (file.size > MAX_FILE) { return chatNote('Masyadong malaki ang recording (max 10 MB).'); }
            const fd = new FormData();
            fd.append('body', '');
            fd.append('voice', '1');
            fd.append('file', file);
            try { await api(URLS.chat + '/' + thread, 'POST', fd); lastSig = null; loadMessages(); }
            catch (e) { chatNote(e.message); }
        };
        r.stop();
    }
    $('#chMic').addEventListener('click', startRec);
    $('#chRecCancel').addEventListener('click', cancelRec);
    $('#chRecSend').addEventListener('click', finishRec);

    /* open / close panel */
    function openChat(id, name) {
        chatOpen = true;
        $('#chatPanel').classList.add('open');
        if (id !== undefined && id !== null) { openThread(id, name); }
        else if (IS_STAFF) { showList(); }
        else { openThread(ME, 'HOA / Staff'); }
    }
    function closeChat() { cancelRec(); chatOpen = false; $('#chatPanel').classList.remove('open'); }

    $('#chatFab').addEventListener('click', () => { chatOpen ? closeChat() : openChat(); });
    $('#chClose').addEventListener('click', closeChat);
    $('#chBack').addEventListener('click', showList);

    refreshThreads();
    setInterval(() => { if (chatOpen && curThread !== null) { loadMessages(); } else { refreshThreads(); } }, 6000);
})();
</script>
@endsection
