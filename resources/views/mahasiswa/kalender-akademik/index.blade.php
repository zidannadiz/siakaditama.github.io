@extends('layouts.app')

@section('title', 'Kalender Akademik')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Kalender Akademik</h1>
        <p class="text-gray-600 mt-1">Lihat jadwal dan deadline akademik</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div id="calendar"></div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/id.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            displayEventTime: false, // Nonaktifkan tampilan waktu di judul
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '{{ route("mahasiswa.kalender-akademik.get-events") }}',
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                showEventModal(info.event);
            },
            eventDidMount: function(info) {
                // Styling untuk event penting
                if (info.event.extendedProps.is_important) {
                    info.el.style.borderWidth = '3px';
                    info.el.style.fontWeight = 'bold';
                    info.el.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
                }
                
                // Tooltip
                info.el.setAttribute('title', info.event.title + (info.event.extendedProps.description ? '\n' + info.event.extendedProps.description : ''));
                info.el.style.cursor = 'pointer';
            }
        });
        calendar.render();
    });
    
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
    }
    
    function showEventModal(event) {
        var jenisLabels = {
            'semester': 'Semester',
            'krs': 'KRS',
            'pembayaran': 'Pembayaran',
            'ujian': 'Ujian',
            'libur': 'Libur',
            'kegiatan': 'Kegiatan',
            'pengumuman': 'Pengumuman',
            'lainnya': 'Lainnya'
        };
        
        var startDate = new Date(event.start);
        var endDate = event.end ? new Date(event.end) : null;
        
        var modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        modal.style.display = 'flex';
        modal.innerHTML = `
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto" style="z-index: 60;">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded" style="background-color: ${event.backgroundColor};"></div>
                            <h3 class="text-xl font-bold text-gray-900">${event.title}</h3>
                        </div>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Jenis Event</p>
                            <p class="text-sm text-gray-900">${jenisLabels[event.extendedProps.jenis] || 'Lainnya'}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tanggal</p>
                            <p class="text-sm text-gray-900">
                                ${startDate.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                                ${startDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) !== '00:00' ? 
                                  ' - ' + startDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : ''}
                                ${endDate && endDate.toDateString() !== startDate.toDateString() ? 
                                  ' s/d ' + endDate.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : ''}
                            </p>
                        </div>
                        
                        ${(event.extendedProps.description || event.description) ? `
                        <div>
                            <p class="text-sm font-medium text-gray-500">Deskripsi</p>
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">${escapeHtml(event.extendedProps.description || event.description || 'Tidak ada deskripsi')}</p>
                        </div>
                        ` : ''}
                        
                        ${event.extendedProps.is_important ? `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-sm font-semibold text-red-800">⚠️ Event Penting</p>
                            <p class="text-xs text-red-600 mt-1">Event ini merupakan deadline atau acara penting</p>
                        </div>
                        ` : ''}
                        
                        ${event.url ? `
                        <div>
                            <a href="${event.url}" target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Buka Link Terkait
                            </a>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
</script>
@endpush
@endsection

