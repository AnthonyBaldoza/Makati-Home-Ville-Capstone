@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
  /* ⚠️ Palitan ng colors ng system theme ninyo (kopyahin sa main CSS) */
  :root {
    --mhv-primary: #1e5aa8;
    --mhv-primary-dark: #14407a;
    --mhv-bg: #f5f7fb;
    --mhv-card: #ffffff;
    --mhv-text: #1f2937;
    --mhv-border: #e2e8f0;

    --c-meeting:  #1e5aa8;
    --c-cleanup:  #2e9e5b;
    --c-seminar:  #e08a1e;
    --c-barangay: #8e44ad;
  }

  .ce-wrap { background: var(--mhv-bg); padding: 20px; border-radius: 12px; color: var(--mhv-text); }
  .ce-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px; }
  .ce-head h2 { margin: 0; color: var(--mhv-primary-dark); }
  .ce-card { background: var(--mhv-card); border: 1px solid var(--mhv-border); border-radius: 12px; padding: 16px; }

  .ce-legend { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 12px; font-size: 14px; }
  .ce-legend span::before { content: ""; display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 6px; vertical-align: -1px; background: var(--dot); }

  .btn-mhv { background: var(--mhv-primary); color: #fff; border: 0; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; }
  .btn-mhv:hover { background: var(--mhv-primary-dark); }
  .btn-danger { background: #c0392b; }
  .btn-light { background: #e5e9f0; color: var(--mhv-text); }

  /* Calendar colors per type */
  .fc .ev-meeting  { background: var(--c-meeting);  border-color: var(--c-meeting); }
  .fc .ev-cleanup  { background: var(--c-cleanup);  border-color: var(--c-cleanup); }
  .fc .ev-seminar  { background: var(--c-seminar);  border-color: var(--c-seminar); }
  .fc .ev-barangay { background: var(--c-barangay); border-color: var(--c-barangay); }
  .fc { --fc-button-bg-color: var(--mhv-primary); --fc-button-border-color: var(--mhv-primary);
        --fc-button-hover-bg-color: var(--mhv-primary-dark); --fc-button-active-bg-color: var(--mhv-primary-dark);
        --fc-today-bg-color: #eaf1fb; }

  /* Modal */
  .ce-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 9999; align-items: center; justify-content: center; padding: 16px; }
  .ce-modal.open { display: flex; }
  .ce-box { background: #fff; border-radius: 12px; padding: 22px; width: 100%; max-width: 460px; max-height: 90vh; overflow-y: auto; }
  .ce-box h3 { margin-top: 0; color: var(--mhv-primary-dark); }
  .ce-box label { display: block; font-size: 13px; margin: 10px 0 4px; font-weight: 600; }
  .ce-box input, .ce-box select, .ce-box textarea { width: 100%; padding: 8px; border: 1px solid var(--mhv-border); border-radius: 8px; font-size: 14px; box-sizing: border-box; }
  .ce-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 16px; }
  .ce-error { color: #c0392b; font-size: 13px; margin-top: 8px; white-space: pre-line; }
</style>

<div class="ce-wrap">
  <div class="ce-head">
    <h2>📅 Community Events Calendar</h2>
    @if($canManage)
      <button class="btn-mhv" id="btnAdd">+ Add Event</button>
    @endif
  </div>

  <div class="ce-legend">
    <span style="--dot:var(--c-meeting)">Community Meeting</span>
    <span style="--dot:var(--c-cleanup)">Clean-up Drive</span>
    <span style="--dot:var(--c-seminar)">Seminar</span>
    <span style="--dot:var(--c-barangay)">Barangay Activity</span>
  </div>

  <div class="ce-card"><div id="calendar"></div></div>
</div>

{{-- Modal: Add/Edit (staff) o Details (resident) --}}
<div class="ce-modal" id="evModal">
  <div class="ce-box">
    <h3 id="modalTitle">Event Details</h3>
    <input type="hidden" id="evId">

    <label>Title</label>
    <input id="evTitle">

    <label>Type</label>
    <select id="evType">
      <option value="meeting">Community Meeting</option>
      <option value="cleanup">Clean-up Drive</option>
      <option value="seminar">Seminar</option>
      <option value="barangay">Barangay Activity</option>
    </select>

    <label>Start</label>
    <input type="datetime-local" id="evStart">

    <label>End (optional)</label>
    <input type="datetime-local" id="evEnd">

    <label>Location</label>
    <input id="evLocation">

    <label>Description</label>
    <textarea id="evDesc" rows="3"></textarea>

    <div class="ce-error" id="evError"></div>

    <div class="ce-actions">
      @if($canManage)
        <button class="btn-mhv btn-danger" id="btnDelete" style="display:none">Delete</button>
        <button class="btn-mhv" id="btnSave">Save</button>
      @endif
      <button class="btn-mhv btn-light" id="btnClose">Close</button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
const CAN_MANAGE = @json($canManage);
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const $ = id => document.getElementById(id);
const fields = ['evTitle','evType','evStart','evEnd','evLocation','evDesc'];

function toInput(d) { // Date -> "YYYY-MM-DDTHH:mm"
  if (!d) return '';
  const p = n => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${p(d.getMonth()+1)}-${p(d.getDate())}T${p(d.getHours())}:${p(d.getMinutes())}`;
}

function openModal({ id = '', title = '', type = 'meeting', start = '', end = '', location = '', desc = '' }) {
  $('evId').value = id; $('evTitle').value = title; $('evType').value = type;
  $('evStart').value = start; $('evEnd').value = end;
  $('evLocation').value = location; $('evDesc').value = desc;
  $('evError').textContent = '';
  $('modalTitle').textContent = CAN_MANAGE ? (id ? 'Edit Event' : 'Add Event') : 'Event Details';
  fields.forEach(f => $(f).disabled = !CAN_MANAGE);
  if (CAN_MANAGE) $('btnDelete').style.display = id ? 'inline-block' : 'none';
  $('evModal').classList.add('open');
}
const closeModal = () => $('evModal').classList.remove('open');

async function send(url, method, body) {
  const res = await fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: body ? JSON.stringify(body) : undefined,
  });
  if (!res.ok) {
    const data = await res.json().catch(() => ({}));
    throw new Error(data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'May error. Subukan ulit.'));
  }
  return res;
}

const calendar = new FullCalendar.Calendar($('calendar'), {
  initialView: window.innerWidth < 700 ? 'listMonth' : 'dayGridMonth',
  height: 'auto',
  headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
  buttonText: { today: 'Today', month: 'Calendar', list: 'List' },
  events: '{{ route("community-events.feed") }}',
  eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
  noEventsText: 'Wala pang naka-schedule na activity.',

  eventClick(info) {
    const e = info.event, p = e.extendedProps;
    openModal({
      id: e.id, title: e.title, type: p.type,
      start: toInput(e.start), end: toInput(e.end),
      location: p.location || '', desc: p.description || '',
    });
  },
  dateClick(info) {
    if (!CAN_MANAGE) return;
    openModal({ start: info.dateStr.slice(0, 10) + 'T09:00' });
  },
});
calendar.render();

$('btnClose').onclick = closeModal;
if (CAN_MANAGE) {
  $('btnAdd').onclick = () => openModal({});

  $('btnSave').onclick = async () => {
    const id = $('evId').value;
    const body = {
      title: $('evTitle').value, type: $('evType').value,
      start_at: $('evStart').value, end_at: $('evEnd').value || null,
      location: $('evLocation').value, description: $('evDesc').value,
    };
    try {
      await send(id ? `/community-events/${id}` : '/community-events', id ? 'PUT' : 'POST', body);
      closeModal(); calendar.refetchEvents();
    } catch (err) { $('evError').textContent = err.message; }
  };

  $('btnDelete').onclick = async () => {
    if (!confirm('I-delete ang event na ito?')) return;
    try {
      await send(`/community-events/${$('evId').value}`, 'DELETE');
      closeModal(); calendar.refetchEvents();
    } catch (err) { $('evError').textContent = err.message; }
  };
}
</script>
@endsection
