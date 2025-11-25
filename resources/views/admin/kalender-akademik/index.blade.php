@extends('layouts.app')

@section('title', 'Kalender Akademik')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kalender Akademik</h1>
            <p class="text-gray-600 mt-1">Kelola event dan deadline akademik</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.kalender-akademik.create') }}" 
               class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
               style="background-color: #3B82F6 !important;">
                + Tambah Event
            </a>
            <a href="{{ route('admin.kalender-akademik.index') }}?view=calendar" 
               class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
               style="background-color: #10B981 !important;">
                Lihat Kalender
            </a>
        </div>
    </div>

    @if(request('view') === 'calendar')
        <!-- Calendar View -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div id="calendar"></div>
        </div>
    @else
        <!-- List View -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($kalenders as $kalender)
                            <tr class="hover:bg-gray-50 relative" style="border-left: 5px solid {{ $kalender->color }};">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 rounded mr-3 flex-shrink-0" style="background-color: {{ $kalender->color }};"></div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $kalender->judul }}</div>
                                            <div class="text-xs mt-1" style="display: block !important; visibility: visible !important; color: #6b7280;">
                                                @php
                                                    $deskripsi = $kalender->deskripsi ?? null;
                                                @endphp
                                                @if($deskripsi && trim($deskripsi) !== '')
                                                    {{ Str::limit($deskripsi, 50) }}
                                                @else
                                                    <span style="color: #9ca3af; font-style: italic;">Tidak ada deskripsi</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($kalender->jenis) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $kalender->tanggal_mulai->format('d/m/Y') }}
                                    @if($kalender->tanggal_selesai && $kalender->tanggal_selesai != $kalender->tanggal_mulai)
                                        - {{ $kalender->tanggal_selesai->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded border border-gray-300" style="background-color: {{ $kalender->color }};"></div>
                                        <span class="text-xs text-gray-600 font-mono">{{ strtoupper($kalender->color) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($kalender->target_role) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($kalender->is_important)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Penting
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Biasa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.kalender-akademik.edit', $kalender) }}" 
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form action="{{ route('admin.kalender-akademik.destroy', $kalender) }}" 
                                              method="POST" class="inline" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" style="cursor: pointer;">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada event kalender akademik
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $kalenders->links() }}
            </div>
        </div>
    @endif
</div>

@if(request('view') === 'calendar')
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
                events: '{{ url("/admin/kalender-akademik/get-events") }}',
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
                    
                    // Tooltip dengan keterangan
                    var tooltipContent = '<div style="padding: 8px; max-width: 250px;">';
                    tooltipContent += '<strong style="display: block; margin-bottom: 4px; font-size: 14px;">' + info.event.title + '</strong>';
                    if (info.event.extendedProps.description) {
                        tooltipContent += '<p style="margin: 0; font-size: 12px; color: #666;">' + 
                            (info.event.extendedProps.description.length > 100 ? 
                             info.event.extendedProps.description.substring(0, 100) + '...' : 
                             info.event.extendedProps.description) + '</p>';
                    }
                    tooltipContent += '<p style="margin: 4px 0 0 0; font-size: 11px; color: #999;">Klik untuk detail lengkap</p>';
                    tooltipContent += '</div>';
                    
                    // Menggunakan tippy.js atau tooltip sederhana
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
            
            // Get description from various possible locations
            var description = '';
            if (event.extendedProps && event.extendedProps.deskripsi) {
                description = event.extendedProps.deskripsi;
            } else if (event.extendedProps && event.extendedProps.description) {
                description = event.extendedProps.description;
            } else if (event.description) {
                description = event.description;
            } else {
                description = 'Tidak ada deskripsi';
            }
            
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
                            
                            <div>
                                <p class="text-sm font-medium text-gray-500">Deskripsi</p>
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">${escapeHtml(description)}</p>
                            </div>
                            
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
@endif
@endsection

