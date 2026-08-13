{{-- Soft 3D illustrative KPI icons (duotone / glass style) --}}

@if ($type === 'people-purple')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="22" cy="20" r="9" fill="#C4B5FD" opacity="0.55"/>
    <circle cx="22" cy="19" r="7" fill="#A78BFA"/>
    <path d="M10 48c0-7 5.5-12 12-12s12 5 12 12" fill="#8B5CF6" opacity="0.35"/>
    <path d="M12 48c0-5.5 4.5-10 10-10s10 4.5 10 10" fill="#7C3AED"/>
    <circle cx="42" cy="22" r="8" fill="#DDD6FE" opacity="0.6"/>
    <circle cx="42" cy="21" r="6.5" fill="#A78BFA" opacity="0.85"/>
    <path d="M32 48c0-6 4.8-10.5 10-10.5S52 42 52 48" fill="#8B5CF6" opacity="0.28"/>
    <path d="M34 48c0-4.8 3.8-8.5 8-8.5s8 3.7 8 8.5" fill="#9333EA"/>
</svg>
@elseif ($type === 'people-blue')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="22" cy="20" r="9" fill="#BFDBFE" opacity="0.55"/>
    <circle cx="22" cy="19" r="7" fill="#60A5FA"/>
    <path d="M10 48c0-7 5.5-12 12-12s12 5 12 12" fill="#3B82F6" opacity="0.35"/>
    <path d="M12 48c0-5.5 4.5-10 10-10s10 4.5 10 10" fill="#2563EB"/>
    <circle cx="42" cy="22" r="8" fill="#DBEAFE" opacity="0.6"/>
    <circle cx="42" cy="21" r="6.5" fill="#60A5FA" opacity="0.85"/>
    <path d="M32 48c0-6 4.8-10.5 10-10.5S52 42 52 48" fill="#3B82F6" opacity="0.28"/>
    <path d="M34 48c0-4.8 3.8-8.5 8-8.5s8 3.7 8 8.5" fill="#1D4ED8"/>
</svg>
@elseif ($type === 'calendar-orange')
@php $calGradId = 'kpiCal' . uniqid(); @endphp
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
        <linearGradient id="{{ $calGradId }}" x1="10" y1="16" x2="54" y2="54" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FED7AA"/>
            <stop offset="1" stop-color="#F97316"/>
        </linearGradient>
    </defs>
    <rect x="10" y="16" width="44" height="38" rx="10" fill="#FDBA74" opacity="0.45"/>
    <rect x="10" y="16" width="44" height="38" rx="10" fill="url(#{{ $calGradId }})" />
    <rect x="10" y="16" width="44" height="13" rx="10" fill="#EA580C"/>
    <rect x="10" y="24" width="44" height="5" fill="#C2410C"/>
    <rect x="18" y="10" width="7" height="14" rx="3.5" fill="#FB923C"/>
    <rect x="39" y="10" width="7" height="14" rx="3.5" fill="#FB923C"/>
    <circle cx="22" cy="38" r="3" fill="#FFF7ED" opacity="0.5"/>
    <circle cx="32" cy="38" r="3" fill="#FFF7ED" opacity="0.5"/>
    <circle cx="42" cy="38" r="3" fill="#FFF7ED" opacity="0.5"/>
    <path d="M36 44l4 4 8-9" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
@elseif ($type === 'coins-green')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    {{-- Back stack --}}
    <ellipse cx="40" cy="48" rx="13" ry="4.5" fill="#047857" opacity="0.35"/>
    <ellipse cx="40" cy="42" rx="13" ry="4.5" fill="#059669" opacity="0.5"/>
    <ellipse cx="40" cy="36" rx="13" ry="4.5" fill="#10B981" opacity="0.65"/>
    <ellipse cx="40" cy="30" rx="13" ry="4.5" fill="#34D399" opacity="0.8"/>
    <ellipse cx="40" cy="24" rx="13" ry="4.5" fill="#6EE7B7"/>
    {{-- Front stack --}}
    <ellipse cx="22" cy="50" rx="11" ry="4" fill="#047857" opacity="0.4"/>
    <ellipse cx="22" cy="44" rx="11" ry="4" fill="#059669" opacity="0.55"/>
    <ellipse cx="22" cy="38" rx="11" ry="4" fill="#10B981" opacity="0.7"/>
    <ellipse cx="22" cy="32" rx="11" ry="4" fill="#34D399" opacity="0.85"/>
    <ellipse cx="22" cy="26" rx="11" ry="4" fill="#A7F3D0"/>
    <ellipse cx="22" cy="24" rx="11" ry="3.5" fill="#D1FAE5"/>
</svg>
@elseif ($type === 'rupee-green')
<svg class="kpi-3d-icon kpi-3d-icon--rupee {{ $size ?? 'kpi-3d-icon--sm' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <text x="32" y="44" text-anchor="middle" fill="#FFFFFF" font-size="34" font-weight="700" font-family="Inter, sans-serif">₹</text>
</svg>
@elseif ($type === 'check-green')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="32" cy="32" r="24" fill="#A7F3D0" opacity="0.45"/>
    <circle cx="32" cy="30" r="20" fill="#34D399"/>
    <circle cx="32" cy="30" r="16" fill="#10B981"/>
    <path d="M22 30l8 8 16-18" stroke="#FFFFFF" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
@elseif ($type === 'pause-red')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="32" cy="32" r="24" fill="#FECACA" opacity="0.45"/>
    <circle cx="32" cy="30" r="20" fill="#F87171"/>
    <circle cx="32" cy="30" r="16" fill="#EF4444"/>
    <rect x="24" y="22" width="6" height="20" rx="2" fill="#FFFFFF"/>
    <rect x="34" y="22" width="6" height="20" rx="2" fill="#FFFFFF"/>
</svg>
@elseif ($type === 'box-blue')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M12 22l20-10 20 10v28l-20 10L12 50V22z" fill="#BFDBFE" opacity="0.5"/>
    <path d="M12 22l20-10 20 10-20 10L12 22z" fill="#60A5FA"/>
    <path d="M32 32v28" stroke="#2563EB" stroke-width="3"/>
    <path d="M12 22l20 10 20-10" stroke="#1D4ED8" stroke-width="2"/>
    <path d="M52 22v28l-20 10V32" fill="#3B82F6" opacity="0.75"/>
</svg>
@elseif ($type === 'scissors-violet')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="18" cy="18" r="8" fill="#C4B5FD"/>
    <circle cx="18" cy="46" r="8" fill="#A78BFA"/>
    <path d="M24 22l28 20" stroke="#7C3AED" stroke-width="4" stroke-linecap="round"/>
    <path d="M24 42l28-20" stroke="#8B5CF6" stroke-width="4" stroke-linecap="round"/>
    <circle cx="46" cy="32" r="6" fill="#DDD6FE"/>
</svg>
@elseif ($type === 'clipboard-cyan')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <rect x="14" y="16" width="36" height="42" rx="8" fill="#BAE6FD" opacity="0.5"/>
    <rect x="14" y="16" width="36" height="42" rx="8" fill="#38BDF8"/>
    <rect x="22" y="10" width="20" height="12" rx="4" fill="#0EA5E9"/>
    <path d="M22 34h20M22 42h14" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
    <path d="M26 28l4 4 8-8" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
@elseif ($type === 'shield-green')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M32 8L14 16v16c0 12.5 7.8 24.2 18 28 10.2-3.8 18-15.5 18-28V16L32 8z" fill="#FFFFFF" opacity="0.35"/>
    <path d="M32 12L18 18v14c0 10.2 6.2 19.8 14 23 7.8-3.2 14-12.8 14-23V18L32 12z" fill="#FFFFFF"/>
    <path d="M24 32l6 6 12-14" stroke="#22C55E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
@elseif ($type === 'clock-orange')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="28" cy="24" r="10" fill="#FFFFFF" opacity="0.9"/>
    <path d="M18 48c0-8 4.5-14 10-14s10 6 10 14" fill="#FFFFFF" opacity="0.85"/>
    <circle cx="44" cy="36" r="14" fill="#FFFFFF" opacity="0.35"/>
    <circle cx="44" cy="36" r="11" fill="#FFFFFF"/>
    <path d="M44 30v7l5 3" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
@elseif ($type === 'user-x-red')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle cx="28" cy="22" r="10" fill="#FFFFFF" opacity="0.9"/>
    <path d="M12 50c0-9 7-16 16-16s16 7 16 16" fill="#FFFFFF" opacity="0.85"/>
    <circle cx="46" cy="46" r="12" fill="#FFFFFF" opacity="0.35"/>
    <circle cx="46" cy="46" r="9" fill="#FFFFFF"/>
    <path d="M42 42l8 8M50 42l-8 8" stroke="#EF4444" stroke-width="3" stroke-linecap="round"/>
</svg>
@elseif ($type === 'heart-pink')
<svg class="kpi-3d-icon {{ $size ?? 'kpi-3d-icon--lg' }}" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M32 52s-18-11-18-24c0-7 5.5-12 12-12 4 0 7.5 2 9 5 1.5-3 5-5 9-5 6.5 0 12 5 12 12 0 13-18 24-18 24z" fill="#FBCFE8" opacity="0.55"/>
    <path d="M32 48s-14-9-14-20c0-5.5 4-9.5 9-9.5 3 0 5.5 1.5 7 4 1.5-2.5 4-4 7-4 5 0 9 4 9 9.5 0 11-14 20-14 20z" fill="#F472B6"/>
    <path d="M32 44s-10-7-10-16c0-4 3-7 7-7 2.2 0 4 1.2 5 3 1-1.8 2.8-3 5-3 4 0 7 3 7 7 0 9-10 16-10 16z" fill="#EC4899"/>
</svg>
@endif
