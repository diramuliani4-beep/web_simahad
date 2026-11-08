@auth
    @php
        $daftarNotifikasi = auth()->user()->notifikasis()->latest('id')->limit(6)->get();
        $jumlahNotifikasiBelumDibaca = auth()->user()->notifikasis()->belumDibaca()->count();
    @endphp

    <div class="notif-wrap">
        <button
            type="button"
            class="notif-trigger"
            id="notifTrigger"
            aria-label="Notifikasi"
            aria-expanded="false"
            title="Notifikasi"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>
            </svg>

            @if ($jumlahNotifikasiBelumDibaca > 0)
                <span class="notif-badge">{{ $jumlahNotifikasiBelumDibaca > 9 ? '9+' : $jumlahNotifikasiBelumDibaca }}</span>
            @endif
        </button>

        <div class="notif-panel" id="notifPanel">
            <div class="notif-header">
                <span>Notifikasi</span>

                @if ($jumlahNotifikasiBelumDibaca > 0)
                    <form method="POST" action="{{ route('notifikasi.bacaSemua') }}">
                        @csrf
                        <button type="submit" class="notif-mark-all">Tandai semua</button>
                    </form>
                @endif
            </div>

            <div class="notif-list">
                @forelse ($daftarNotifikasi as $notifikasi)
                    <form method="POST" action="{{ route('notifikasi.baca', $notifikasi) }}">
                        @csrf
                        <button type="submit" class="notif-item {{ $notifikasi->sudahDibaca() ? '' : 'belum-dibaca' }}">
                            <span class="notif-icon">{{ $notifikasi->ikon() }}</span>
                            <span class="notif-text">
                                <span class="notif-title">{{ $notifikasi->judul }}</span>
                                @if (filled($notifikasi->pesan))
                                    <span class="notif-message">{{ \Illuminate\Support\Str::limit($notifikasi->pesan, 90) }}</span>
                                @endif
                                <span class="notif-time">
                                    {{ $notifikasi->created_at?->locale('id')->diffForHumans() }}
                                </span>
                            </span>
                        </button>
                    </form>
                @empty
                    <div class="notif-empty">Belum ada notifikasi.</div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .notif-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .notif-trigger {
            position: relative;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            padding: 0;
            border: 2px solid var(--maroon, #800d22);
            border-radius: 50%;
            background: #fff;
            color: var(--maroon, #800d22);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .notif-trigger svg {
            width: 21px;
            height: 21px;
        }

        .notif-trigger:hover {
            background: var(--maroon, #800d22);
            color: #fff;
        }

        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            background: #b91c1c;
            color: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: 700;
            line-height: 18px;
            text-align: center;
        }

        .notif-panel {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 1300;
            width: 340px;
            max-height: 420px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #e5d5ce;
            border-radius: 12px;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.16);
            text-align: left;
        }

        .notif-panel.aktif {
            display: block;
        }

        .notif-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px;
            border-bottom: 1px solid #f2e9e5;
            font-family: 'Poppins', 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            color: #6b0d1b;
        }

        .notif-mark-all {
            border: none;
            background: transparent;
            color: #6b0d1b;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
        }

        .notif-item {
            display: flex;
            gap: 10px;
            width: 100%;
            padding: 11px 14px;
            border: none;
            border-bottom: 1px solid #f7f1ee;
            background: #fff;
            text-align: left;
            cursor: pointer;
            font-family: 'Poppins', 'Plus Jakarta Sans', Arial, sans-serif;
        }

        .notif-item:hover {
            background: #fdf7f5;
        }

        .notif-item.belum-dibaca {
            background: #fdf2f4;
        }

        .notif-icon {
            font-size: 16px;
            line-height: 1.3;
        }

        .notif-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .notif-title {
            font-size: 12.8px;
            font-weight: 700;
            color: #1f1f1f;
        }

        .notif-message {
            font-size: 12px;
            color: #5b5b5b;
            line-height: 1.45;
        }

        .notif-time {
            font-size: 11px;
            color: #8a8a8a;
        }

        .notif-empty {
            padding: 22px 14px;
            text-align: center;
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 12.5px;
            color: #7a7a7a;
        }

        @media (max-width: 480px) {
            .notif-panel {
                width: min(88vw, 340px);
                right: -56px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trigger = document.getElementById('notifTrigger');
            const panel = document.getElementById('notifPanel');

            if (!trigger || !panel) return;

            trigger.addEventListener('click', function (event) {
                event.stopPropagation();
                const aktif = panel.classList.toggle('aktif');
                trigger.setAttribute('aria-expanded', String(aktif));
            });

            panel.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            document.addEventListener('click', function () {
                panel.classList.remove('aktif');
                trigger.setAttribute('aria-expanded', 'false');
            });
        });
    </script>
@endauth
