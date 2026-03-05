@extends('layouts.hr')
@section('content')

    <div class="notif-wrapper">

        <div class="notif-header">
            <div class="notif-title-row">
                <div>
                    <h1 class="notif-title">Notifications</h1>
                    <p class="notif-subtitle">Stay updated on visitor check-ins</p>
                </div>
                @if($unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-mark-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>

            {{-- Tabs --}}
            <div class="notif-tabs">
                <button class="tab-btn active" data-tab="all">
                    All
                    <span class="tab-badge">{{ $allNotifications->count() }}</span>
                </button>
                <button class="tab-btn" data-tab="unread">
                    Unread
                    <span class="tab-badge unread">{{ $unreadNotifications->count() }}</span>
                </button>
                <button class="tab-btn" data-tab="read">
                    Read
                    <span class="tab-badge">{{ $readNotifications->count() }}</span>
                </button>
            </div>
        </div>

        {{-- ALL --}}
        <div class="tab-panel active" id="tab-all">
            @forelse($allNotifications as $notification)
                <div class="notif-card {{ is_null($notification->read_at) ? 'icon-unread' : 'icon-read' }}">
                        <a href="{{ route('notifications.readAndRedirect', $notification->id) }}" 
                            class="notif-icon {{ is_null($notification->read_at) ? 'icon-unread' : 'icon-read' }}" 
                            title="View visitor">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </a>
                    <div class="notif-body">
                        <p class="notif-message">{{ $notification->data['message'] }}</p>
                        <div class="notif-meta">
                            <span class="notif-visitor">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $notification->data['visitor_name'] ?? 'Unknown Visitor' }}
                            </span>
                            <span class="notif-time">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div class="notif-actions">
                        @if(is_null($notification->read_at))
                            <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-read" title="Mark as read">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </button>
                            </form>
                          @else
                            <span class="read-badge">Read</span>
                        @endif
                    </div>
                </div>
              @empty
                @include('partials._empty_notif', ['label' => 'No notifications yet'])
            @endforelse
        </div>

        {{-- UNREAD --}}
        <div class="tab-panel" id="tab-unread">
            @forelse($unreadNotifications as $notification)
                <div class="notif-card unread">
                        <a href="{{ route('notifications.readAndRedirect', $notification->id) }}" 
                            class="notif-icon {{ is_null($notification->read_at) ? 'icon-unread' : 'icon-read' }}" 
                            title="View visitor">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </a>
                    <div class="notif-body">
                        <p class="notif-message">{{ $notification->data['message'] }}</p>
                        <div class="notif-meta">
                            <span class="notif-visitor">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $notification->data['visitor_name'] ?? 'Unknown Visitor' }}
                            </span>
                            <span class="notif-time">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div class="notif-actions">
                        <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-read" title="Mark as read">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
             @empty
                @include('partials._empty_notif', ['label' => 'No unread notifications'])
            @endforelse
        </div>

        {{-- READ --}}
        <div class="tab-panel" id="tab-read">
            @forelse($readNotifications as $notification)
                <div class="notif-card">
                        <a href="{{ route('visitors.show', encrypt($notification->data['visitor_id'])) }}" 
                            class="notif-icon {{ is_null($notification->read_at) ? 'icon-read' : 'icon-unread' }}" 
                            title="View visitor">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </a>
                    <div class="notif-body">
                        <p class="notif-message">{{ $notification->data['message'] }}</p>
                        <div class="notif-meta">
                            <span class="notif-visitor">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $notification->data['visitor_name'] ?? 'Unknown Visitor' }}
                            </span>
                            <span class="notif-time">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                            @if($notification->read_at)
                                <span class="notif-read-time">
                                    · Read {{ $notification->read_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="notif-actions">
                        <span class="read-badge">Read</span>
                    </div>
                </div>
            @empty
                @include('partials._empty_notif', ['label' => 'No read notifications'])
            @endforelse
        </div>

    </div>

    {{-- Empty state partial inline fallback --}}
    @once
        @push('scripts')
        @endpush
    @endonce

    <style>
            /* WRAPPER   */
            .notif-wrapper {
                max-width: 760px;
                margin: 2rem auto;
                padding: 0 1rem;
                font-family: 'Segoe UI', system-ui, sans-serif;
            }

            /* HEADER */
            .notif-header {
                margin-bottom: 1.5rem;
            }
            .notif-title-row {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 1rem;
                margin-bottom: 1.25rem;
            }
            .notif-title {
                font-size: 1.6rem;
                font-weight: 700;
                color: #1a1a2e;
                margin: 0 0 .2rem;
            }
            .notif-subtitle {
                font-size: .875rem;
                color: #6b7280;
                margin: 0;
            }

            /* MARK ALL BUTTON */
            .btn-mark-all {
                display: inline-flex;
                align-items: center;
                gap: .4rem;
                background: #1a1a2e;
                color: #fff;
                border: none;
                padding: .55rem 1.1rem;
                border-radius: 8px;
                font-size: .85rem;
                font-weight: 500;
                cursor: pointer;
                transition: background .2s, transform .15s;
                white-space: nowrap;
            }
            .btn-mark-all:hover {
                background: #16213e;
                transform: translateY(-1px);
            }

            /* TABS */
            .notif-tabs {
                display: flex;
                gap: .5rem;
                border-bottom: 2px solid #e5e7eb;
                padding-bottom: 0;
            }
            .tab-btn {
                display: inline-flex;
                align-items: center;
                gap: .4rem;
                background: none;
                border: none;
                padding: .6rem 1rem;
                font-size: .9rem;
                font-weight: 500;
                color: #6b7280;
                cursor: pointer;
                border-bottom: 2px solid transparent;
                margin-bottom: -2px;
                transition: color .2s, border-color .2s;
                border-radius: 0;
            }
            .tab-btn:hover { color: #1a1a2e; }
            .tab-btn.active {
                color: #1a1a2e;
                border-bottom-color: #1a1a2e;
            }
            .tab-badge {
                background: #e5e7eb;
                color: #374151;
                font-size: .72rem;
                font-weight: 600;
                padding: .15rem .45rem;
                border-radius: 20px;
                min-width: 20px;
                text-align: center;
            }
            .tab-badge.unread {
                background: #fee2e2;
                color: #dc2626;
            }

            /* TAB PANELS */
            .tab-panel { display: none; padding-top: 1rem; }
            .tab-panel.active { display: block; }

            /* NOTIFICATION CARD */
            .notif-card {
                display: flex;
                align-items: flex-start;
                gap: 1rem;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 1rem 1.2rem;
                margin-bottom: .75rem;
                transition: box-shadow .2s, border-color .2s;
                position: relative;
            }
            .notif-card:hover {
                box-shadow: 0 4px 16px rgba(0,0,0,.07);
                border-color: #d1d5db;
            }
            .notif-card.unread {
                border-left: 4px solid #3b82f6;
                background: #f0f6ff;
            }

            /* ICON */
            .notif-icon {
                flex-shrink: 0;
                width: 42px;
                height: 42px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .icon-unread { background: #dbeafe; color: #2563eb; }
            .icon-read   { background: #f3f4f6; color: #9ca3af; }

            /* BODY */
            .notif-body { flex: 1; min-width: 0; }
            .notif-message {
                margin: 0 0 .4rem;
                font-size: .93rem;
                color: #111827;
                font-weight: 500;
                line-height: 1.45;
            }
            .notif-card:not(.unread) .notif-message {
                font-weight: 400;
                color: #374151;
            }
            .notif-meta {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: .6rem;
                font-size: .78rem;
                color: #6b7280;
            }
            .notif-visitor,
            .notif-time,
            .notif-read-time {
                display: inline-flex;
                align-items: center;
                gap: .25rem;
            }
            .notif-read-time { color: #9ca3af; }

            /* ACTIONS */
            .notif-actions { flex-shrink: 0; display: flex; align-items: center; }
            .btn-read {
                background: #dbeafe;
                border: none;
                color: #2563eb;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background .2s, transform .15s;
            }
            .btn-read:hover {
                background: #bfdbfe;
                transform: scale(1.1);
            }
            .read-badge {
                font-size: .75rem;
                font-weight: 600;
                color: #6b7280;
                background: #f3f4f6;
                padding: .25rem .6rem;
                border-radius: 20px;
            }

            /* EMPTY STATE */
            .notif-empty {
                text-align: center;
                padding: 3rem 1rem;
                color: #9ca3af;
            }
            .notif-empty svg { margin-bottom: .75rem; opacity: .4; }
            .notif-empty p { margin: 0; font-size: .95rem; }

            @media (max-width: 520px) {
                .notif-title-row { flex-direction: column; }
                .notif-card { padding: .85rem 1rem; }
            }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs  = document.querySelectorAll('.tab-btn');
            const panels = document.querySelectorAll('.tab-panel');

            tabs.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));

                    btn.classList.add('active');
                    document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
                });
            });
        });


        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

    </script>

@endsection