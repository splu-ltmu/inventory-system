@extends('layouts.admin')

@section('content')
    @php
        $pageTitle = 'Member Settings Monitoring';
    @endphp

    <style>
        .monitoring-container {
            margin: 20px 0;
        }

        .client-section {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .client-header {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .client-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }

        .settings-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 18px;
            margin: 16px;
            margin-bottom: 18px;
            box-shadow: 0 10px 25px rgba(15,23,42,.08);
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }

        .settings-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(15,23,42,.12);
        }

        .card-header {
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            background: rgba(139, 92, 246, 0.08);
            border-bottom: 1px solid var(--line);
        }

        .card-header h3 {
            margin: 0;
            color: var(--text);
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .members-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .members-table th {
            background: #f8fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
            color: #374151;
        }

        .members-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .members-table tr:hover {
            background: #f8fafc;
        }

        .member-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .member-active {
            background: #ecfdf5;
            color: #059669;
        }

        .member-inactive {
            background: #fef2f2;
            color: #dc2626;
        }

        .search-container {
            margin-bottom: 20px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 16px;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #8b5cf6;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .no-data {
            text-align: center;
            padding: 48px;
            color: #64748b;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #8b5cf6;
        }

        .distribution-info {
            display: flex;
            gap: 16px;
            font-size: 12px;
        }

        .distribution-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .distribution-label {
            color: #64748b;
        }

        .distribution-value {
            font-weight: 600;
            color: #374151;
        }

        .card-content {
            padding: 16px;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
        }

        .collapsed .toggle-icon {
            transform: rotate(-90deg);
        }
    </style>

    <div class="monitoring-container">
        <h2 style="margin-bottom: 20px; color: #1e293b; font-size: 24px; font-weight: 700;">
            👥 Member Settings Monitoring
        </h2>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $totalClients ?? 0 }}</div>
                <div class="stat-label">Total Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalMembers ?? 0 }}</div>
                <div class="stat-label">Total Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $activeMembers ?? 0 }}</div>
                <div class="stat-label">Active Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalDistributedItems ?? 0 }}</div>
                <div class="stat-label">Distributed Items</div>
            </div>
        </div>

        <!-- Search -->
        <div class="search-container">
            <span class="search-icon">🔍</span>
            <input type="text" id="memberSearch" class="search-input" placeholder="Search members by name, email, or client name..." oninput="filterMembers()">
        </div>

        @if(isset($clientsWithMembers) && $clientsWithMembers->count() > 0)
            @foreach($clientsWithMembers as $client)
                <div class="client-section" data-client-name="{{ strtolower($client->name ?? '') }}" data-client-email="{{ strtolower($client->email ?? '') }}">
                    <div class="client-header">
                        <div class="client-info">
                            <div class="client-avatar">
                                {{ strtoupper(substr($client->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 16px;">
                                    {{ $client->name ?? 'Unknown Client' }}
                                    <span style="font-size: 12px; opacity: 0.8;">({{ $client->email ?? 'No email' }})</span>
                                </div>
                                <div style="font-size: 12px; opacity: 0.8;">
                                    Office: {{ $client->office ?? 'Not specified' }} | 
                                    Members: {{ $client->members_count ?? 0 }}
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 12px; opacity: 0.8;">Total Distributed</div>
                            <div style="font-size: 18px; font-weight: bold;">
                                {{ $client->total_distributed_items ?? 0 }}
                            </div>
                        </div>
                    </div>

                    @if(isset($client->members) && $client->members->count() > 0)
                        <div class="settings-card">
                            <div class="card-header" onclick="toggleCard('members-{{ $client->id }}')">
                                <h3>
                                    <span>👥</span>
                                    Members List
                                </h3>
                                <span class="toggle-icon">▼</span>
                            </div>
                            <div id="members-{{ $client->id }}" class="card-content">
                                <table class="members-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Distributed Items</th>
                                            <th>Available Items</th>
                                            <th>Used Items</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($client->members as $member)
                                            <tr data-member-name="{{ strtolower($member->name ?? '') }}" data-member-email="{{ strtolower($member->email ?? '') }}">
                                                <td style="font-weight: 600; color: #1e40af;">
                                                    {{ $member->name ?? 'Unknown Member' }}
                                                </td>
                                                <td>{{ $member->email ?? 'No email' }}</td>
                                                <td style="text-align: center; font-weight: 600;">
                                                    {{ $member->distributed_items ?? 0 }}
                                                </td>
                                                <td style="text-align: center; font-weight: 600; color: #059669;">
                                                    {{ $member->available_items ?? 0 }}
                                                </td>
                                                <td style="text-align: center; font-weight: 600; color: #d97706;">
                                                    {{ $member->used_items ?? 0 }}
                                                </td>
                                                <td style="text-align: center;">
                                                    <span class="member-badge {{ ($member->available_items ?? 0) > 0 ? 'member-active' : 'member-inactive' }}">
                                                        {{ ($member->available_items ?? 0) > 0 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div style="padding: 20px; text-align: center; color: #64748b; background: #f8fafc;">
                            No members found for this client.
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="no-data">
                <div style="font-size: 48px; margin-bottom: 16px;">👥</div>
                <div style="font-size: 18px; font-weight: 600; color: #7c3aed;">No member data available.</div>
                <div style="font-size: 14px; margin-top: 8px;">Client members will appear here once they are added to the system.</div>
            </div>
        @endif
    </div>

    <script>
        function toggleCard(cardId) {
            const card = document.getElementById(cardId);
            const header = card.previousElementSibling;
            
            if (card.style.display === 'none') {
                card.style.display = 'block';
                header.classList.remove('collapsed');
            } else {
                card.style.display = 'none';
                header.classList.add('collapsed');
            }
        }

        function filterMembers() {
            const searchTerm = document.getElementById('memberSearch').value.toLowerCase();
            const clientSections = document.querySelectorAll('.client-section');
            let visibleCount = 0;

            clientSections.forEach(section => {
                const clientName = section.dataset.clientName || '';
                const clientEmail = section.dataset.clientEmail || '';
                const memberNames = Array.from(section.querySelectorAll('tr[data-member-name]'))
                    .map(row => row.dataset.memberName || '')
                    .join(' ');
                const memberEmails = Array.from(section.querySelectorAll('tr[data-member-email]'))
                    .map(row => row.dataset.memberEmail || '')
                    .join(' ');

                const matches = clientName.includes(searchTerm) ||
                               clientEmail.includes(searchTerm) ||
                               memberNames.includes(searchTerm) ||
                               memberEmails.includes(searchTerm);

                if (matches || searchTerm === '') {
                    section.style.display = 'block';
                    visibleCount++;
                } else {
                    section.style.display = 'none';
                }
            });

            // Auto-expand cards with matching members
            if (searchTerm) {
                clientSections.forEach(section => {
                    if (section.style.display === 'block') {
                        const cardContent = section.querySelector('.card-content');
                        if (cardContent) {
                            cardContent.style.display = 'block';
                            const header = cardContent.previousElementSibling;
                            if (header) {
                                header.classList.remove('collapsed');
                            }
                        }
                    }
                });
            }
        }

        // Auto-refresh every 5 minutes to get latest member data
        setInterval(() => {
            location.reload();
        }, 300000);
    </script>
@endsection
