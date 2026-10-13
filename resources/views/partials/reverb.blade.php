@php
    $reverb = [
        'key' => config('broadcasting.connections.reverb.key'),
        'host' => config('broadcasting.connections.reverb.options.host') ?? '127.0.0.1',
        'port' => (int) config('broadcasting.connections.reverb.options.port', 8080),
        'scheme' => config('broadcasting.connections.reverb.options.scheme') ?? 'http',
        'userId' => auth()->id(),
    ];
@endphp

@if ($reverb['key'])
    <script>
        window.JiwantaReverb = @json($reverb);
    </script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        (() => {
            const cfg = window.JiwantaReverb || {};

            const applyStatus = (state) => {
                window.dispatchEvent(new CustomEvent('jiwanta:status', { detail: { state } }));

                const map = {
                    connected: ['Realtime aktif', 'bg-emerald-500'],
                    disconnected: ['Terputus', 'bg-amber-500'],
                    unavailable: ['Realtime nonaktif', 'bg-slate-300'],
                    failed: ['Realtime nonaktif', 'bg-slate-300'],
                };
                const [text, dot] = map[state] || map.unavailable;

                document.querySelectorAll('[data-ws-text]').forEach((el) => (el.textContent = text));
                document.querySelectorAll('[data-ws-dot]').forEach((el) => (el.className = 'relative h-2 w-2 rounded-full ' + dot));
            };

            if (typeof Pusher === 'undefined' || ! cfg.key) {
                applyStatus('unavailable');
                return;
            }

            const pusher = new Pusher(cfg.key, {
                cluster: 'mt1',
                wsHost: cfg.host,
                wsPort: cfg.port,
                wssPort: cfg.port,
                forceTLS: cfg.scheme === 'https',
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
            });

            const statusHandlers = [];
            ['connected', 'disconnected', 'unavailable', 'failed'].forEach((state) => {
                pusher.connection.bind(state, () => {
                    window.JiwantaRealtime.status = state;
                    statusHandlers.forEach((fn) => fn(state));
                    applyStatus(state);
                });
            });

            window.JiwantaRealtime = {
                pusher,
                status: null,
                userId: cfg.userId,
                userChannel: cfg.userId ? `user.${cfg.userId}` : null,
                onStatus: (fn) => statusHandlers.push(fn),
                subscribe: (channel, event, cb) => pusher.subscribe(channel).bind(event, cb),
                onData: (fn) => window.addEventListener('jiwanta:realtime', (e) => fn(e.detail)),
                refresh: async (url, cb) => {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const json = await res.json();
                    if (cb) cb(json && json.data ? json.data : json);
                    return json;
                },
            };

            const emit = (type, data) => window.dispatchEvent(new CustomEvent('jiwanta:realtime', { detail: { type, data } }));
            pusher.subscribe('admin.orders').bind('order.placed', (data) => emit('order.placed', data));
            if (cfg.userId) {
                pusher.subscribe(`user.${cfg.userId}`).bind('order.status', (data) => emit('order.status', data));
            }

            const ready = (fn) => document.readyState === 'loading'
                ? document.addEventListener('DOMContentLoaded', fn)
                : fn();

            ready(() => {
                const root = document.querySelector('[data-notif-root]');

                if (! root) {
                    return;
                }

                const panel = root.querySelector('[data-notif-panel]');
                const list = root.querySelector('[data-notif-list]');
                const empty = root.querySelector('[data-notif-empty]');
                const count = root.querySelector('[data-notif-count]');
                const statusDot = root.querySelector('[data-notif-status-dot]');
                const statusText = root.querySelector('[data-notif-status-text]');
                const bell = root.querySelector('[data-bell]');

                let unread = 0;

                bell.addEventListener('click', (e) => {
                    e.stopPropagation();
                    panel.classList.toggle('hidden');
                });
                document.addEventListener('click', (e) => {
                    if (! root.contains(e.target)) panel.classList.add('hidden');
                });

                const setStatus = (text, dotClass) => {
                    statusText.textContent = text;
                    statusDot.className = 'h-1.5 w-1.5 rounded-full ' + dotClass;
                };

                const el = (tag, className, text) => {
                    const node = document.createElement(tag);
                    node.className = className;
                    if (text !== undefined) node.textContent = text;
                    return node;
                };

                const pushNotif = (data) => {
                    unread += 1;
                    count.textContent = unread > 9 ? '9+' : String(unread);
                    count.classList.remove('hidden');
                    count.classList.add('flex');
                    empty.classList.add('hidden');

                    const item = el('div', 'px-4 py-3 hover:bg-slate-50');
                    const header = el('div', 'flex items-start justify-between gap-2');
                    header.append(
                        el('p', 'text-xs font-semibold text-slate-800', data.judul || 'Pesanan Baru'),
                        el('span', 'shrink-0 text-[10px] text-slate-400', data.waktu || ''),
                    );
                    item.append(
                        header,
                        el('p', 'mt-0.5 text-[11px] text-slate-500', `${data.pemesan || 'Pengunjung'} \u2022 ${data.tamu || ''}`),
                        el('p', 'mt-1 text-[11px] font-bold text-emerald-700', data.total_label || ''),
                        el('p', 'text-[10px] text-slate-400', data.kode || ''),
                    );
                    list.prepend(item);
                };

                pusher.connection.bind('connected', () => setStatus('Realtime aktif', 'bg-emerald-500'));
                pusher.connection.bind('disconnected', () => setStatus('Terputus', 'bg-amber-500'));
                pusher.connection.bind('unavailable', () => setStatus('Realtime nonaktif', 'bg-slate-300'));
                pusher.connection.bind('failed', () => setStatus('Realtime nonaktif', 'bg-slate-300'));

                pusher.subscribe('admin.orders').bind('order.placed', pushNotif);
            });
        })();
    </script>
@endif
